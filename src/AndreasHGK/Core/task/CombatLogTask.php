<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\user\UserManager;
use pocketmine\scheduler\Task;

class CombatLogTask extends Task {

    public function getInterval() : int {
        return 20;
    }

    public function onRun() : void {
        $time = time();
        foreach(UserManager::getInstance()->getAllOnline() as $user){
            if($user->getLastHit()+10 < $time && $user->getLastHit() > 0){
                $user->clearLastHit();
                $user->getPlayer()->sendMessage("§r§b§l> §r§7You are no longer in combat. You can now logout safely.");
            }
        }
    }
}