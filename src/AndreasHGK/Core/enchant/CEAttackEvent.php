<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\event\entity\EntityDamageByEntityEvent;

class CEAttackEvent {

    private $event;

    private $knockback = 1;

    private $deflect = false;

    private $toughness = 0;

    private $autoRepair = false;

    public function __construct(EntityDamageByEntityEvent $ev) {
        $this->event = $ev;
        $this->knockback = $ev->getKnockBack();
    }

    public function getToughness() : float {
        return $this->toughness;
    }

    public function setToughness(float $toughness) : void {
        $this->toughness = $toughness;
    }

    public function getDeflect() : bool {
        return $this->deflect;
    }

    public function setDeflect(bool $bool) : void {
        $this->deflect = $bool;
    }

    public function getKnockback() : float {
        return $this->knockback;
    }

    public function setKnockBack(float $knockback) : void {
        $this->knockback = $knockback;
    }

    public function getEvent() : EntityDamageByEntityEvent {
        return $this->event;
    }

    public function setEvent(EntityDamageByEntityEvent $ev) : void {
        $this->event = $ev;
    }

    public function isAutoRepair() : bool{
        return $this->autoRepair;
    }

    public function setAutoRepair(bool $autoRepair) : void{
        $this->autoRepair = $autoRepair;
    }
}