<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\user\UserManager;
use pocketmine\scheduler\Task;
use function time;

class AFKTimerTask extends Task {

    public function getInterval() : int {
        return 35;
    }

    public function onRun() : void {
        $time = time();
        foreach(UserManager::getInstance()->getAllOnline() as $user){
			$player = $user->getPlayer();
			if(!$player->isConnected()){
				continue;
			}

            if($user->activity) {
                $user->updateLastActivity();
            }

            $user->activity = false;
            if($user->getLastActivity() + 300 < $time && !$user->isAFK()) {
                $user->setAFK();
                $player->sendMessage("§r§b§l>§r§7 You are now AFK.");
                continue;
            }

            if($user->isAFK() and $user->getLastActivity() + 600 < $time){
                $player->kick("§r§b§l>§r§7 You have been kicked for going AFK for too long.", false);
            }
        }
    }
}
