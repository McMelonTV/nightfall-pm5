<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\ui\LeaderboardsForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LeaderboardCommand extends Executor{

    public function __construct(){
        parent::__construct("leaderboard", "see the leaderboards", "/leaderboard", Core::PERM_MAIN."command.leaderboard", ["top"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        LeaderboardsForm::sendTo($sender);
        return true;
    }

}