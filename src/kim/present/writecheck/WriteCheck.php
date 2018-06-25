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

namespace kim\present\writecheck;

use kim\present\writecheck\lang\PluginLang;
use kim\present\writecheck\listener\PlayerEventListener;
use kim\present\writecheck\util\Utils;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\{
	Command, CommandSender, PluginCommand
};
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class WriteCheck extends PluginBase{
	/**
	 * @var WriteCheck
	 */
	private static $instance = null;

	/**
	 * @return WriteCheck
	 */
	public static function getInstance() : WriteCheck{
		return self::$instance;
	}

	/**
	 * @var PluginLang
	 */
	private $language;

	/**
	 * @var PluginCommand
	 */
	private $command;

	public function onLoad() : void{
		self::$instance = $this;
	}

	public function onEnable() : void{
		//Save default resources
		$this->saveResource("lang/eng/lang.ini", false);
		$this->saveResource("lang/kor/lang.ini", false);
		$this->saveResource("lang/language.list", false);

		//Load config file
		$this->saveDefaultConfig();
		$this->reloadConfig();

		//Load language file
		$config = $this->getConfig();
		$this->language = new PluginLang($this, $config->getNested("settings.language"));
		$this->getLogger()->info($this->language->translateString("language.selected", [$this->language->getName(), $this->language->getLang()]));

		//Register main command
		$this->command = new PluginCommand($config->getNested("command.name"), $this);
		$this->command->setPermission("wcheck.cmd");
		$this->command->setAliases($config->getNested("command.aliases"));
		$this->command->setUsage($this->language->translateString("commands.wcheck.usage"));
		$this->command->setDescription($this->language->translateString("commands.wcheck.description"));
		$this->getServer()->getCommandMap()->register($this->getName(), $this->command);

		//Load permission's default value from config
		$permissions = $this->getServer()->getPluginManager()->getPermissions();
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
					$sender->sendMessage($this->getLanguage()->translateString("commands.wcheck.failure", [(string) $money]));
				}else{
					$return = $economyApi->reduceMoney($sender, $price, false, $this->getName());
					if($return === EconomyAPI::RET_SUCCESS){
						$sender->getInventory()->addItem($this->getCheck($amount, $count));
						$sender->sendMessage($this->getLanguage()->translateString("commands.wcheck.success", [(string) $amount, (string) $count, (string) $price, (string) ($money - $price)]));
					}else{
						$sender->sendMessage($this->getLanguage()->translateString("economyFailure", [(string) $return]));
					}
				}
			}else{
				$sender->sendMessage($this->getLanguage()->translateString("commands.generic.onlyPlayer"));
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

	/**
	 * @param int $amount
	 * @param int $count
	 *
	 * @return Item
	 */
	public function getCheck(int $amount, int $count = 1) : Item{
		$paper = Item::get(Item::PAPER, 0xff, $count);
		$paper->setNamedTagEntry(new IntTag('whitecheck-amount', $amount));
		$paper->setCustomName($this->getLanguage()->translateString('check-name', [(string) $amount]));
		$lore = [];
		foreach($paper->setLore($this->getLanguage()->getArray("check.lore")) as $key => $line){
			$lore[] = strtr($line, Utils::listToPairs([$amount]));
		}
		$paper->setLore($lore);
		return $paper;
	}

	/**
	 * @param Item $item
	 *
	 * @return int|null
	 */
	public function getAmount(Item $item) : ?int{
		if($item->getId() == Item::PAPER && $item->getDamage() === 0xff){
			$amount = $item->getNamedTag()->getTagValue('whitecheck-amount', IntTag::class, -1);
			if($amount !== -1){
				return $amount;
			}
		}
		return null;
	}

	/**
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function isCheck(Item $item) : bool{
		return $this->getAmount($item) !== null;
	}
}
