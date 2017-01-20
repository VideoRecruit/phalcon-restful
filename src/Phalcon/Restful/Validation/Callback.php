<?php

namespace VideoRecruit\Phalcon\Restful\Validation;

use Phalcon\Validation;
use Phalcon\Validation\Exception as ValidationException;

/**
 * Class Callback
 *
 * @package VideoRecruit\Phalcon\Restful\Validation
 */
class Callback extends Validation\Validator
{
	const MESSAGE = 'The value of the field :field is invalid';
	const TYPE = 'Callback';

	/**
	 * @param Validation $validation
	 * @param string $field
	 * @return bool
	 * @throws ValidationException
	 */
	public function validate(Validation $validation, $field)
	{
		if (!is_string($field)) {
			throw new ValidationException('Field name must be a string.');
		}

		$label = $validation->getLabel($field);
		$callback = $this->getOption('callback');
		$replacePairs = [':field' => $label];

		if (!is_callable($callback)) {
			throw new ValidationException('Field must be a valid callback.');
		}

		// validate callback result
		$result = $callback($validation->getValue($field));

		if (!is_bool($result)) {
			throw new ValidationException('Callback validator has to return boolean value.');
		} elseif ($result === FALSE) {
			$message = $this->getOption('message', self::MESSAGE);

			$msg = new Validation\Message(strtr($message, $replacePairs), $field, self::TYPE);
			$validation->appendMessage($msg);

			return FALSE;
		}

		return TRUE;
	}
}
