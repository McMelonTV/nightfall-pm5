<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\rank\MineRankManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\PermissionManager;
use pocketmine\Server;

class SetMineCommand extends Executor{

    public function __construct(){
        parent::__construct("setmine", "set someones mine", "/setrank <player> <mine>", "nightfall.command.setmine");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addArrayParameter(0, 1, "mine", "Minerank", MineRankManager::getInstance()->getAllNames(), false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(count($args) < 2){
            $sender->sendMessage("§c§l> §r§7Usage: §c".$this->usage."§7.");
            return true;
        }

        $player = Server::getInstance()->getPlayerByPrefix($args[0]);
        if($player === null && UserManager::getInstance()->exist($args[0])) {
            $player = Server::getInstance()->getOfflinePlayer($args[0]);
        }

        if(!$player->hasPlayedBefore()){
            $sender->sendMessage("§c§l> §r§7That player was never connected.");
            return true;
        }

        $mineRank = MineRankManager::getInstance()->getFromName($args[1]);
        if($mineRank === null){
            $sender->sendMessage("§c§l> §r§7That mine does not exist.");
            return true;
        }

        $user = UserManager::getInstance()->get($player);
        $user->setMineRank($mineRank);
        $permManager = PermissionManager::getInstance();
        foreach(UserManager::getInstance()->get($player)->getMineRank()->getPerms() as $perm){
            $permManager->subscribeToPermission($perm, $player);
        }

        $sender->sendMessage("§b§l> §r§7Set §b".$player->getName()."§7's mine to §b".$mineRank->getName()."§7.");
        if($user->isOnline() && $player !== $sender) {
            $player->sendMessage("§b§l> §r§7Your mine has been changed to §b".$mineRank->getName()."§7.");
        }

        if(!$user->isOnline()) {
            UserManager::getInstance()->save($user);
        }

        return true;
    }
}