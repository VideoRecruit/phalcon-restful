<?php

namespace VideoRecruit\Phalcon\Restful\DI;

use Phalcon\DiInterface;
use Phalcon\Events\Manager as EventManager;
use Phalcon\Mvc\Dispatcher;
use VideoRecruit\Phalcon\Restful;

/**
 * Class RestfulExtension
 *
 * @package VideoRecruit\Phalcon\Restful\DI
 */
class RestfulExtension
{
	const INPUT = 'videorecruit.phalcon.restful.input';
	const MAPPER_FACTORY = 'videorecruit.phalcon.restful.mapperFactory';
	const VALIDATOR = 'videorecruit.phalcon.restful.validator';

	const REQUEST = 'request';
	const DISPATCHER = 'dispatcher';
	const EVENTS_MANAGER = 'eventsManager';

	/**
	 * @var DiInterface
	 */
	private $di;

	/**
	 * RestfulExtension constructor.
	 *
	 * @param DiInterface $di
	 */
	public function __construct(DiInterface $di)
	{
		$this->di = $di;

		$this->di->setShared(self::INPUT, function () {
			return new Restful\Input(
				$this->get(self::REQUEST),
				$this->get(self::MAPPER_FACTORY),
				$this->get(self::VALIDATOR)
			);
		});

		$this->di->setShared(self::MAPPER_FACTORY, function () {
			return new Restful\Mapper\MapperFactory();
		});

		$this->di->setShared(self::VALIDATOR, function () {
			return new Restful\Validation\Validator();
		});

		/**
		 * @var Dispatcher $dispatcher
		 * @var EventManager $events
		 */
		$dispatcher = $this->di->get(self::DISPATCHER);
		$events = $dispatcher->getEventsManager() ?: $this->di->get(self::EVENTS_MANAGER);

		$events->attach('dispatch:beforeDispatch', function ($event, Dispatcher $dispatcher) {
			$controllerClassName = $dispatcher->getControllerClass();
			$actionName = 'validate' . ucfirst($dispatcher->getActionName());

			try {
				$reflection = new \ReflectionMethod($controllerClassName, $actionName . $dispatcher->getActionSuffix());

				/** @var Restful\Input $input */
				$input = $this->di->get(RestfulExtension::INPUT);

				$dispatcher->setActionName($actionName);
				$dispatcher->setParam(0, $input);
				$dispatcher->dispatch();

				try {
					$input->validate();
				} catch (Restful\ValidationException $e) {
					$eventsManager = $dispatcher->getEventsManager();
					$eventsManager->fire('dispatch:beforeException', $dispatcher, $e);

					$availableListeners = $eventsManager->getListeners('dispatch:beforeException');

					if (count($availableListeners) === 0) {
						throw $e;
					}

					return FALSE;
				}
			} catch (\ReflectionException $e) {}
		});

		$dispatcher->setEventsManager($events);
	}

	/**
	 * Register restful services into Phalcon's di container.
	 *
	 * @param DiInterface $di
	 * @return self
	 */
	public static function register(DiInterface $di)
	{
		return new self($di);
	}
}
