<?php

namespace presentkim\writecheck\command\subcommands;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
use onebone\economyapi\EconomyAPI;
use presentkim\writecheck\WriteCheck as Plugin;
use presentkim\writecheck\command\{
  PoolCommand, SubCommand
};
use presentkim\writecheck\util\{
  Translation, Utils
};

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
                if (($money = $economyApi->myMoney($sender)) < $amount * $count) {
                    $sender->sendMessage(Plugin::$prefix . $this->translate('failure', $money));
                } else {
                    $economyApi->reduceMoney($sender, $amount * $count);

                    $paper = Item::get(Item::PAPER, 0xff, $count);
                    $paper->setNamedTagEntry(new IntTag('whitecheck-amount', $amount));
                    $paper->setCustomName(Translation::translate('check-name', $amount));
                    $lore = [];
                    foreach (Translation::getArray('check-lore') as $key => $line) {
                        $lore[] = strtr($line, Utils::listToPairs([$amount]));
                    }
                    $paper->setLore($lore);
                    $sender->getInventory()->addItem($paper);

                    $sender->sendMessage(Plugin::$prefix . $this->translate('success', $amount, $count));
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