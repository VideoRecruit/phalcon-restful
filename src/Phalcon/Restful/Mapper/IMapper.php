<?php

namespace VideoRecruit\Phalcon\Restful\Mapper;

/**
 * Interface IMapper
 *
 * @package VideoRecruit\Phalcon\Restful\Mapper
 */
interface IMapper
{

	/**
	 * @param string $data
	 * @return array
	 */
	function parse($data);
}
