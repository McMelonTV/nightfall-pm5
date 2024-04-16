<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class LeaderboardsForm {

    public static function sendTo(Player $sender) : void {
        $ui = new SimpleForm(static function (Player $player, $data){
            if($data === null) {
                return;
            }

            switch ($data){
                case "baltop":
                    BalTopForm::sendTo($player);
                    break;
                case "earntop":
                    EarnTopForm::sendTo($player);
                    break;
                case "breaktop":
                    BreakTopForm::sendTo($player);
                    break;
                case "minetop":
                    MineTopForm::sendTo($player);
                    break;
                case "killtop":
                    KillTopForm::sendTo($player);
                    break;
                case "kdtop":
                    KDRatioTopForm::sendTo($player);
                    break;
            }
        });

        $ui->setTitle("§bLeaderboards");
        $ui->setContent("§r§fSelect the category you want to view.");

        $ui->addButton("§rBalance", -1, "", "baltop");
        $ui->addButton("§rTotal money earned", -1, "", "earntop");
        $ui->addButton("§rTotal blocks broken", -1, "", "breaktop");
        $ui->addButton("§rHighest mine", -1, "", "minetop");
        $ui->addButton("§rMost kills", -1, "", "killtop");
        $ui->addButton("§rHighest K/D ratio", -1, "", "kdtop");

        $sender->sendForm($ui);
    }
}