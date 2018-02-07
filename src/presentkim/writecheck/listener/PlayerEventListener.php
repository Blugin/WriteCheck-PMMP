<?php

namespace presentkim\writecheck\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
use onebone\economyapi\EconomyAPI;
use presentkim\writecheck\WriteCheck as Plugin;
use presentkim\writecheck\util\Translation;

class PlayerEventListener implements Listener{

    /** @var Plugin */
    private $owner = null;

    /** @var int[] */
    private $touched = [];

    public function __construct(){
        $this->owner = Plugin::getInstance();
    }

    /**
     * @priority LOWEST
     *
     * @param PlayerInteractEvent $event
     */
    public function onPlayerInteractEvent(PlayerInteractEvent $event) : void{
        if ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK || $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR) {
            $player = $event->getPlayer();
            $inventory = $player->getInventory();
            $item = $inventory->getItemInHand();
            if ($item->getId() == Item::PAPER && $item->getDamage() === 0xff) {
                $amount = $item->getNamedTag()->getTagValue('whitecheck-amount', IntTag::class, -1);
                if ($amount !== -1) {
                    if ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                        $playerName = $player->getLowerCaseName();
                        if (!isset($this->touched[$playerName]) || $this->touched[$playerName] < time()) {
                            $player->sendMessage(Plugin::$prefix . Translation::translate('check-help', $amount));
                            $this->touched[$playerName] = time() + 3;
                        }
                    } else {
                        $item->count = 1;
                        $inventory->removeItem($item);

                        $economyApi = EconomyAPI::getInstance();
                        $economyApi->addMoney($player, $amount);
                        $player->sendMessage(Plugin::$prefix . Translation::translate('check-use', $amount, $economyApi->myMoney($player)));
                    }
                    $event->setCancelled(true);
                }
            }
        }
    }
}