<?php

namespace presentkim\writecheck\command\subcommands;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use onebone\economyapi\EconomyAPI;
use presentkim\writecheck\WriteCheck as Plugin;
use presentkim\writecheck\command\{
  PoolCommand, SubCommand
};
use presentkim\writecheck\util\Translation;

class WriteSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'write');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (isset($args[0]) && is_numeric($args[0])) {
            if ($sender instanceof Player) {
                $amount = (int) $args[0];
                $count = max(isset($args[1]) && is_numeric($args[1]) ? (int) $args[1] : 1, 1);

                $economyApi = EconomyAPI::getInstance();
                $price = $amount * $count;
                if (($money = $economyApi->myMoney($sender)) < $price) {
                    $sender->sendMessage(Plugin::$prefix . $this->translate('failure', $money));
                } else {
                    $economyApi->reduceMoney($sender, $price);
                    $sender->getInventory()->addItem($this->plugin->getCheck($amount, $count));

                    $sender->sendMessage(Plugin::$prefix . $this->translate('success', $amount, $count, $price, $money - $price));
                }
            } else {
                $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@in-game'));
            }
            return true;
        } else {
            return false;
        }
    }
}