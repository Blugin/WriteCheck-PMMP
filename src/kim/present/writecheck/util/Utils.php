<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://opensource.org/licenses/MIT MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

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