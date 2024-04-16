<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\achievement\Achievement;
use AndreasHGK\Core\achievement\AchievementManager;
use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\Price;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CreateSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("create", "create a gang", "create <gang>", "nightfall.command.gang.create", ["make", "c"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        if($user->isInGang()){
            $sender->sendMessage("§r§c§l>§r§7 You are already in a gang.");
            return true;
        }

        $price = new Price(0, 0, 0, 0, 50000);
        if(!$price->canAfford($user->getPlayer())){
            $sender->sendMessage("§r§c§l>§r§7 You need §b50000$ §7in order to create a gang.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a name for your gang.");
            return true;
        }

        $name = implode(" ", $args);
        if(!GangManager::getInstance()->validateGangNameLength($name)){
            $sender->sendMessage("§r§c§l>§r§7 A gang name should be at least §b".GangManager::NAME_MIN."§r§7 characters long and cannot be longer than §b".GangManager::NAME_MAX."§r§7 characters.");
            return true;
        }

        if(!GangManager::getInstance()->validateGangNameValidity($name)){
            $sender->sendMessage("§r§c§l>§r§7 A gang name must only contain alphanumeric characters.");
            return true;
        }

        if(GangManager::getInstance()->exists($name)){
            $sender->sendMessage("§r§c§l>§r§7 A gang with that name already exists.");
            return true;
        }

        $price->pay($user->getPlayer());

        $gang = GangManager::getInstance()->create($name, $user);

        AchievementManager::getInstance()->tryAchieve($user, Achievement::TEAM_UP);

        $sender->sendMessage("§r§b§l> §r§7You created a gang named §b".$gang->getName()."§r§7.");
        return true;
    }
}