<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SetdescriptionSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("setdescription", "set your gang description", "setdescription [description]", "nightfall.command.gang.setdescription", ["setdesc"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command in-game.");
            return true;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        if(!$user->isInGang()){
            $sender->sendMessage("§r§c§l>§r§7 You are not in a gang.");
            return true;
        }

        $gang = $user->getGang();
        if(!$user->getGangRank()->equals(GangRank::LEADER())){
            $sender->sendMessage("§r§c§l>§r§7 You must be the leader of a gang to set its description.");
            return true;
        }

        $gang->setDescription(implode(" ", $args));

        $sender->sendMessage("§r§b§l> §r§7You set your gang description to §b".$gang->getDescription()."§r§7.");
        return true;
    }
}