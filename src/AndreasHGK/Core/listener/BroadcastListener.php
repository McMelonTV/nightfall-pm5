<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\task\BroadcastTask;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

class BroadcastListener implements Listener {

    /**
     * @param PlayerChatEvent $_
     *
     * @priority Monitor
     */
    public function onChat(PlayerChatEvent $_) : void {
        BroadcastTask::getInstance()->addMessage();
    }
}