<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class KickSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("kick", "kick a gang member", "kick <player>", "nightfall.command.gang.kick");
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
        if(!$user->getGangRank()->equals(GangRank::OFFICER()) and !$user->getGangRank()->equals(GangRank::LEADER())){
            $sender->sendMessage("§r§c§l>§r§7 You must be a officer or above to kick a gang member.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a player to kick.");
            return true;
        }

        $targetUser = $gang->getMember($args[0]);
        if($targetUser === null){
            $sender->sendMessage("§r§c§l>§r§7 That player is not in the gang.");
            return true;
        }

        if(!$user->getGangRank()->equals(GangRank::OFFICER()) and $targetUser->getGangRank()->equals(GangRank::OFFICER())){
            $sender->sendMessage("§r§c§l>§r§7 Only the leader can kick an officer.");
            return true;
        }

        if($targetUser->getGangRank() === GangRank::LEADER()){
            $sender->sendMessage("§r§c§l>§r§7 You cannot kick the leader.");
            return true;
        }

        $gang->removeMember($targetUser->getName());
        $sender->sendMessage("§r§b§l> §r§7You have kicked §b" . $targetUser->getName() . "§r§7 from your gang.");
        if(($player = $targetUser->getPlayer()) instanceof Player){
            $player->sendMessage("§r§b§l> §r§7You have been kicked from your gang.");
        }

        return true;
    }
}