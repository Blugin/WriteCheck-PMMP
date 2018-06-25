<?php

declare(strict_types=1);

namespace kim\present\writecheck\listener;

use kim\present\writecheck\util\Translation;
use kim\present\writecheck\WriteCheck as Plugin;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class PlayerEventListener implements Listener{
	/**
	 * @var Plugin
	 */
	private $owner = null;

	/**
	 * @var int[]
	 */
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
		if(!$event->isCancelled() && ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK || $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR)){
			$player = $event->getPlayer();
			$inventory = $player->getInventory();
			$item = $inventory->getItemInHand();
			$amount = $this->owner->getAmount($item);
			if($amount !== null){
				if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK){
					if(!isset($this->touched[$playerName = $player->getLowerCaseName()]) || $this->touched[$playerName] < time()){
						$this->touched[$playerName] = time() + 3;

						$player->sendMessage(Translation::translate('check-help', $amount));
					}
				}else{
					$economyApi = EconomyAPI::getInstance();
					$return = $economyApi->addMoney($player, $amount, false, $this->owner->getName());
					if($return === EconomyAPI::RET_SUCCESS){
						--$item->count;
						$inventory->setItemInHand($item);
						$player->sendMessage(Translation::translate('check-use', $amount, $economyApi->myMoney($player)));
					}else{
						$player->sendMessage(Translation::translate('economy-failure', $return));
					}
				}
				$event->setCancelled(true);
			}
		}
	}
}