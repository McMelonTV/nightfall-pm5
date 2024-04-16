<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class PerformanceCommand extends Executor{

    public function __construct(){
        parent::__construct("performance", "do a server performance test", "/performance", Core::PERM_MAIN."command.performance");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $begin = microtime(true);
        $end = $begin+1;

        for($i = 0; microtime(true) < $end; ++$i){
            $rand = mt_rand(-9999, 9999);
        }

        $sender->sendMessage("§r§b§l>§r§7 The performance test scored §b".$i."§7.");
        return true;
    }

}