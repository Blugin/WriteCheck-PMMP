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

namespace kim\present\writecheck;

use kim\present\writecheck\lang\PluginLang;
use kim\present\writecheck\listener\PlayerEventListener;
use kim\present\writecheck\task\CheckUpdateAsyncTask;
use kim\present\writecheck\util\CheckManager;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\{
	Command, CommandSender, PluginCommand
};
use pocketmine\permission\{
	Permission, PermissionManager
};
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class WriteCheck extends PluginBase{
	/** @var WriteCheck */
	private static $instance = null;

	/**
	 * @return WriteCheck
	 */
	public static function getInstance() : WriteCheck{
		return self::$instance;
	}

	/** @var PluginLang */
	private $language;

	/** @var PluginCommand */
	private $command;

	/**
	 * Called when the plugin is loaded, before calling onEnable()
	 */
	public function onLoad() : void{
		self::$instance = $this;
	}

	/**
	 * Called when the plugin is enabled
	 */
	public function onEnable() : void{
		//Save default resources
		$this->saveResource("lang/eng/lang.ini", false);
		$this->saveResource("lang/kor/lang.ini", false);
		$this->saveResource("lang/language.list", false);

		//Load config file
		$this->saveDefaultConfig();
		$this->reloadConfig();
		$config = $this->getConfig();

		//Check latest version
		if($config->getNested("settings.update-check", false)){
			$this->getServer()->getAsyncPool()->submitTask(new CheckUpdateAsyncTask());
		}

		//Load language file
		$this->language = new PluginLang($this, $config->getNested("settings.language"));
		$this->getLogger()->info($this->language->translate("language.selected", [$this->language->getName(), $this->language->getLang()]));

		//Register main command
		$this->command = new PluginCommand($config->getNested("command.name"), $this);
		$this->command->setPermission("wcheck.cmd");
		$this->command->setAliases($config->getNested("command.aliases"));
		$this->command->setUsage($this->language->translate("commands.wcheck.usage"));
		$this->command->setDescription($this->language->translate("commands.wcheck.description"));
		$this->getServer()->getCommandMap()->register($this->getName(), $this->command);

		//Load permission's default value from config
		$permissions = PermissionManager::getInstance()->getPermissions();
		$defaultValue = $config->getNested("permission.main");
		if($defaultValue !== null){
			$permissions["wcheck.cmd"]->setDefault(Permission::getByName($config->getNested("permission.main")));
		}

		//Register event listeners
		$this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener($this), $this);
	}

	/**
	 * @param CommandSender $sender
	 * @param Command       $command
	 * @param string        $label
	 * @param string[]      $args
	 *
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(isset($args[0]) && is_numeric($args[0])){
			if($sender instanceof Player){
				$amount = (int) $args[0];
				$count = max(isset($args[1]) && is_numeric($args[1]) ? (int) $args[1] : 1, 1);

				$economyApi = EconomyAPI::getInstance();
				$price = $amount * $count;
				if(($money = $economyApi->myMoney($sender)) < $price){
					$sender->sendMessage($this->getLanguage()->translate("commands.wcheck.failure", [(string) $money]));
				}else{
					$return = $economyApi->reduceMoney($sender, $price, false, $this->getName());
					if($return === EconomyAPI::RET_SUCCESS){
						$sender->getInventory()->addItem(CheckManager::makeCheck($amount, $count));
						$sender->sendMessage($this->getLanguage()->translate("commands.wcheck.success", [(string) $amount, (string) $count, (string) $price, (string) ($money - $price)]));
					}else{
						$sender->sendMessage($this->getLanguage()->translate("economyFailure", [(string) $return]));
					}
				}
			}else{
				$sender->sendMessage($this->getLanguage()->translate("commands.generic.onlyPlayer"));
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @Override for multilingual support of the config file
	 *
	 * @return bool
	 */
	public function saveDefaultConfig() : bool{
		$resource = $this->getResource("lang/{$this->getServer()->getLanguage()->getLang()}/config.yml");
		if($resource === null){
			$resource = $this->getResource("lang/" . PluginLang::FALLBACK_LANGUAGE . "/config.yml");
		}

		if(!file_exists($configFile = $this->getDataFolder() . "config.yml")){
			$ret = stream_copy_to_stream($resource, $fp = fopen($configFile, "wb")) > 0;
			fclose($fp);
			fclose($resource);
			return $ret;
		}
		return false;
	}

	/**
	 * @return PluginLang
	 */
	public function getLanguage() : PluginLang{
		return $this->language;
	}
}
