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

class EnemySubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("enemy", "become an enemy with a gang", "enemy <gang>", "nightfall.command.gang.enemy", ["enemywith"]);
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
            $sender->sendMessage("§r§c§l>§r§7 You must be the leader to enemy.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a gang to enemy.");
            return true;
        }

        if(($targetGang = GangManager::getInstance()->getByName($args[0])) === null){
            $sender->sendMessage("§r§c§l>§r§7 That gang does not exist.");
            return true;
        }

        $targetName = $targetGang->getName();

        $gangName = $gang->getName();
        if(!$gang->isAlliedWith($targetGang)){
            $sender->sendMessage("§r§b§l> §r§7You are already enemied with §b".$targetName."§7.");
            return true;
        }

        $gang->removeAlly($targetGang);

        $sender->sendMessage("§r§b§l> §r§7You are now enemies wih §b".$targetName."§7.");

        $leader = $targetGang->getLeader();
        if($leader === null || !$leader->isOnline()){
            return true;
        }

        $leader->getPlayer()->sendMessage("§r§b§l> §r§7You are now enemies with §b".$gangName."§7.");
        return true;
    }
}