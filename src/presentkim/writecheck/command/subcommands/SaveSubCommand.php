<?php

namespace presentkim\writecheck\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\writecheck\{
  command\PoolCommand, WriteCheckMain as Plugin, command\SubCommand
};

class SaveSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'save');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args){
        $this->plugin->save();
        $sender->sendMessage(Plugin::$prefix . $this->translate('success'));

        return true;
    }
}