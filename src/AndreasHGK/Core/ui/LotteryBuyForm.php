<?php
declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\lottery\Lottery;
use AndreasHGK\Core\user\UserManager;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;

class LotteryBuyForm {

    public static function sendTo(Player $sender) : void {
        $user = UserManager::getInstance()->getOnline($sender);

        $ui = new CustomForm(static function (Player $sender, ?array $data) {
            if($data === null || !isset($data["count"]) || !is_numeric($data["count"])) {
                return;
            }

            $count = (int)$data["count"];

            $user = UserManager::getInstance()->getOnline($sender);
            if($user === null){
                $sender->sendMessage("§r§c> §r§7Something went wrong while trying to buy tickets.");
                return;
            }

            if($count < 1 || $count > 100) {
                $sender->sendMessage("§r§c> §r§7Please enter a correct ticket count.");
                return;
            }

            $cost = $count * Lottery::TICKET_PRICE;

            if($user->getBalance() < $cost) {
                $sender->sendMessage("§r§c> §r§7You can't afford that.");
                return;
            }

            Lottery::getInstance()->buyTickets($user, $count);
            $user->takeMoney($cost);
            $sender->sendMessage("§r§b> §r§7You bought §b$count §r§7tickets for §b\${$cost}§r§7.");
        });
        $ui->setTitle("§bLottery §7- buy tickets");
        $ui->addLabel("§r§7Please select the amount of lottery tickets you want to buy. Each ticket costs §b$100000§r§7 and will add §b$75000 §r§7to the jackpot. Buying more tickets will increase your chance of winning. You can also §b/vote§r§7 to get a free ticket!");
        $ui->addSlider("§b> §r§7Ticket count", 1, 100, -1, -1, "count");

        $sender->sendForm($ui);
    }
}