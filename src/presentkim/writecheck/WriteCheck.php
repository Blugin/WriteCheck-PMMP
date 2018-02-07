<?php

namespace presentkim\writecheck;

use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
use pocketmine\plugin\PluginBase;
use presentkim\writecheck\command\PoolCommand;
use presentkim\writecheck\command\subcommands\{
  WriteSubCommand, LangSubCommand, ReloadSubCommand
};
use presentkim\writecheck\listener\PlayerEventListener;
use presentkim\writecheck\util\{
  Translation, Utils
};

class WriteCheck extends PluginBase{

    /** @var self */
    private static $instance = null;

    /** @var string */
    public static $prefix = '';

    /** @var PoolCommand */
    private $command = null;

    /** @return self */
    public static function getInstance() : self{
        return self::$instance;
    }

    public function onLoad() : void{
        if (self::$instance === null) {
            self::$instance = $this;
            Translation::loadFromResource($this->getResource('lang/eng.yml'), true);
        }
    }

    public function onEnable() : void{
        $this->load();
        $this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
    }

    public function load() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $langfilename = $dataFolder . 'lang.yml';
        if (!file_exists($langfilename)) {
            $resource = $this->getResource('lang/eng.yml');
            fwrite($fp = fopen("{$dataFolder}lang.yml", "wb"), $contents = stream_get_contents($resource));
            fclose($fp);
            Translation::loadFromContents($contents);
        } else {
            Translation::load($langfilename);
        }

        self::$prefix = Translation::translate('prefix');
        $this->reloadCommand();
    }

    public function reloadCommand() : void{
        if ($this->command == null) {
            $this->command = new PoolCommand($this, 'wcheck');
            $this->command->createSubCommand(WriteSubCommand::class);
            $this->command->createSubCommand(LangSubCommand::class);
            $this->command->createSubCommand(ReloadSubCommand::class);
        }
        $this->command->updateTranslation();
        $this->command->updateSudCommandTranslation();
        if ($this->command->isRegistered()) {
            $this->getServer()->getCommandMap()->unregister($this->command);
        }
        $this->getServer()->getCommandMap()->register(strtolower($this->getName()), $this->command);
    }

    /**
     * @param string $name = ''
     *
     * @return PoolCommand
     */
    public function getCommand(string $name = '') : PoolCommand{
        return $this->command;
    }

    /** @param PoolCommand $command */
    public function setCommand(PoolCommand $command) : void{
        $this->command = $command;
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
        $paper->setCustomName(Translation::translate('check-name', $amount));
        $lore = [];
        foreach (Translation::getArray('check-lore') as $key => $line) {
            $lore[] = strtr($line, Utils::listToPairs([$amount]));
        }
        $paper->setLore($lore);
        return $paper;
    }
}
