<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class PromoteSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("promote", "promote a gang member", "promote <player>", "nightfall.command.gang.promote");
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
        if(!$user->getGangRank()->equals(GangRank::LEADER())){
            $sender->sendMessage("§r§c§l>§r§7 You must be a leader to promote a gang member.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a player to promote.");
            return true;
        }

        $targetUser = $gang->getMember(implode(" ", $args));
        if($targetUser === null){
            $sender->sendMessage("§r§c§l>§r§7 That player is not in the gang.");
            return true;
        }

        $gangRank = $targetUser->getGangRank();
        if($gangRank->equals(GangRank::OFFICER())){
            $sender->sendMessage("§r§c§l>§r§7 You cannot promote a officer.");
            return true;
        }

        if($gangRank->equals(GangRank::LEADER())){
            $sender->sendMessage("§r§c§l>§r§7 You cannot promote yourself.");
            return true;
        }

        $gangRank = $gangRank->above();
        $targetUser->setGangRank($gangRank);

        $sender->sendMessage("§r§b§l> §r§7You have promoted §b" . $targetUser->getName() . "§7.");
        if($targetUser->isOnline()){
            $targetUser->getPlayer()->sendMessage("§r§b§l> §r§7You have been promoted to §b" . $gangRank->name() . "§7.");
        }
        return true;
    }

}