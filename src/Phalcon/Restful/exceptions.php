<?php

namespace VideoRecruit\Phalcon\Restful;

use Exception;
use Phalcon\Validation\Message;
use Phalcon\Validation\Message\Group;


interface IException
{

}



/**
 * The exception that is thrown when the value of an argument is
 * outside the allowable range of values as defined by the invoked method.
 */
class ArgumentOutOfRangeException extends \InvalidArgumentException implements IException
{

}



/**
 * The exception that is thrown when a method call is invalid for the object's
 * current state, method has been invoked at an illegal or inappropriate time.
 */
class InvalidStateException extends \RuntimeException implements IException
{

}



/**
 * The exception that is thrown when a requested method or operation is not implemented.
 */
class NotImplementedException extends \LogicException implements IException
{

}



/**
 * The exception that is thrown when an invoked method is not supported. For scenarios where
 * it is sometimes possible to perform the requested operation, see InvalidStateException.
 */
class NotSupportedException extends \LogicException implements IException
{

}



/**
 * The exception that is thrown when an argument does not match with the expected value.
 */
class InvalidArgumentException extends \InvalidArgumentException implements IException
{

}



/**
 * The exception that is thrown when an illegal index was requested.
 */
class OutOfRangeException extends \OutOfRangeException implements IException
{

}



/**
 * The exception that is thrown when a value (typically returned by function) does not match with the expected value.
 */
class UnexpectedValueException extends \UnexpectedValueException implements IException
{

}



/**
 * The exception is thrown when
 */
class ValidationException extends \LogicException implements IException
{

	/**
	 * @var Group
	 */
	private $errors;

	/**
	 * ValidationException constructor.
	 *
	 * @param Group $errors
	 * @param string $message
	 * @param int $code
	 * @param Exception|NULL $previous
	 */
	public function __construct(Group $errors, $message = 'Validation failed.', $code = 0, Exception $previous = NULL)
	{
		parent::__construct($message, $code, $previous);
		$this->errors = $errors;
	}

	/**
	 * @return Message[]
	 */
	public function getErrors()
	{
		return iterator_to_array($this->errors);
	}
}
