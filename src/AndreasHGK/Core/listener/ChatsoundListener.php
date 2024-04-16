<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\world\sound\PopSound;

class ChatsoundListener implements Listener{

    /**
     * @param PlayerChatEvent $ev
     *
     * @priority HIGH
     */
    public function onChat(PlayerChatEvent $ev) : void {
        $player = $ev->getPlayer();
        $player->getNetworkSession()->sendDataPacket(((new PopSound(2))->encode($player->getPosition()->asVector3()))[0]);
    }
}