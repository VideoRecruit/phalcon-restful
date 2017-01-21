<?php

namespace VideoRecruit\Phalcon\Restful\Validation;

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
	 * @var array
	 */
	private $rules = [];

	/**
	 * Field constructor.
	 *
	 * @param Validator $validator
	 * @param string $name
	 */
	public function __construct(Validator $validator, $name)
	{
		$this->validator = $validator;
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $expression
	 * @param string $message
	 * @param mixed $argument
	 * @return self
	 */
	public function addRule($expression, $message = NULL, $argument = NULL)
	{
		$this->rules[] = $rule = new Rule($this, $expression, $message, $argument);
		$this->validator->addRule($this, $rule);

		return $this;
	}

	/**
	 * @return Rule[]
	 */
	public function getRules()
	{
		return $this->rules;
	}
}
