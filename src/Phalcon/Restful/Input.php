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
		return $this->validator->field($name, $this->get($name));
	}

	/**
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($name, $defaultValue = NULL)
	{
		$data = $this->data;

		foreach ($fieldParts = explode('.', $name) as $fieldName) {
			if (!is_array($data) || !array_key_exists($fieldName, $data)) {
				return $defaultValue;
			}

			$data = $data[$fieldName];
		}

		return $data;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return self
	 */
	public function set($name, $value)
	{
		$data = &$this->data;
		$iterator = 1;

		$fieldParts = explode('.', $name);
		$levels = count($fieldParts);

		foreach ($fieldParts as $fieldName) {
			if ($iterator === $levels) {
				$data[$fieldName] = $value;
				break;
			}

			$data[$fieldName] = [];
			$data = &$data[$fieldName];
			$iterator++;
		}

		return $this;
	}

	/**
	 * Return all input data as an array.
	 * This optimizes data structure and removes null values which are optional.
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->optimizeData($this->data);
	}

	/**
	 * @param array $data
	 * @return self
	 */
	public function setData(array $data)
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * @return void
	 * @throws ValidationException
	 */
	public function validate()
	{
		$this->validator->validate();
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
	 * @return self
	 */
	public function __set($name, $value)
	{
		return $this->set($name, $value);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return $this->get($name) !== NULL;
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

	/**
	 * @param array $data
	 * @param string $path
	 * @return array
	 */
	private function optimizeData(array $data, $path = NULL)
	{
		foreach ($data as $key => &$value) {
			$path = $path ? "$path.$key" : "$key";

			if (is_array($value)) {
				$value = $this->optimizeData($value, $path);

				if (count($value) === 0) {
					unset($data[$key]);
				}

				continue;
			}

			if ($value === NULL && !$this->field($path)->isRequired()) {
				unset($data[$key]);
			}
		}

		return $data;
	}
}
