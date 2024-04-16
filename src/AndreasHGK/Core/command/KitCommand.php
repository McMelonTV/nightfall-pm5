<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\ui\KitForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class KitCommand extends Executor{

    public function __construct(){
        parent::__construct("kit", "get a kit", "/kit", Core::PERM_MAIN."command.kit");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        KitForm::sendTo($sender);
        return true;
    }

}