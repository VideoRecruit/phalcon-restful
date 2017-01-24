<?php

namespace VideoRecruit\Phalcon\Restful\Validation;

use Phalcon\Validation;
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
	const FILE = 'file';

	/**
	 * @var Field[]
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
	 * @param Rule $rule
	 * @return self
	 */
	public function addRule(Field $field, Rule $rule)
	{
		$this->validation->add($field->getName(), $rule->getValidator());

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
}
