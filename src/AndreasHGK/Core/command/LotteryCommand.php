<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\ui\LotteryForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LotteryCommand extends Executor{

    public function __construct(){
        parent::__construct("lottery", "open the lottery", "/lottery", "nightfall.command.lottery");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) return false;

        LotteryForm::sendTo($sender);

        return true;
    }
}