<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\leaderboard\Leaderboards;
use AndreasHGK\Core\utils\IntUtils;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\Server;

class EarnTopForm {

    public static function sendTo(Player $sender) : void {
        $pname = $sender->getName();

        $top = Leaderboards::getInstance()->getLeaderboard(Leaderboards::EARNTOP);

        $sender = Server::getInstance()->getPlayerExact($pname);

        if($sender === null) return;

        if(empty($top->getUsers())) {
            $sender->sendMessage("§r§c§l> §r§7The leaderboards are still regenerating, please try again later!");
            return;
        }

        $ui = new CustomForm(null);
        $ui->setTitle("§bTop: total earned money");

        $str = "§r§fA list of the players who have earned the most money (by mining).\n\n";
        foreach($top->getUsers() as $key => $user) {
            $str .= "\n §b".($key+1)."§r§8>§r§7 ".$user->getName()."§r§8 [§b$".IntUtils::shortNumberRounded($user->getTotalEarnedMoney())."§8]§r\n";
        }

        $ui->addLabel($str);

        $sender->sendForm($ui);
    }
}