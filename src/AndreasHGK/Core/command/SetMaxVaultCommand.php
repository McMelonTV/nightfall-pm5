<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class SetMaxVaultCommand extends Executor{

    public function __construct(){
        parent::__construct("setmaxvaults", "set someones max vaults", "/setmaxvaults <player> <vaults>", "nightfall.command.setmaxvaults");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addNormalParameter(0, 1, "vaults", CustomCommandParameter::ARG_TYPE_INT, false, true);
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

        if($player === null){
            $sender->sendMessage("§c§l> §r§7That player was never connected.");
            return true;
        }

        $plots = $args[1];
        if(!is_numeric($plots)){
            $sender->sendMessage("§c§l> §r§7Please enter a valid max vault count.");
            return true;
        }

        $plots = (int)$plots;
        $user = UserManager::getInstance()->get($player);
        $user->getVault()->setMaxPages($plots);

        $sender->sendMessage("§b§l> §r§7Set §b".$player->getName()."§7's max vault count to §b".$plots."§7.");
        if($user->isOnline() && $player !== $sender) {
            $player->sendMessage("§b§l> §r§7Your max vault count has been changed to §b".$plots."§7.");
        }

        if(!$user->isOnline()) {
            UserManager::getInstance()->save($user);
        }

        return true;
    }
}