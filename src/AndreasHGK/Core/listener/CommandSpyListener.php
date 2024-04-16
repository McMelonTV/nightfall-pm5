<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class CommandSpyListener implements Listener {

    /**
     * @param CommandEvent $ev
     *
     * @priority LOWEST
     */
    public function onCommand(CommandEvent $ev) : void {
        $message = $ev->getCommand();

        $evPlayer = $ev->getSender();
        if(!$evPlayer instanceof Player){
            return;
        }
        $name = $evPlayer->getName();

        Server::getInstance()->getLogger()->info("§8[C-SPY] §r§9".$name." §8>§r§7 /".$message);
        foreach (UserManager::getInstance()->getAllOnline() as $user){
            $player = $user->getPlayer();
            if($user->getCommandSpy() && $player !== $evPlayer){
                $player->sendMessage("§9".$name." §8>§r§7 /".$message."⛏");
            }
        }
    }
}