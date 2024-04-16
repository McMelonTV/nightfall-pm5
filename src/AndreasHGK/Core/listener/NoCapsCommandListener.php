<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class NoCapsCommandListener implements Listener {

    /**
     * @param PlayerCommandPreprocessEvent $ev
     *
     * @priority highest
     */
    public function onCommand(PlayerCommandPreprocessEvent $ev) : void {
        $msg = $ev->getMessage();
        if(substr($msg, 0, 1) !== "/" && substr($msg, 0, 2) !== "./") {
            return;
        }

        $msgA = explode(" ", $msg);
        $msgA[0] = strtolower($msgA[0]);

        $msg = implode(" ", $msgA);

        $ev->setMessage($msg);
    }

}