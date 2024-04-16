<?php

declare(strict_types=1);

namespace AndreasHGK\Core\leaderboard;

use AndreasHGK\Core\Core;
use pocketmine\scheduler\Task;

class LeaderboardTask extends Task {

    public function onRun() : void {
        $l = Leaderboards::getInstance();
        $data = [];

        foreach($l->getLeaderboards() as $board) {
            $data[$board->getName()] = $board->asData();
        }

        Core::getInstance()->getServer()->getAsyncPool()->submitTask(new AsyncLeaderboardRegenerateTask(serialize($data), Core::getInstance()->getDataFolder()));
    }

}