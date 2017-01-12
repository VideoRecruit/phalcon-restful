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
		$this->validator->addRule($this, $expression, $message, $argument);
		return $this;
	}
}