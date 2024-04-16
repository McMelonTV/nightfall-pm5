<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\auctionhouse\AuctionManager;
use pocketmine\scheduler\Task;

class AuctionExpireTask extends Task {

    public function getInterval() : int {
        return 250;
    }

    public function onRun() : void {
        $time = time();
        foreach(AuctionManager::getInstance()->getAllArray() as $auctionItem){
            //if($auctionItem->getSellTime() + 10 < $time){ //for dev purposes
            if($auctionItem->getSellTime() + 86400 < $time){
                AuctionManager::getInstance()->expire($auctionItem->getSeller(), $auctionItem->getId());
            }
        }
    }

}