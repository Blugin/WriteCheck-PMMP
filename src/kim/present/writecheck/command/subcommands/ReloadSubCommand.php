<?php

declare(strict_types=1);

namespace kim\present\writecheck\command\subcommands;

use kim\present\writecheck\command\{
	PoolCommand, SubCommand
};
use kim\present\writecheck\WriteCheck as Plugin;
use pocketmine\command\CommandSender;

class ReloadSubCommand extends SubCommand{
	public function __construct(PoolCommand $owner){
		parent::__construct($owner, 'reload');
	}

	/**
	 * @param CommandSender $sender
	 * @param String[]      $args
	 *
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, array $args) : bool{
		$this->plugin->load();
		$sender->sendMessage(Plugin::$prefix . $this->translate('success'));

		return true;
	}
}