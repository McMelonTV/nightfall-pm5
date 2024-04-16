<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class SoftrestartCommand extends Executor{

    public function __construct(){
        parent::__construct("softrestart", "softrestart", "/softrestart", "nightfall.command.softrestart");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(isset($args[0]) && is_numeric($args[0])){
            $time = (int) $args[0];
        }else{
            $time = 10;
        }
        Core::$restart = $time;
        $sender->sendMessage("§b§l> §r§7Enabled softrestart.");
        return true;
    }
}