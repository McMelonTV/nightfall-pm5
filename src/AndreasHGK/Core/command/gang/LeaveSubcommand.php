<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class LeaveSubcommand extends Subcommand{

	public function __construct(){
		parent::__construct("leave", "leave your gang", "leave", "nightfall.command.gang.leave");
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
		if(!$sender instanceof Player){
			$sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
			return true;
		}

		$user = UserManager::getInstance()->getOnline($sender);
		if(!$user->isInGang()){
			$sender->sendMessage("§r§c§l>§r§7 You are not in a gang.");
			return true;
		}

		$gang = $user->getGang();
		if($user->getGangRank()->equals(GangRank::LEADER())){
			$sender->sendMessage("§r§c§l>§r§7 You cannot leave while being leader.");
			return true;
		}

		$gang->removeMember($user->getName());

		Server::getInstance()->broadcastMessage("§r§b§l> §r§b".$user->getName()." §r§7 has left the gang.", $gang->getOnlineMembers());

		$sender->sendMessage("§r§b§l> §r§7 You have left the gang.");
		return true;
	}
}
