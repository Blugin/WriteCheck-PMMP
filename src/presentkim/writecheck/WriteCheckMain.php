<?php

namespace presentkim\writecheck;

use pocketmine\plugin\PluginBase;
use presentkim\writecheck\listener\PlayerEventListener;
use presentkim\writecheck\util\Translation;
use presentkim\writecheck\command\PoolCommand;
use presentkim\writecheck\command\subcommands\{
  WriteSubCommand, LangSubCommand, ReloadSubCommand, SaveSubCommand
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
            // register instance
            self::$instance = $this;

            // create wcheck PoolCommand
            $this->command = new PoolCommand($this, 'wcheck');
            $this->command->createSubCommand(WriteSubCommand::class);
            $this->command->createSubCommand(LangSubCommand::class);
            $this->command->createSubCommand(ReloadSubCommand::class);
            $this->command->createSubCommand(SaveSubCommand::class);

            // load utils
            $this->getServer()->getLoader()->loadClass('presentkim\writecheck\util\Utils');
        }
    }

    /**
     *
     */
    public function onEnable(){
        $this->load();

        // register event listeners
        $this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
    }

    public function onDisable(){
        $this->save();
    }

    public function load(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        // load lang
        $langfilename = $dataFolder . 'lang.yml';
        if (!file_exists($langfilename)) {
            Translation::loadFromResource($this->getResource('lang/eng.yml'));
            Translation::save($langfilename);
        } else {
            Translation::load($langfilename);
        }

        // register prefix
        self::$prefix = Translation::translate('prefix');

        // update translation and register command
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

    public function save(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        // save lang
        Translation::save("{$dataFolder}lang.yml");
    }
}
