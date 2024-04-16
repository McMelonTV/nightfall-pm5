<?php

declare(strict_types=1);

namespace AndreasHGK\Core\bounty;

class BountyManager {

    /** @var self */
    protected static $instance;

    public static function getInstance() : self {
        if(!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }


    /** @var Bounty[] */
    public $bounties = [];

    /**
     * @return Bounty[]
     */
    public function getBounties() : array {
        return $this->bounties;
    }

    /**
     * @param Bounty $bounty
     */
    public function setBounty(Bounty $bounty) : void {
        $this->bounties[$bounty->getPlayer()] = $bounty;
    }
}