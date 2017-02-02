<?php

namespace VideoRecruit\Phalcon\Restful\Mapper;

/**
 * Class MultipartMapper
 *
 * @package VideoRecruit\Phalcon\Restful\Mapper
 */
class MultipartMapper implements IMapper
{

	/**
	 * @param string $data
	 * @return array
	 */
	public function parse($data)
	{
		// Fetch content and determine boundary
		$boundary = substr($data, 0, strpos($data, "\r\n"));

		// Fetch each part
		$parts = array_slice(explode($boundary, $data), 1);
		$output = [];

		foreach ($parts as $part) {

			$part = trim($part, "\r\n");

			// If this is the last part, break
			if ($part === '--') {
				break;
			}

			// Separate content from headers
			$part = trim($part, "\r\n");
			list ($rawHeaders, $body) = explode("\r\n\r\n", $part, 2);

			// Parse the headers list
			$rawHeaders = explode("\r\n", $rawHeaders);
			$headers = [];

			foreach ($rawHeaders as $header) {
				list ($name, $value) = explode(':', $header);
				$headers[strtolower(trim($name))] = trim($value);
			}

			// Parse the Content-Disposition to get the field name, etc.
			if (array_key_exists('content-disposition', $headers)) {
				preg_match(
					'/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
					$headers['content-disposition'],
					$matches
				);

				list(,, $name) = $matches;
				$output[$name] = $body;
			}
		}

		return $output;
	}
}
