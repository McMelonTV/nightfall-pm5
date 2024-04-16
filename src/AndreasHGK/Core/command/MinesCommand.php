<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\ui\MinesForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MinesCommand extends Executor{

    public function __construct(){
        parent::__construct("mines", "choose a mine", "/mines", "nightfall.command.mines");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if(!$sender instanceof Player) {
            $sender->sendMessage("§r§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        MinesForm::sendTo($sender);
        return true;
    }
}