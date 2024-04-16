<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MineCommand extends Executor{

    public function __construct(){
        parent::__construct("mine", "teleport to a mine", "/mine [mine]", "nightfall.command.mine");
        $this->addParameterMap(0);
        $this->addArrayParameter(0, 0, "mine", "Mine", MineManager::getInstance()->getAllNames(), false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            $sender->sendMessage("§r§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        if(!isset($args[0])){
            $mine = MineManager::getInstance()->get($user->getMineRankId());
        }else{
            $mine = MineManager::getInstance()->getFromName(implode($args));
        }

        if($mine === null){
            $sender->sendMessage("§r§c§l> §r§7The selected mine could not be found.");
            return true;
        }

        if(!$mine->hasAccessTo(UserManager::getInstance()->getOnline($sender))){
            $sender->sendMessage("§r§c§l> §r§7You don't have access to this mine.");
            return true;
        }

        $sender->teleport($mine->getSpawnPosition());

        $sender->sendMessage("§r§b§l> §r§7You have been teleported to mine §b".$mine->getName()."§r§7.");
        return true;
    }

}