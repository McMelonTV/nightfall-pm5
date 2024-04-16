<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\leaderboard\Leaderboards;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\Server;

class KillTopForm {

    public static function sendTo(Player $sender) : void {
        $pname = $sender->getName();

        $top = Leaderboards::getInstance()->getLeaderboard(Leaderboards::KILLTOP);

        $sender = Server::getInstance()->getPlayerExact($pname);

        if($sender === null) return;

        if(empty($top->getUsers())) {
            $sender->sendMessage("§r§c§l> §r§7The leaderboards are still regenerating, please try again later!");
            return;
        }

        $ui = new CustomForm(null);
        $ui->setTitle("§bTop: kills");

        $str = "§r§fA list of the players who currently have the most kills.\n\n";

        foreach($top->getUsers() as $i => $user) {
            $str .= "\n §b".($i+1)."§r§8>§r§7 ".$user->getName()."§r§8 [§b".$user->getKills()." kills§8]§r\n";
        }

        $ui->addLabel($str);

        $sender->sendForm($ui);
    }

}