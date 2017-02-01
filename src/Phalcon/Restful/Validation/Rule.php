<?php

namespace VideoRecruit\Phalcon\Restful\Validation;

use Phalcon\Validation\ValidatorInterface;
use VideoRecruit\Phalcon\Restful\InvalidArgumentException;

/**
 * Class Rule
 *
 * @package VideoRecruit\Phalcon\Restful\Validation
 */
class Rule
{
	const SIZE = 'maxSize';
	const MIME_TYPE = 'allowedTypes';

	/**
	 * @var array
	 */
	private static $defaultMessages = [
		Validator::UUID => 'Field :field must be a valid UUID',
		Validator::FLOAT => 'Field :field must be a valid float number',
	];

	/**
	 * @var array
	 */
	private static $validators = [
		Validator::REQUIRED => 'Phalcon\Validation\Validator\PresenceOf',
		Validator::EMAIL => 'Phalcon\Validation\Validator\Email',
		Validator::URL => 'Phalcon\Validation\Validator\Url',
		Validator::EQUAL => 'Phalcon\Validation\Validator\Identical',
		Validator::CREDIT_CARD => 'Phalcon\Validation\Validator\CreditCard',
		Validator::LENGTH => 'Phalcon\Validation\Validator\StringLength',
		Validator::MIN_LENGTH => 'Phalcon\Validation\Validator\StringLength',
		Validator::MAX_LENGTH => 'Phalcon\Validation\Validator\StringLength',
		Validator::INTEGER => 'Phalcon\Validation\Validator\Numericality',
		Validator::RANGE => 'Phalcon\Validation\Validator\Between',
		Validator::DATE => 'Phalcon\Validation\Validator\Date',
		Validator::PATTERN => 'Phalcon\Validation\Validator\Regex',
		Validator::UUID => 'VideoRecruit\Phalcon\Restful\Validation\Uuid',
		Validator::FLOAT => 'VideoRecruit\Phalcon\Restful\Validation\FloatNumber',
		Validator::CALLBACK => 'VideoRecruit\Phalcon\Restful\Validation\Callback',
		Validator::FILE => 'Phalcon\Validation\Validator\File',
		Validator::IN => 'Phalcon\Validation\Validator\InclusionIn',
	];

	/**
	 * @var Field
	 */
	private $field;

	/**
	 * @var string
	 */
	private $expression;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var mixed
	 */
	private $argument;

	/**
	 * Rule constructor.
	 *
	 * @param Field $field
	 * @param string $expression
	 * @param string $message
	 * @param mixed $argument
	 * @throws InvalidArgumentException
	 */
	public function __construct(Field $field, $expression, $message = NULL, $argument = NULL)
	{
		if (!array_key_exists($expression, self::$validators)) {
			throw new InvalidArgumentException(sprintf('Undefined rule %s. This one does not seem to exist.', $expression));
		}

		if ($message === NULL && array_key_exists($expression, self::$defaultMessages)) {
			$message = self::$defaultMessages[$expression];
		}

		$this->field = $field;
		$this->expression = $expression;
		$this->message = $message;
		$this->argument = $argument;
	}

	/**
	 * @return ValidatorInterface
	 */
	public function getValidator()
	{
		$options = $this->getOptions();

		if ($this->message) {
			$replacePairs = [':field' => $this->field->getName(),];

			foreach ($options as $key => $value) {
				if (is_numeric($value) || is_string($value)) {
					$replacePairs[":$key"] = $value;
				}
			}

			$options['message'] = strtr($this->message, $replacePairs);
		}

		return new self::$validators[$this->expression]($options);
	}

	/**
	 * @return array
	 */
	private function getOptions()
	{
		$options = [
			'allowEmpty' => !$this->field->isRequired() && $this->field->getValue() === NULL,
		];

		switch ($this->expression) {
			case Validator::EQUAL:
				$options['value'] = $this->argument;
				break;

			case Validator::MIN_LENGTH:
				$options['min'] = $this->argument;
				break;

			case Validator::MAX_LENGTH:
				$options['max'] = $this->argument;
				break;

			case Validator::LENGTH:
				list ($min, $max) = $this->argument;

				$options['min'] = $min;
				$options['max'] = $max;
				break;

			case Validator::RANGE:
				list ($min, $max) = $this->argument;

				$options['minimum'] = $min;
				$options['maximum'] = $max;
				break;

			case Validator::DATE:
				$options['format'] = $this->argument;
				break;

			case Validator::PATTERN:
				if ($pattern = $this->argument) {
					$pattern = ltrim(rtrim($pattern, '$'), '^');

					if (strpos($pattern, '/^') === 0) {
						$pattern = substr($pattern, 2);
					}

					if (strrpos($pattern, '$/') === strlen($pattern) - 2) {
						$pattern = substr($pattern, 0, -2);
					}
				}

				$options['pattern'] = $pattern ? "/^{$pattern}$/" : NULL;
				break;

			case Validator::CALLBACK:
				$options['callback'] = $this->argument;
				break;

			case Validator::FILE:
				$options['messageEmpty'] = 'Field :field must be a file';

				if (is_array($this->argument)) {
					if (array_key_exists(self::SIZE, $this->argument)) {
						$options[self::SIZE] = $this->argument[self::SIZE];
					}

					if (array_key_exists(self::MIME_TYPE, $this->argument)) {
						$options[self::MIME_TYPE] = (array) $this->argument[self::MIME_TYPE];
					}
				}

				break;

			case Validator::IN:
				$options['domain'] = $this->argument;
				$options['strict'] = TRUE;
				break;
		}

		return $options;
	}
}
