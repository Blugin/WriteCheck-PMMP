<?php

declare(strict_types=1);

namespace kim\present\writecheck\lang;

use kim\present\writecheck\WriteCheck;
use pocketmine\lang\BaseLang;

class PluginLang extends BaseLang{
	/**
	 * @var WriteCheck
	 */
	private $plugin;

	/**
	 * @noinspection PhpMissingParentConstructorInspection
	 * PluginLang constructor.
	 *
	 * @param WriteCheck $plugin
	 * @param string     $lang
	 */
	public function __construct(WriteCheck $plugin, string $lang){
		$this->langName = strtolower($lang);
		$this->plugin = $plugin;

		$this->load($lang);
		if(!self::loadLang($file = $plugin->getDataFolder() . "lang/" . self::FALLBACK_LANGUAGE . "/lang.ini", $this->fallbackLang)){
			$plugin->getLogger()->error("Missing required language file $file");
		}
	}

	/**
	 * @param string $lang
	 *
	 * @return bool
	 */
	public function load(string $lang) : bool{
		if($this->isAvailableLanguage($lang)){
			if(!self::loadLang($file = $this->plugin->getDataFolder() . "lang/" . $this->langName . "/lang.ini", $this->lang)){
				$this->plugin->getLogger()->error("Missing required language file $file");
			}else{
				return true;
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
}