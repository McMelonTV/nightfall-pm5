<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;

class CEMineEvent {

    private $user;

    private $event;

    private $minedBlocks = [];

    private $priceModifier = 1;

    private $fusion = false;

    private $resourceBoost = 1;
    private $doMineStardust = false;

    private $autoRepair = false;

    private $xpboost = 0;

    public function getXPBoost() : float {
        return $this->xpboost;
    }

    public function setXPBoost(float $boost) : void {
        $this->xpboost = $boost;
    }

    public function getAutoRepair() : bool {
        return $this->autoRepair;
    }

    public function setAutoRepair(bool $repair) : void {
        $this->autoRepair = $repair;
    }

    public function getResourceBoost() : float {
        return $this->resourceBoost;
    }

    public function setResourceBoost(float $boost) : void {
        $this->resourceBoost = $boost;
    }

    public function doMineStardust() : bool {
        return $this->doMineStardust;
    }

    public function setMineStardust(bool $allow) : void {
        $this->doMineStardust = $allow;
    }

    public function getUser() : User {
        return $this->user;
    }

    public function getFusion() : bool {
        return $this->fusion;
    }

    public function setFusion(bool $fusion) : void {
        $this->fusion = $fusion;
    }

    public function getPriceModifier() : float {
        return $this->priceModifier;
    }

    public function setPriceModifier(float $modifier) : void {
        $this->priceModifier = $modifier;
    }

    public function getEvent() : BlockBreakEvent {
        return $this->event;
    }

    public function setEvent(BlockBreakEvent $ev) : void {
        $this->event = $ev;
    }

    /**
     * @return array|Block[]
     */
    public function getMinedBlocks() : array {
        return $this->minedBlocks;
    }

    public function setMinedBlocks(array $blocks) : void {
        $this->minedBlocks = $blocks;
    }

    public function __construct(BlockBreakEvent $ev) {
        $this->user = UserManager::getInstance()->getOnline($ev->getPlayer());
        $this->event = $ev;
        $this->minedBlocks = [$ev->getBlock()];
    }
}