<?php

namespace VideoRecruit\Phalcon\Restful;

use Phalcon\DiInterface;

/**
 * Class TRestInput
 *
 * @package VideoRecruit\Phalcon\Restful
 */
trait TRestInput
{

	/**
	 * @return Input
	 */
	protected function getInput()
	{
		return $this->getDI()->get(DI\RestfulExtension::INPUT);
	}

	/**
	 * @return DiInterface
	 */
	abstract protected function getDI();
}
