<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\task;

use AndreasHGK\Core\user\UserManager;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;

class RankExpireTask extends RankSystemTask {

    private UserManager $userManager;

    public function __construct() {
        $this->userManager = UserManager::getInstance();
    }

    public function getRepeat() : int {
        return 1800;
    }

    public function onRun() : void {
        $time = time();
        foreach($this->userManager->getAllOnline() as $user) {
            $ranks = $user->getRankComponent();

            foreach($ranks->getRanks() as $rank) {
                if($rank->isPermanent()) continue;
                if($rank->getExpire() > $time) continue;

                $ranks->removeRank($rank->getRank()->getId());
                $user->getPlayer()->sendMessage("§r§a§l> §r§7Your §r§a{$rank->getRank()->getName()}§r§7 rank has now expired.");
                $user->getPlayer()->getNetworkSession()->sendDataPacket(LevelSoundEventPacket::create(LevelSoundEvent::BEACON_DEACTIVATE, $user->getPlayer()->getPosition(), 0, "", false, false));
            }

        }
    }

}