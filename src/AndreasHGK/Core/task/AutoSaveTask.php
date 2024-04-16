<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\auctionhouse\AuctionManager;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\user\BannedUserManager;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\vault\VaultManager;
use pocketmine\scheduler\Task;

class AutoSaveTask extends Task {

    public $time = 1200;

    public $lastTime = 0;

    public function getInterval() : int {
        return 10;
    }

    public function onRun() : void {
        if($this->lastTime === 0) $this->lastTime = microtime(true) - 1;

        $diff = microtime(true) - $this->lastTime;
        $this->lastTime = microtime(true);

        $this->time -= $diff;

        if($this->time > 0) return;
        $this->time = 1200;
        UserManager::getInstance()->saveAll();
        VaultManager::getInstance()->saveAll();
        MineManager::getInstance()->saveAll();
        AuctionManager::getInstance()->saveAll();
        BannedUserManager::getInstance()->saveAll();
        PlotManager::getInstance()->saveAll();
        GangManager::getInstance()->saveAll();
    }
}
