<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0.0
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\writecheck\listener;

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
						$player->sendMessage($this->owner->getLanguage()->translateString("check.help", [(string) $amount]));
					}
				}else{
					$economyApi = EconomyAPI::getInstance();
					$return = $economyApi->addMoney($player, $amount, false, $this->owner->getName());
					if($return === EconomyAPI::RET_SUCCESS){
						--$item->count;
						$inventory->setItemInHand($item);
						$player->sendMessage($this->owner->getLanguage()->translateString("check.use", [(string) $amount, (string) $economyApi->myMoney($player)]));
					}else{
						$player->sendMessage($this->owner->getLanguage()->translateString("economyFailure", [(string) $return]));
					}
				}
				$event->setCancelled(true);
			}
		}
	}
}