<?php

namespace VideoRecruit\Phalcon\Restful\Validation;

use Phalcon\Validation;
use Phalcon\Validation\Exception as ValidationException;

/**
 * Class Uuid
 *
 * @package VideoRecruit\Phalcon\Restful\Validation
 */
class Uuid extends Validation\Validator
{

	const MESSAGE = 'Field :field must be a valid UUID';
	const MESSAGE_VERSIONS = 'Field :field must be one of the following UUID versions: :versions';
	const TYPE = 'Uuid';

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

		if (!preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[1-5][A-Z0-9]{3}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $value)) {
			$message = $this->getOption('message', self::MESSAGE);

			$msg = new Validation\Message(strtr($message, $replacePairs), $field, self::TYPE);
			$validation->appendMessage($msg);

			return FALSE;
		}

		$allowedVersions = $this->getOption('allowedVersions', [1, 2, 3, 4, 5]);
		$replacePairs[':versions'] = implode(', ', $allowedVersions);

		if (!in_array((int) $value[14], $allowedVersions, TRUE)) {
			$msg = new Validation\Message(strtr(self::MESSAGE_VERSIONS, $replacePairs), $field, self::TYPE);
			$validation->appendMessage($msg);

			return FALSE;
		}

		return TRUE;
	}
}
