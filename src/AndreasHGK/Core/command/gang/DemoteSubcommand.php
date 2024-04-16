<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class DemoteSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("demote", "demote a gang member", "demote <player>", "nightfall.command.gang.demote");
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
            $sender->sendMessage("§r§c§l>§r§7 You must be a leader to demote a gang member.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a player to demote.");
            return true;
        }

        $user = $gang->getMember($args[0]);
        if($user === null){
            $sender->sendMessage("§r§c§l>§r§7 That player is not in the gang.");
            return true;
        }

        if($user->getGangRank()->equals(GangRank::RECRUIT())){
            $sender->sendMessage("§r§c§l>§r§7 You cannot demote a recruit, kick them instead.");
            return true;
        }

        if($user->getGangRank()->equals(GangRank::LEADER())){
            $sender->sendMessage("§r§c§l>§r§7 You cannot demote yourself.");
            return true;
        }

        $belowRank = $user->getGangRank()->below();
        $user->setGangRank($belowRank);

        $sender->sendMessage("§r§b§l> §r§7You have demoted §b" . $user->getName() . "§r§7.");
        if($user->isOnline()){
            $user->getPlayer()->sendMessage("§r§b§l> §r§7You have been demoted to §b" . $belowRank->name());
        }

        return true;
    }
}