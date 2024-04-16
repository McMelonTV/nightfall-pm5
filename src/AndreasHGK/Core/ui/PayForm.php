<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\user\UserManager;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\Server;

class PayForm {

    public static function sendTo(Player $sender, Player $target = null, int $amount = null) : void {
        $players = [];
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $players[] = $player->getName();
        }

        $ui = new CustomForm(static function (Player $sender, ?array $data) use ($players){
            if($data === null){
                return;
            }

            $playerName = $players[$data["player"]];
            $money = $data["money"];
            $target = Server::getInstance()->getPlayerExact($playerName);
            if($target === null){
                $sender->sendMessage("§c§l> §r§7Player not found.");
                return;
            }

            if(!is_numeric($money) || $money < 0){
                $sender->sendMessage("§c§l> §r§7Please enter a valid amount of money.");
                return;
            }else{
                $money = (int)$money;
            }

            $senderUser = UserManager::getInstance()->get($sender);
            $targetUser = UserManager::getInstance()->get($target);
            if($senderUser->getBalance() < $money){
                $sender->sendMessage("§c§l> §r§7You don't have enough money for this transaction.");
                return;
            }

            $senderUser->takeMoney($money);
            $targetUser->addMoney($money);
            $sender->sendMessage("§b§l> §r§7You paid §e$".$money."§r§7 to §e".$target->getName()."§r§7.");
            if($target !== $sender) {
                $target->sendMessage("§b§l> §e".$sender->getName()." §r§7paid you §e$".$money."§r§7.");
            }
        });
        $ui->setTitle("§b/pay");
        $ui->addDropdown("§b§l> §r§7Player", $players, isset($target) ? array_search($target->getName(), $players) : null, "player");
        $ui->addInput("§b§l> §r§7Money", "", (string)$amount ?? null, "money");

        $sender->sendForm($ui);
    }

}