<?php
declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\lottery\Lottery;
use AndreasHGK\Core\utils\TimeUtils;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class LotteryForm {

    public static function sendTo(Player $sender) : void {
        $ui = new SimpleForm(static function (Player $sender, ?string $data) {
            if($data === null) {
                return;
            }

            if($data === "buy") LotteryBuyForm::sendTo($sender);
        });
        $ui->setTitle("§bLottery");

        $count  = Lottery::getInstance()->getTicketCount();

        $total = Lottery::getInstance()->getTotalMoney();

        $time = TimeUtils::intToTimeString(Lottery::getInstance()->getTime());
        $ui->setContent("§r§7Welcome to the lottery! Here you can buy tickets for §b$100000 each§r§7. The more you buy, the higher your chances of winning the jackpot.\n\n§r§b$count §r§7tickets have currently been sold.\n§r§7The jackpot for this lottery is §r§b\${$total}§r§7.\n§r§7The next draw is in §b{$time}§r§7.");

        $ui->addButton("Buy tickets", -1, "", "buy");
        $ui->addButton("Close", -1, "", "close");

        $sender->sendForm($ui);
    }
}