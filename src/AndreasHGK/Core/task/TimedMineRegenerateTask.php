<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\mine\MineManager;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class TimedMineRegenerateTask extends Task {

    public $time = 1200;

    public $lastTime = 0;

    public function getInterval() : int{
        return 10;
    }

    public function onRun() : void{
        if($this->lastTime === 0){
            $this->lastTime = microtime(true) - 1;
        }

        $diff = microtime(true) - $this->lastTime;
        $this->lastTime = microtime(true);

        $this->time -= $diff;

        if($this->time > 0){
            return;
        }
        $this->time = 1200;
        Server::getInstance()->getLogger()->info("regenerating all mines...");
        MineManager::getInstance()->regenAll();
    }
}