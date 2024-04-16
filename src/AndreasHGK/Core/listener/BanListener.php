<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\BannedUserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;

class BanListener implements Listener {

    public function onPreLogin(PlayerPreLoginEvent $ev) : void {
        $info = $ev->getPlayerInfo();
        //if(BannedUserManager::getInstance()->isBannedCheckAll($info, $ev->getIp())){
        $ban = BannedUserManager::getInstance()->get($info->getUsername());
        if($ban === null) {
            return;
        }

        if($ban->isTempBan() && $ban->getBanExpire() < time()){
            BannedUserManager::getInstance()->unban($info->getUsername());
            return;
        }

        if($ban->isTempBan()){
            $ev->setKickReason(PlayerPreLoginEvent::KICK_REASON_PLUGIN, "§r§8[§bNF§8]\n§r§7You have been banned from the server!\n§r§7By: §b" . $ban->getBanner() . "§r§7\n§r§7Reason: §b" . $ban->getReason() . "\n§r§7Expiration date: §b" . ($ban->getBanExpire() > 0 ? date("d/m/Y", $ban->getBanExpire()) . " at " . date("h:i:s", $ban->getBanExpire()) : "never"));
        }else{
            $ev->setKickReason(PlayerPreLoginEvent::KICK_REASON_PLUGIN, "§r§8[§bNF§8]\n§r§7You have been banned from the server!\n§r§7By: §b" . $ban->getBanner() . "§r§7\n§r§7Reason: §b" . $ban->getReason() . "\n§r§7Expiration date: §b" . ($ban->getBanExpire() > 0 ? date("d/m/Y", $ban->getBanExpire()) . " at " . date("h:i:s", $ban->getBanExpire()) : "never"));
        }
    }
}