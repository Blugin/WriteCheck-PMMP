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
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0.0
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\writecheck\lang;

use pocketmine\lang\{
	Language, LanguageNotFoundException
};
use pocketmine\plugin\PluginBase;

class PluginLang extends Language{
	/**
	 * @Override for support the array at lang file
	 *
	 * @param string $path
	 * @param string $languageCode
	 *
	 * @return array
	 */
	protected static function loadLang(string $path, string $languageCode) : array{
		$file = $path . $languageCode . ".ini";
		if(file_exists($file)){
			return array_map(function($entry){
				return is_string($entry) ? stripcslashes($entry) : $entry;
			}, parse_ini_file($file, false, INI_SCANNER_RAW));
		}
		throw new LanguageNotFoundException("Language \"$languageCode\" not found");
	}

	/** @var PluginBase */
	private $plugin;

	/**
	 * @noinspection PhpMissingParentConstructorInspection
	 * PluginLang constructor.
	 *
	 * @param PluginBase $plugin
	 * @param string     $lang
	 */
	public function __construct(PluginBase $plugin, string $lang){
		$this->langName = strtolower($lang);
		$this->plugin = $plugin;

		//Load required language
		$this->load($lang);

		//Load fallback language
		$resoruce = $plugin->getResource("lang/" . self::FALLBACK_LANGUAGE . "/lang.ini");
		if($resoruce !== null){
			$this->fallbackLang = parse_ini_string(stream_get_contents($resoruce), false, INI_SCANNER_RAW);
		}else{
			$plugin->getLogger()->error("Missing fallback language file");
		}
	}

	/**
	 * @param string $lang
	 *
	 * @return bool
	 */
	public function load(string $lang) : bool{
		if($this->isAvailableLanguage($lang)){
			try{
				$this->lang = self::loadLang($this->plugin->getDataFolder() . "lang/", "{$this->langName}/lang");
			}catch(LanguageNotFoundException $e){
				$this->plugin->getLogger()->error("Missing required language file ({$this->langName})");
			}
		}
		return false;
	}

	/**
	 * Read available language list from language.list file
	 *
	 * @return string[]
	 */
	public function getAvailableLanguageList() : array{
		return explode("\n", file_get_contents($this->plugin->getDataFolder() . "lang/language.list"));
	}

	/**
	 * @param string $lang
	 *
	 * @return bool
	 */
	public function isAvailableLanguage(string $lang) : bool{
		return in_array(strtolower($lang), $this->getAvailableLanguageList());
	}

	/**
	 * @param string $id
	 *
	 * @return string[]|null
	 */
	public function getArray(string $id) : ?array{
		$result = null;
		if(isset($this->lang[$id])){
			$result = $this->lang[$id];
		}elseif(isset($this->fallbackLang[$id])){
			$result = $this->fallbackLang[$id];
		}

		if(!is_array($result)){
			return null;
		}
		return $result;
	}
}