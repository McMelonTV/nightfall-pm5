<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\listener;

use AndreasHGK\RankSystem\RankSystem;
use pocketmine\event\Listener;

class BaseListener implements Listener {

    /** @var RankSystem */
    protected $plugin;

    public function __construct() {
        $this->plugin = RankSystem::getInstance();
    }

}