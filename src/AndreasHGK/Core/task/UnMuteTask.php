<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\user\UserManager;
use pocketmine\scheduler\Task;
use function time;

class UnMuteTask extends Task {

    public function getInterval() : int {
        return 20;
    }

    public function onRun() : void {
        $time = time();
        foreach(UserManager::getInstance()->getAllOnline() as $user){
            if(!$user->isMuted()) {
                continue;
            }

            if($user->getMuteExpire() === -1) {
                continue;
            }

            if($user->getMuteExpire() <= $time){
                $user->setMuted(false);
                $user->setMuteExpire(-1);
                $user->getPlayer()->sendMessage("§r§b§l> §r§7You are no longer muted.");
            }
        }
    }
}