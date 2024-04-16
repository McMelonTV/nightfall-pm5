<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;

class HungerListener implements Listener{

    public function onHunger(PlayerExhaustEvent $ev) : void {
        $ev->cancel();
        $ev->getPlayer()->getHungerManager()->setFood(19.9999);
    }
}