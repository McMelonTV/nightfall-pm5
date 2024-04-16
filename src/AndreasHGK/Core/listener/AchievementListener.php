<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\achievement\Achievement;
use AndreasHGK\Core\achievement\AchievementManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class AchievementListener implements Listener {

    /**
     * @priority Lowest
     *
     * @param EntityDamageEvent $ev
     */
    public function onDamage(EntityDamageEvent $ev) : void {
        $player = $ev->getEntity();
        if(!$player instanceof Player) {
            return;
        }

        if($ev->getCause() === EntityDamageEvent::CAUSE_FALL && $ev->getFinalDamage() >= 22){
            AchievementManager::getInstance()->tryAchieve(UserManager::getInstance()->get($player), Achievement::DAREDEVIL);
        }
    }

}