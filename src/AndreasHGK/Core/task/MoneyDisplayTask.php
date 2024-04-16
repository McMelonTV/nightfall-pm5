<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\IntUtils;
use pocketmine\scheduler\Task;

class MoneyDisplayTask extends Task {

    public function getInterval() : int {
        return 25;
    }

    public function onRun() : void {
        $time = time();
        foreach(UserManager::getInstance()->getAllOnline() as $user){
            if($user->barChange+2 > $time) {
                continue;
            }

            $player = $user->getPlayer();
            if(!$player->isConnected()){
                continue;
            }
            $user->barChange = 0;
            $player->sendPopup("§r§b$".IntUtils::shortNumberRounded($user->getBalance()));
        }
    }
}