<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\user\UserManager;
use pocketmine\scheduler\Task;

class AntispamTask extends Task {

    public $lastTime = 0;

    public function getInterval() : int {
        return 9;
    }

    public function onRun() : void {
        if($this->lastTime === 0) $this->lastTime = microtime(true) - 1;

        $diff = microtime(true) - $this->lastTime;
        $this->lastTime = microtime(true);

        foreach(UserManager::getInstance()->getAllOnline() as $user){
            $user->setSpamScore(max(0, $user->getSpamScore() - 75 * $diff));
        }
    }
}