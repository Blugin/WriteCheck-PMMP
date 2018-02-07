<?php

namespace presentkim\writecheck\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
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
        if (!$event->isCancelled() && ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK || $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR)) {
            $player = $event->getPlayer();
            $inventory = $player->getInventory();
            $item = $inventory->getItemInHand();
            $amount = $this->owner->getAmount($item);
            if ($amount !== null) {
                if ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                    if (!isset($this->touched[$playerName = $player->getLowerCaseName()]) || $this->touched[$playerName] < time()) {
                        $this->touched[$playerName] = time() + 3;

                        $player->sendMessage(Plugin::$prefix . Translation::translate('check-help', $amount));
                    }
                } else {
                    $economyApi = EconomyAPI::getInstance();
                    $return = $economyApi->addMoney($player, $amount, false, $this->owner->getName());
                    if ($return === EconomyAPI::RET_SUCCESS) {
                        --$item->count;
                        $inventory->setItemInHand($item);
                        $player->sendMessage(Plugin::$prefix . Translation::translate('check-use', $amount, $economyApi->myMoney($player)));
                    } else {
                        $player->sendMessage(Plugin::$prefix . Translation::translate('economy-failure', $return));
                    }
                }
                $event->setCancelled(true);
            }
        }
    }
}