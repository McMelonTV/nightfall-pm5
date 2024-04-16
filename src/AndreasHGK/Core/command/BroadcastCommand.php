<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class BroadcastCommand extends Executor{

    public function __construct(){
        parent::__construct("broadcast", "broadcast a message", "/broadcast", "nightfall.command.broadcast");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $msg = TextFormat::colorize(implode(" ", $args));
        Server::getInstance()->broadcastMessage($msg);
        return true;
    }

}