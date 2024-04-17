<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;

class NoCapsCommandListener implements Listener {

    /**
     * @param CommandEvent $ev
     *
     * @priority highest
     */
    public function onCommand(CommandEvent $ev) : void {
		//this might not be right
        $msg = $ev->getCommand();
        if(substr($msg, 0, 1) !== "/" && substr($msg, 0, 2) !== "./") {
            return;
        }

        $msgA = explode(" ", $msg);
        $msgA[0] = strtolower($msgA[0]);

        $msg = implode(" ", $msgA);

        $ev->setCommand($msg);
    }

}