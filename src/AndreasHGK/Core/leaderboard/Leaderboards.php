<?php

declare(strict_types=1);

namespace AndreasHGK\Core\leaderboard;

use AndreasHGK\Core\Core;

class Leaderboards {

    public const KILLTOP = "kills";
    public const BALTOP = "balance";
    public const EARNTOP = "earnings";
    public const KDTOP = "kdr";
    public const MINETOP = "mine";
    public const BREAKTOP = "blockbreaks";

    /** @var self */
    public static $instance;

    /**
     * @return static
     */
    public static function getInstance() : self {
        if(!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    /** @var Leaderboard[] */
    protected $leaderboards = [];

    /**
     * @return Leaderboard[]
     */
    public function getLeaderboards() : array {
        return $this->leaderboards;  
    }

    /**
     * @param string $name
     * @return Leaderboard|null
     */
    public function getLeaderboard(string $name) : ?Leaderboard {
        return $this->leaderboards[$name] ?? null;
    }

    /**
     * @param Leaderboard $leaderboard
     */
    public function register(Leaderboard $leaderboard) : void {
        $this->leaderboards[$leaderboard->getName()] = $leaderboard;
    }


    /**
     * Setup the default leaderboards
     */
    public function setup() : void {
        $leaderboards = [
            new Leaderboard(self::BALTOP, 30),
            new Leaderboard(self::EARNTOP, 30),
            new Leaderboard(self::BREAKTOP, 30),
            new Leaderboard(self::MINETOP, 30),
            new Leaderboard(self::KILLTOP, 30),
            new Leaderboard(self::KDTOP, 30),
        ];
        foreach($leaderboards as $leaderboard) {
            $this->register($leaderboard);
        }

        Core::getInstance()->getScheduler()->scheduleRepeatingTask(new LeaderboardTask(), 6000);
    }

}