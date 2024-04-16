<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class DisbandSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("disband", "disband a gang", "disband [gang]", "nightfall.command.gang.disband");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        if(!$user->isInGang() && !$user->getAdminMode()){
            $sender->sendMessage("§r§c§l>§r§7 You are not in a gang.");
            return true;
        }

        $gang = $user->getGang();
        if($user->getAdminMode() && isset($args[0])){
            $name = implode($args);
            if(!GangManager::getInstance()->exists($name)){
                $sender->sendMessage("§r§c§l>§r§7 No such gang exists.");
                return true;
            }

            $gang = GangManager::getInstance()->getByName($name);
        }

        if(!$user->getGangRank()->equals(GangRank::LEADER()) && !$user->getAdminMode()){
            $sender->sendMessage("§r§c§l>§r§7 You must be the leader of a gang to disband it.");
            return true;
        }

        GangManager::getInstance()->delete($gang);

        $sender->sendMessage("§r§b§l> §r§7You disbanded §b".$gang->getName()."§r§7.");
        return true;
    }
}