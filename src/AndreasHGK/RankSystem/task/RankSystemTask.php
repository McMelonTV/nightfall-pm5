<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\task;

use pocketmine\scheduler\Task;

abstract class RankSystemTask extends Task {

    /**
     * Get the time between each repetition of the task
     * If -1, the task will not repeat
     *
     * @return int
     */
    public function getRepeat() : int {
        return -1;
    }

    /**
     * Get the delay for the task
     * -1 means no delay
     *
     * @return int
     */
    public function getDelay() : int {
        return -1;
    }

}