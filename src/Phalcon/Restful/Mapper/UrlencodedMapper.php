<?php

namespace VideoRecruit\Phalcon\Restful\Mapper;

/**
 * Class UrlencodedMapper
 *
 * @package VideoRecruit\Phalcon\Restful\Mapper
 */
class UrlencodedMapper implements IMapper
{

	/**
	 * @param string $data
	 * @return array
	 */
	public function parse($data)
	{
		$values = [];

		parse_str($data, $values);

		return $values;
	}
}
