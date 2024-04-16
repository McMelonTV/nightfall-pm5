<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\plot\PlotManager;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;

class WorldChangeListener implements Listener{

    private ?World $defaultWorld;

    public function __construct(){
        $this->defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
    }

    public function onEntityTeleport(EntityTeleportEvent $ev) : void{
        $player = $ev->getEntity();
        if(!$player instanceof Player){
            return;
        }

        $world = $ev->getTo()->getWorld();
        if($world === $ev->getFrom()->getWorld()){
            return;
        }

        if($world === $this->defaultWorld){
            $player->setViewDistance(6);
        }elseif(($displayName = $world->getDisplayName()) === Core::PVPMINEWORLD || $displayName === PlotManager::$plotworld){
            $player->setViewDistance(8);
        }elseif(MineManager::getInstance()->isMineWorld($world)){
            $player->setViewDistance(4);
        }
    }
}