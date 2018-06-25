<?php

declare(strict_types=1);

namespace kim\present\writecheck\util;

class Utils{
	/**
	 * Generate the associative array for use strstr() method
	 *
	 * @param string[] $list
	 *
	 * @return string[]
	 */
	public static function listToPairs(array $list) : array{
		$pairs = [];
		$size = sizeOf($list);
		for($i = 0; $i < $size; ++$i){
			$pairs["{%$i}"] = $list[$i];
		}
		return $pairs;
	}
}