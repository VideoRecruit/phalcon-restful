<?php

namespace VideoRecruit\Phalcon\Restful\Validation;

use Phalcon\Validation;

/**
 * Class Field
 *
 * @package VideoRecruit\Phalcon\Restful\Validation
 */
class Field
{

	/**
	 * @var Validator
	 */
	private $validator;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @var bool
	 */
	private $required = FALSE;

	/**
	 * @var Rule[]
	 */
	private $rules = [];

	/**
	 * Field constructor.
	 *
	 * @param Validator $validator
	 * @param string $name
	 * @param mixed $value
	 */
	public function __construct(Validator $validator, $name, $value)
	{
		$this->validator = $validator;
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return boolean
	 */
	public function isRequired()
	{
		return $this->required;
	}

	/**
	 * @param string $expression
	 * @param string $message
	 * @param mixed $argument
	 * @return self
	 */
	public function addRule($expression, $message = NULL, $argument = NULL)
	{
		if ($expression === Validator::REQUIRED) {
			$this->required = TRUE;
		} else {
			$this->rules[] = new Rule($this, $expression, $message, $argument);
		}

		return $this;
	}

	/**
	 * @return Rule[]
	 */
	public function getRules()
	{
		return $this->rules;
	}

	/**
	 * Validate field over defined rules.
	 *
	 * @return Validation\Message[]
	 */
	public function validate()
	{
		$errors = [];

		if ($this->required && $this->value === NULL) {
			$replacePairs = [':field' => $this->name];
			$errors[] = new Validation\Message(strtr('Field :field is required', $replacePairs), $this->name, 'Required');
		}

		$validation = new Validation();
		foreach ($this->rules as $rule) {
			$validation->add($this->name, $rule->getValidator());
		}

		$fieldErrors = iterator_to_array($validation->validate([$this->name => $this->value]));
		if (count($fieldErrors)) {
			array_push($errors, ...$fieldErrors);
		}

		return $errors;
	}
}
