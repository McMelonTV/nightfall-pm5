<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\user\UserManager;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class DelayedCommandTask extends Task {

    public $player;

    public $msg;

    public function __construct(Player $player, string $msg){
        $this->player = $player;
        $this->msg = $msg;
    }

    public function onRun() : void {
        $user = UserManager::getInstance()->getOnline($this->player);
        if($user === null) {
            return;
        }

        if(!$user->isWaitingForCommand()) {
            return;
        }

        $newMsg = explode(" ", $this->msg);
        $newMsg[0] = str_replace("/", "", $newMsg[0]);
        $newMsg = implode(" ", $newMsg);

        $user->finishCommandDelay();

        Server::getInstance()->getCommandMap()->dispatch($this->player, $newMsg);
    }
}