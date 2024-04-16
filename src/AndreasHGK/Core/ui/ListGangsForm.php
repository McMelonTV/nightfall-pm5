<?php

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\gang\GangManager;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;

class ListGangsForm{

    public static function sendTo(Player $sender) : void {
        $ui = new CustomForm(null);
        $ui->setTitle("§bGang list");

        $str = "§r§fHere is a list of all gangs on the server.\n";
        foreach(GangManager::getInstance()->getAll() as $gang){
            $str .= "\n§b > §7".$gang->getName();
        }

        $ui->addLabel($str);

        $sender->sendForm($ui);
    }
}