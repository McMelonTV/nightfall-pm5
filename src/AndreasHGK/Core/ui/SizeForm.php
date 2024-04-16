<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;

class SizeForm {

    public static function sendTo(Player $sender) : void {
        $ui = new CustomForm(static function (Player $player, ?array $data){
            if($data === null) {
                return;
            }

            $size = $data["size"];
            if($size < 50){
                $player->sendMessage("§b§l> §r§7Your size can't be lower than §b50§7.");
                return;
            }

            if($size > 150){
                $player->sendMessage("§b§l> §r§7Your size can't be higher than §b150§7.");
                return;
            }

            $user = UserManager::getInstance()->getOnline($player);
            if($user instanceof User){
                $user->setSize((int)$size);
            }

            $player->setScale($size/100);
            $player->sendMessage("§b§l> §r§7Your set your size to §b".$player->getScale()."§7.");
        });

        $ui->setTitle("§b/size");
        $ui->addLabel("Please select a value you want your size to be. §8(100 for default size)");

        $ui->addSlider("§b§l>§r§7 Size §8(in percent)§7", 50, 150, -1, 100, "size");

        $sender->sendForm($ui);
    }
}