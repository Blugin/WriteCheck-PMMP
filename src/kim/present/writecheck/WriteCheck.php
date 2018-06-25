<?php

declare(strict_types=1);

namespace kim\present\writecheck;

use kim\present\writecheck\command\PoolCommand;
use kim\present\writecheck\command\subcommands\WriteSubCommand;
use kim\present\writecheck\listener\PlayerEventListener;
use kim\present\writecheck\util\{
	Translation, Utils
};
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
use pocketmine\plugin\PluginBase;

class WriteCheck extends PluginBase{
	/**
	 * @var WriteCheck
	 */
	private static $instance = null;

	/**
	 * @var PoolCommand
	 */
	private $command = null;

	/**
	 * @return WriteCheck
	 */
	public static function getInstance() : WriteCheck{
		return self::$instance;
	}

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
		$this->command = new PoolCommand($this, 'wcheck');
		$this->command->createSubCommand(WriteSubCommand::class);
		$this->command->updateTranslation();
		$this->command->updateSudCommandTranslation();
		$this->getServer()->getCommandMap()->unregister($this->command);
		$this->getServer()->getCommandMap()->register(strtolower($this->getName()), $this->command);

		//Register event listeners
		$this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
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
