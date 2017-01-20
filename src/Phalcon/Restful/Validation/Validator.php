<?php

namespace VideoRecruit\Phalcon\Restful\Validation;

use Phalcon\Validation;
use VideoRecruit\Phalcon\Restful\InvalidArgumentException;
use VideoRecruit\Phalcon\Restful\ValidationException;

/**
 * Class Validator
 *
 * @package VideoRecruit\Phalcon\Restful\Validation
 */
class Validator
{
	const REQUIRED = 'required';
	const EMAIL = 'email';
	const URL = 'url';
	const EQUAL = 'equal';
	const CREDIT_CARD = 'creditCard';
	const LENGTH = 'length';
	const MIN_LENGTH = 'minLength';
	const MAX_LENGTH = 'maxLength';
	const INTEGER = 'integer';
	const RANGE = 'range';
	const DATE = 'date';
	const PATTERN = 'pattern';
	const UUID = 'uuid';
	const FLOAT = 'float';
	const CALLBACK = 'callback';

	/**
	 * @var array
	 */
	private static $availableRules = [
		self::REQUIRED => 'Phalcon\Validation\Validator\PresenceOf',
		self::EMAIL => 'Phalcon\Validation\Validator\Email',
		self::URL => 'Phalcon\Validation\Validator\Url',
		self::EQUAL => 'Phalcon\Validation\Validator\Identical',
		self::CREDIT_CARD => 'Phalcon\Validation\Validator\CreditCard',
		self::LENGTH => 'Phalcon\Validation\Validator\StringLength',
		self::MIN_LENGTH => 'Phalcon\Validation\Validator\StringLength',
		self::MAX_LENGTH => 'Phalcon\Validation\Validator\StringLength',
		self::INTEGER => 'Phalcon\Validation\Validator\Numericality',
		self::RANGE => 'Phalcon\Validation\Validator\Between',
		self::DATE => 'Phalcon\Validation\Validator\Date',
		self::PATTERN => 'Phalcon\Validation\Validator\Regex',
		self::UUID => 'VideoRecruit\Phalcon\Restful\Validation\Uuid',
		self::FLOAT => 'VideoRecruit\Phalcon\Restful\Validation\FloatNumber',
		self::CALLBACK => 'VideoRecruit\Phalcon\Restful\Validation\Callback',
	];

	/**
	 * @var array
	 */
	private static $messages = [
		self::PATTERN => 'Field date does not match the required format %s',
		self::UUID => 'Field must be a valid UUID',
		self::FLOAT => 'Field must be a valid float number',
	];

	/**
	 * @var array
	 */
	private $fields = [];

	/**
	 * @var Validation
	 */
	private $validation;

	/**
	 * Validator constructor.
	 */
	public function __construct()
	{
		$this->validation = new Validation;
	}

	/**
	 * @param string $name
	 * @return Field
	 */
	public function field($name)
	{
		if (!array_key_exists($name, $this->fields)) {
			$this->fields[$name] = new Field($this, $name);
		}

		return $this->fields[$name];
	}

	/**
	 * @param Field $field
	 * @param string $expression
	 * @param string $message
	 * @param mixed $argument
	 * @return self
	 */
	public function addRule(Field $field, $expression, $message = NULL, $argument = NULL)
	{
		$this->validation->add($field->getName(), $this->getRule($expression, $message, $argument));
		return $this;
	}

	/**
	 * @param array $data
	 * @return void
	 * @throws ValidationException
	 */
	public function validate(array $data)
	{
		$errors = $this->validation->validate($data);

		if ($errors->count() !== 0) {
			throw new ValidationException($errors);
		}
	}

	/**
	 * @param string $expression
	 * @param string $message
	 * @param mixed $argument
	 * @return Validation\ValidatorInterface
	 * @throws InvalidArgumentException
	 */
	private function getRule($expression, $message = NULL, $argument = NULL)
	{
		if (!array_key_exists($expression, self::$availableRules)) {
			throw new InvalidArgumentException(sprintf('Undefined rule %s. This one does not seem to exist.', $expression));
		}

		if ($message === NULL && array_key_exists($expression, self::$messages)) {
			$message = self::$messages[$expression];
		}

		$options = $this->getRuleOptions($expression, $argument);

		if ($message) {
			$options['message'] = $message;
		}

		return new self::$availableRules[$expression]($options);
	}

	/**
	 * @param string $expression
	 * @param mixed $argument
	 * @return array
	 */
	private function getRuleOptions($expression, $argument)
	{
		$options = [];

		switch ($expression) {
			case self::MIN_LENGTH:
				$options['min'] = $argument;
				break;

			case self::MAX_LENGTH:
				$options['max'] = $argument;
				break;

			case self::LENGTH:
				list ($min, $max) = $argument;

				$options['min'] = $min;
				$options['max'] = $max;
				break;

			case self::RANGE:
				list ($min, $max) = $argument;

				$options['minimum'] = $min;
				$options['maximum'] = $max;
				break;

			case self::DATE:
				$options['format'] = $argument;
				break;

			case self::PATTERN:
				if ($pattern = $argument) {
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

			case self::CALLBACK:
				$options['callback'] = $argument;
				break;
		}

		return $options;
	}
}
