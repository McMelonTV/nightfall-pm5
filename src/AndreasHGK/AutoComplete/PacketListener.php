<?php

declare(strict_types=1);

namespace AndreasHGK\AutoComplete;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class PacketListener implements Listener {

    public function onPacketSend(DataPacketSendEvent $ev) : void {
        return;
        $packets = $ev->getPackets();
        foreach($packets as $key => $packet){
            if(!$packet instanceof AvailableCommandsPacket) continue;
            AutoComplete::getInstance()->updatePacketFor($ev->getTargets()[0]->getPlayer(), $packet);
            \Closure::bind(function () use($key, $packet) {
                $this->packets[$key] = $packet;
            }, $ev, DataPacketSendEvent::class)();
        }
    }

}