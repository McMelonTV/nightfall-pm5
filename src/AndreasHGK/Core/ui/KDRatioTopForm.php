<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\leaderboard\Leaderboards;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\Server;

class KDRatioTopForm {

    public static function sendTo(Player $sender) : void {
        $pname = $sender->getName();

        $top = Leaderboards::getInstance()->getLeaderboard(Leaderboards::KDTOP);

        $sender = Server::getInstance()->getPlayerExact($pname);

        if($sender === null) {
            return;
        }

        if(empty($top->getUsers())) {
            $sender->sendMessage("§r§c§l> §r§7The leaderboards are still regenerating, please try again later!");
            return;
        }

        $ui = new CustomForm(null);
        $ui->setTitle("§bTop: K/D Ratio");

        $str = "§r§fA list of the players who currently have the kill to death ratio. Only deaths by other players count.\n\n";
        foreach($top->getUsers() as $i => $user) {
            $str .= "\n §b".($i+1)."§r§8>§r§7 ".$user->getName()."§r§8 [§b".round($user->getKDR(), 2)." K/D§8]§r\n";
        }

        $ui->addLabel($str);

        $sender->sendForm($ui);
    }
}