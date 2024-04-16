<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;

class AutoVoteCheckTask extends Task {

    public $lastRun = 0;

    public function getInterval() : int {
        return 8;
    }

    public function onRun() : void {
        if($this->lastRun + 20 > microtime(true)){
            return;
        }

        $this->lastRun = microtime(true);

        $players = [];
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $players[] = $player->getName();
        }

        Server::getInstance()->getAsyncPool()->submitTask(new CheckVoteTask("fE6vi81D4FQVY7Qx4cnWnM0ZI0MrWGP95", $players));
    }

}