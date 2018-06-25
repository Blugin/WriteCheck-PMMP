<?php

declare(strict_types=1);

namespace kim\present\writecheck;

use kim\present\writecheck\listener\PlayerEventListener;
use kim\present\writecheck\util\{
	Translation, Utils
};
use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
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
	 * @var PluginCommand
	 */
	private $command;

	public function onLoad() : void{
		if(self::$instance === null){
			self::$instance = $this;
			Translation::loadFromResource($this->getResource('lang/eng.yml'), true);
		}
	}

	public function onEnable() : void{
		if(!file_exists($dataFolder = $this->getDataFolder())){
			mkdir($dataFolder, 0777, true);
		}

		//Load language file
		$langFile = $dataFolder . 'lang.yml';
		if(!file_exists($langFile)){
			$resource = $this->getResource('lang/eng.yml');
			fwrite($fp = fopen("{$dataFolder}lang.yml", "wb"), $contents = stream_get_contents($resource));
			fclose($fp);
			Translation::loadFromContents($contents);
		}else{
			Translation::load($langFile);
		}

		//Register main command
		$this->command = new PluginCommand(Translation::translate("command-wcheck"), $this);
		$this->command->setPermission("wcheck.cmd");
		$this->command->setAliases(Translation::getArray("command-wcheck@aliases"));
		$this->command->setUsage(Translation::translate("command-wcheck@usage"));
		$this->command->setDescription(Translation::translate("command-wcheck@description"));
		$this->getServer()->getCommandMap()->register($this->getName(), $this->command);

		//Register event listeners
		$this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
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
					$sender->sendMessage(Translation::translate("command-wcheck@failure", (string) $money));
				}else{
					$return = $economyApi->reduceMoney($sender, $price, false, $this->getName());
					if($return === EconomyAPI::RET_SUCCESS){
						$sender->getInventory()->addItem($this->getCheck($amount, $count));
						$sender->sendMessage(Translation::translate("command-wcheck@success", (string) $amount, (string) $count, (string) $price, (string) ($money - $price)));
					}else{
						$sender->sendMessage(Translation::translate("command-generic-failure@economy-failure", (string) $return));
					}
				}
			}else{
				$sender->sendMessage(Translation::translate('command-generic-failure@in-game'));
			}
			return true;
		}else{
			return false;
		}
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
		$paper->setCustomName(Translation::translate('check-name', (string) $amount));
		$lore = [];
		foreach(Translation::getArray('check-lore') as $key => $line){
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
