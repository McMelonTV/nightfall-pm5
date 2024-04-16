<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\ui\BalTopForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class BalTopCommand extends Executor {

    public function __construct() {
        parent::__construct("baltop", "see the leaderboard for money", "/baltop", Core::PERM_MAIN."command.baltop", ["topmoney"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if(!$sender instanceof Player) return false;
        BalTopForm::sendTo($sender);
        return true;
    }
}