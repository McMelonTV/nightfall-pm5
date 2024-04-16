<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class SetBalanceCommand extends Executor{

    public function __construct(){
        parent::__construct("setbalance", "set someones balance", "/setbalance <player> <balance>", "nightfall.command.setbalance", ["setmoney"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addNormalParameter(0, 1, "balance", CustomCommandParameter::ARG_TYPE_INT, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(count($args) < 2){
            $sender->sendMessage("§c§l> §r§7Usage: §c".$this->usage."§7.");
            return true;
        }

        $player = Server::getInstance()->getPlayerExact($args[0]);
        if($player === null && UserManager::getInstance()->exist($args[0])) {
            $player = Server::getInstance()->getOfflinePlayer($args[0]);
        }

        if($player === null){
            $sender->sendMessage("§c§l> §r§7That player was never connected.");
            return true;
        }

        if(!is_int((int)$args[1])){
            $sender->sendMessage("§c§l> §r§7Please enter a valid prestige number.");
            return true;
        }

        $user = UserManager::getInstance()->get($player);
        $user->setBalance((int)$args[1]);
        $sender->sendMessage("§b§l> §r§7Set §b".$player->getName()."§7's balance to §b$".$args[1]."§7.");
        if($user->isOnline() && $player !== $sender) {
            $player->sendMessage("§b§l> §r§7Your balance has been changed to §b$".$args[1]."§7.");
        }

        if(!$user->isOnline()) {
            UserManager::getInstance()->save($user);
        }

        return true;
    }
}