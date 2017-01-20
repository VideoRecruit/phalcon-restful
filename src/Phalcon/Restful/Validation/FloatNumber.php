<?php

namespace VideoRecruit\Phalcon\Restful\Validation;

use Phalcon\Validation;
use Phalcon\Validation\Exception as ValidationException;

/**
 * Class FloatNumber
 *
 * @package VideoRecruit\Phalcon\Restful\Validation
 */
class FloatNumber extends Validation\Validator
{

	const MESSAGE = 'Field :field must be a valid float number';
	const TYPE = 'Float';

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
		$value = $validation->getValue($field);
		$replacePairs = [':field' => $label];

		if (!preg_match('/^\d{1,7}(?:\.\d{1,2})?$/', $value)) {
			$message = $this->getOption('message', self::MESSAGE);

			$msg = new Validation\Message(strtr($message, $replacePairs), $field, self::TYPE);
			$validation->appendMessage($msg);

			return FALSE;
		}

		return TRUE;
	}
}
