<?php

namespace presentkim\writecheck;

use pocketmine\plugin\PluginBase;
use presentkim\writecheck\listener\PlayerEventListener;
use presentkim\writecheck\util\Translation;
use presentkim\writecheck\command\PoolCommand;
use presentkim\writecheck\command\subcommands\{
  WriteSubCommand, LangSubCommand, ReloadSubCommand
};

class WriteCheckMain extends PluginBase{

    /** @var self */
    private static $instance = null;

    /** @var string */
    public static $prefix = '';

    /** @var PoolCommand */
    private $command;

    /** @return self */
    public static function getInstance(){
        return self::$instance;
    }

    public function onLoad(){
        if (self::$instance === null) {
            self::$instance = $this;
            $this->getServer()->getLoader()->loadClass('presentkim\writecheck\util\Utils');

            $this->command = new PoolCommand($this, 'wcheck');
            $this->command->createSubCommand(WriteSubCommand::class);
            $this->command->createSubCommand(LangSubCommand::class);
            $this->command->createSubCommand(ReloadSubCommand::class);

            Translation::loadFromResource($this->getResource('lang/eng.yml'), true);
        }
    }

    public function onEnable(){
        $this->load();
        $this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
    }

    public function load(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $langfilename = $dataFolder . 'lang.yml';
        if (!file_exists($langfilename)) {
            $resource = $this->getResource('lang/eng.yml');
            Translation::loadFromResource($resource);
            stream_copy_to_stream($resource, $fp = fopen("{$dataFolder}lang.yml", "wb"));
            fclose($fp);
        } else {
            Translation::load($langfilename);
        }

        self::$prefix = Translation::translate('prefix');
        $this->reloadCommand();
    }

    public function reloadCommand(){
        $this->command->updateTranslation();
        $this->command->updateSudCommandTranslation();
        if ($this->command->isRegistered()) {
            $this->getServer()->getCommandMap()->unregister($this->command);
        }
        $this->getServer()->getCommandMap()->register(strtolower($this->getName()), $this->command);
    }
}
