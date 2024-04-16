<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\OfflineUser;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AllySubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("ally", "ally with another gang", "ally <gang>", "nightfall.command.gang.ally", ["allywith"]);
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
            $sender->sendMessage("§r§c§l>§r§7 You must be the leader to ally.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a gang to ally with.");
            return true;
        }

        if(($targetGang = GangManager::getInstance()->getByName($args[0])) === null){
            $sender->sendMessage("§r§c§l>§r§7 That gang does not exist.");
            return true;
        }

        $targetName = $targetGang->getName();

        $gangName = $gang->getName();
        if($targetGang->askedToAllyWith($gang)){
            $targetGang->allyWith($gang);

            $sender->sendMessage("§r§b§l> §r§7You are now allied with §b" . $targetName . "§7.");

            $leader = $targetGang->getLeader();
            if(!$leader->isOnline() || $leader === null){
                return true;
            }

            $leader->getPlayer()->sendMessage("§r§b§l> §r§7You are now allied with §b" . $gangName . "§7.");
            return true;
        }

        $leader = $targetGang->getLeader();
        if(!$leader->isOnline() || $leader === null){
            $sender->sendMessage("§r§b§l> §r§7The leader of that gang is offline, wait for them to come online to ally with them.");
            return true;
        }

        $gang->askToAllyWith($targetGang);

        $sender->sendMessage("§r§b§l> §r§7You have asked to ally wih §b".$targetName."§7.");
        $leader->getPlayer()->sendMessage("§r§b§l> §r§b".$gangName."§r§7 has asked to ally with you, do §b/gang ally ".$gangName."§7 to accept the request.");
        return true;
    }
}