<?php

namespace VideoRecruit\Phalcon\Restful\Mapper;

/**
 * Class JsonMapper
 *
 * @package VideoRecruit\Phalcon\Restful\Mapper
 */
class JsonMapper implements IMapper
{

	/**
	 * @param string $data
	 * @return array
	 */
	public function parse($data)
	{
		return json_decode($data, TRUE);
	}
}
