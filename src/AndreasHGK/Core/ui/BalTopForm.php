<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\leaderboard\Leaderboards;
use AndreasHGK\Core\utils\IntUtils;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\Server;

class BalTopForm {

    public static function sendTo(Player $sender) : void {
        $pname = $sender->getName();

/*        $balTop = [];
        foreach(UserManager::getInstance()->getAll() as $name => $user){
            $balTop[$name] = $user->getBalance();
        }*/

/*        $sorter = new AsyncSortTask(
            $balTop,
            function ($var1, $var2) {
                if($var1 < $var2) {
                    return 1;
                }

                if($var1 > $var2) {
                    return -1;
                }

                if($var1 === $var2) {
                    return 0;
                }

                return 0;
            },
            function ($balTop) use ($pname) {
                $sender = Server::getInstance()->getPlayerExact($pname);
                if($sender === null) return;

                $ui = new CustomForm(null);

                $ui->setTitle("§bTop: money");

                $str = "§r§fA list of the players who currently have the most amount of money.\n\n";
                for($i = 0; $i < 25; ++$i){
                    if(empty($balTop)) break;
                    $name = array_key_first($balTop);
                    $money = array_shift($balTop);
                    $str .= "\n §b".($i+1)."§r§8>§r§7 ".$name."§r§8 [§b$".IntUtils::shortNumberRounded($money)."§8]§r\n";
                }

                $ui->addLabel($str);

                $sender->sendForm($ui);
            }
        );

        Server::getInstance()->getAsyncPool()->submitTask($sorter);*/

        $baltop = Leaderboards::getInstance()->getLeaderboard(Leaderboards::BALTOP);

        $sender = Server::getInstance()->getPlayerExact($pname);
        if($sender === null) return;

        if(empty($baltop->getUsers())) {
            $sender->sendMessage("§r§c§l> §r§7The leaderboards are still regenerating, please try again later!");
            return;
        }

        $ui = new CustomForm(null);

        $ui->setTitle("§bTop: money");

        $str = "§r§fA list of the players who currently have the most amount of money.\n\n";

        foreach($baltop->getUsers() as $key => $user) {
            $str .= "\n §b".($key+1)."§r§8>§r§7 ".$user->getName()."§r§8 [§b$".IntUtils::shortNumberRounded($user->getBalance())."§8]§r\n";
        }

        $ui->addLabel($str);

        $sender->sendForm($ui);
    }
}