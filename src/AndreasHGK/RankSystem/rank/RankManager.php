<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\rank;

use AndreasHGK\RankSystem\RankSystem;
use AndreasHGK\RankSystem\utils\InvalidArgumentException;

class RankManager {

    /** @var Rank[] */
    private $ranks = [];
    /** @var RankInstance[] */
    private array $defaultRanks = [];

    /**
     * Load the ranks from data
     */
    public function load() : void {
        $this->ranks = RankSystem::getInstance()->getRankProvider()->loadRanks();
        foreach($this->ranks as $rank) {
            if($rank->isDefault()) {
                $this->defaultRanks[$rank->getId()] = RankInstance::create($rank, -1, true);
            }
        }
    }

    /**
     * @return RankInstance[]
     */
    public function getDefaultRanks() : array {
        return $this->defaultRanks;
    }

    /**
     * Get all the registered ranks
     *
     * @return Rank[]
     */
    public function getAll() : array {
        return $this->ranks;
    }

    /**
     * Get a rank with the given ID
     *
     * @param string $id
     * @return Rank|null
     */
    public function get(string $id) : ?Rank {
        return $this->ranks[$id] ?? null;
    }

    /**
     * Check if a rank exists
     *
     * @param string $id
     * @return bool
     */
    public function exists(string $id) : bool {
        return isset($this->ranks[$id]);
    }

    /**
     * Register a new rank to the rank manager
     *
     * @param Rank $rank
     * @param bool $force
     */
    public function register(Rank $rank, bool $force = false) : void {
        if($this->exists($rank->getId()) && !$force) throw new InvalidArgumentException("a rank with that ID has already been registered");
        $this->ranks[$rank->getId()] = $rank;
    }

}