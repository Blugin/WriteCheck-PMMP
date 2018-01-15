<?php

namespace presentkim\writecheck\listener;

use pocketmine\event\{
  Listener, player\PlayerInteractEvent
};
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
use onebone\economyapi\EconomyAPI;
use presentkim\writecheck\{
  WriteCheckMain as Plugin, util\Translation
};

class PlayerEventListener implements Listener{

    /** @var Plugin */
    private $owner = null;

    public function __construct(){
        $this->owner = Plugin::getInstance();
    }

    /** @param PlayerInteractEvent $event */
    public function onPlayerInteractEvent(PlayerInteractEvent $event){
        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR) {
            $player = $event->getPlayer();
            $inventory = $player->getInventory();
            $item = $inventory->getItemInHand();
            if ($item->getId() == Item::PAPER && $item->getDamage() === 0xff) {
                $amount = $item->getNamedTag()->getTagValue('whitecheck-amount', IntTag::class, -1);
                if ($amount !== -1) {
                    $item->count = 1;
                    $inventory->removeItem($item);

                    $economyApi = EconomyAPI::getInstance();
                    $economyApi->addMoney($player, $amount);
                    $player->sendMessage(Plugin::$prefix . Translation::translate('check-use', $amount, $economyApi->myMoney($player)));
                }
            }
        }
    }
}