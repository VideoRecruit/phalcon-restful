<?php

namespace VideoRecruit\Phalcon\Restful;

use Phalcon\Http\Request;

/**
 * Class InputValidator
 *
 * @package VideoRecruit\Phalcon\Restful
 */
class Input
{

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Mapper\MapperFactory
	 */
	private $mapperFactory;

	/**
	 * @var Validation\Validator
	 */
	private $validator;

	/**
	 * @var array
	 */
	private $data = [];

	/**
	 * Input constructor.
	 *
	 * @param Request $request
	 * @param Mapper\MapperFactory $mapperFactory
	 * @param Validation\Validator $validator
	 */
	public function __construct(Request $request, Mapper\MapperFactory $mapperFactory, Validation\Validator $validator)
	{
		$this->request = $request;
		$this->mapperFactory = $mapperFactory;
		$this->validator = $validator;

		$this->data = $this->parseData();
	}

	/**
	 * @param string $name
	 * @return Validation\Field
	 */
	public function field($name)
	{
		return $this->validator->field($name);
	}

	/**
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($name, $defaultValue = NULL)
	{
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}

		return $defaultValue;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @return void
	 * @throws ValidationException
	 */
	public function validate()
	{
		$this->validator->validate($this->getData());
	}

	/**
	 * @return bool
	 */
	public function isValid()
	{
		try {
			$this->validate();
			return TRUE;
		} catch (ValidationException $e) {}

		return FALSE;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->get($name);
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return array_key_exists($name, $this->data);
	}

	/**
	 * @return array
	 */
	private function parseData()
	{
		return array_merge($_GET, $_POST, $_FILES, $this->parseRawData());
	}

	/**
	 * @return array
	 */
	private function parseRawData()
	{
		$body = trim($this->request->getRawBody());
		$contentType = $this->request->getHeader('Content-Type');

		if (empty($body) || empty($contentType)) {
			return [];
		}

		$mapper = $this->mapperFactory->getMapper($contentType);
		$result = $mapper->parse($body);

		if (!is_array($result)) {
			throw new UnexpectedValueException(sprintf('Invalid value returned by a mapper: %s. Array value was expected.', gettype($result)));
		}

		return $result;
	}
}
