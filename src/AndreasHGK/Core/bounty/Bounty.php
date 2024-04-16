<?php

declare(strict_types=1);

namespace AndreasHGK\Core\bounty;

class Bounty {

    /** @var string */
    protected $player;
    /** @var int */
    protected $bounty;

    /**
     * @return string
     */
    public function getPlayer() : string {
        return $this->player;
    }

    /**
     * @return int
     */
    public function getBounty() : int {
        return $this->bounty;
    }

    /**
     * @param int $bounty
     */
    public function setBounty(int $bounty) : void {
        $this->bounty = $bounty;
    }

    public function __construct(string $player, int $bounty) {
        $this->player = strtolower($player);
        $this->bounty = $bounty;
    }
}