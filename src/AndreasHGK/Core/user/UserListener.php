<?php

declare(strict_types=1);

namespace AndreasHGK\Core\user;

use AndreasHGK\Core\vault\VaultManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class UserListener implements Listener {

    /**
     * @param PlayerLoginEvent $ev
     *
     * @priority LOWEST
     */
    public function onPlayerLogin(PlayerLoginEvent $ev) : void{
        $player = $ev->getPlayer();

        $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
        $vec = $defaultWorld->getSpawnLocation()->add(0.5, 0, 0.5);
        $player->teleport(new Position($vec->x, $vec->y, $vec->z, $defaultWorld));

        $user = UserManager::getInstance()->get($player, true);
        if(($nick = $user->getNick()) !== ""){
            $player->setDisplayName(TextFormat::colorize($nick));
        }

        VaultManager::getInstance()->load($user);

        $user->setJoinTime(time());

        $networkSession = $player->getNetworkSession();
        $user->registerIP($networkSession->getIp());

        $extraData = $networkSession->getPlayerInfo()->getExtraData();

        $user->registerClientId((string)$extraData["ClientRandomId"]);
        $user->registerDeviceId($extraData["DeviceId"]);
    }

    public function onToggleFly(PlayerToggleFlightEvent $ev) : void{
        $player = $ev->getPlayer();

        $user = UserManager::getInstance()->getOnline($player);
        if($ev->isFlying() && !$user->canFly()){
            $ev->cancel();
            $user->setFly(false);
        }
    }

    /**
     * @param PlayerQuitEvent $ev
     *
     * @priority HIGHEST
     */
    public function onLeave(PlayerQuitEvent $ev) : void {
        $user = UserManager::getInstance()->getOnline($ev->getPlayer());
        if($user === null) {
            return;
        }

        $user->getPlayer()->getEffects()->clear();

        VaultManager::getInstance()->save(VaultManager::getInstance()->get($user));
        VaultManager::getInstance()->unload($user);

        UserManager::getInstance()->save($user);
        UserManager::getInstance()->unload($user);
    }
}