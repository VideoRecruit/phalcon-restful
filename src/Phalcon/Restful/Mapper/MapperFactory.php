<?php

namespace VideoRecruit\Phalcon\Restful\Mapper;

/**
 * Class MapperFactory
 *
 * @package VideoRecruit\Phalcon\Restful\Mapper
 */
class MapperFactory
{
	const JSON = 'application/json';
	const URLENCODED = 'application/x-www-form-urlencoded';

	/**
	 * @var array
	 */
	private $mappers = [];

	/**
	 * MapperFactory constructor.
	 */
	public function __construct()
	{
		$this->mappers[self::JSON] = new JsonMapper();
		$this->mappers[self::URLENCODED] = new UrlencodedMapper();
	}

	/**
	 * @param string $contentType
	 * @param IMapper $mapper
	 * @return self
	 */
	public function registerMapper($contentType, $mapper)
	{
		$this->mappers[$contentType] = $mapper;

		return $this;
	}

	/**
	 * @param string $contentType
	 * @return IMapper
	 */
	public function getMapper($contentType)
	{
		$contentType = explode(';', trim($contentType))[0];

		return $this->mappers[trim($contentType)];
	}
}