<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\ui\PayForm;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class PayCommand extends Executor{

    public function __construct(){
        parent::__construct("pay", "pay someone", "/pay [player] [amount]", "nightfall.command.pay");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, true, true);
        $this->addNormalParameter(0, 1, "amount", CustomCommandParameter::ARG_TYPE_INT, true, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) return false;

        if(isset($args[0])){
            $player = Server::getInstance()->getPlayerByPrefix($args[0]);
            if($player === null && UserManager::getInstance()->exist($args[0])) {
                $player = Server::getInstance()->getOfflinePlayer($args[0]);
            }

            if($player === null){
                $sender->sendMessage("§c§l> §r§7That player was never connected.");
                return true;
            }
        }else{
            $player = null;
        }
        if(isset($args[1])){
            if(!is_float((float)$args[1]) || (float)$args[1] < 0){
                $sender->sendMessage("§c§l> §r§7Please enter a valid prestige amount of money.");
                return true;
            }

            $money = (float)$args[1];

            $senderUser = UserManager::getInstance()->get($sender);
            $targetUser = UserManager::getInstance()->get($player);

            if($senderUser->getBalance() < $money){
                $sender->sendMessage("§c§l> §r§7You don't have enough money for this transaction.");
                return true;
            }

            $senderUser->takeMoney((int)$money);
            $targetUser->addMoney((int)$money);
            $sender->sendMessage("§b§l> §r§7You paid §e$".$money."§r§7 to §e".$player->getName()."§r§7.");
            if($player !== $sender) {
                $player->sendMessage("§b§l> §r§e".$sender->getName()." §r§7paid you §e$".$money."§r§7.");
            }

            return true;
        }else{
            $money = null;
        }

        PayForm::sendTo($sender, $player, $money);
        return true;
    }

}