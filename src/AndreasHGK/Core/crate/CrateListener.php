<?php

declare(strict_types=1);

namespace AndreasHGK\Core\crate;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\task\CrateAnimation;
use AndreasHGK\Core\ui\CrateItemsInventory;
use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemIds;

class CrateListener implements Listener {

    public function onInteract(PlayerInteractEvent $ev) : void {
        $block = $ev->getBlock();
        $locations = CrateManager::getInstance()->getAllLocations();

        foreach($locations as $location){
            if($location->equals($block->getPos())){
                $crate = $location;
                break;
            }
        }

        if(!isset($crate)) {
            return;
        }

        $ev->cancel();
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);

        //key check
        $hand = $player->getInventory()->getItemInHand();
        if($hand->getId() === ItemIds::AIR || $hand->getNamedTag()->getTag("cratekey") === null){
            $player->sendMessage("§r§c§l> §r§7You require a key to open a crate.");
            return;
        }

        $id = $hand->getNamedTag()->getInt("cratekey");

        $crate = CrateManager::getInstance()->get($id);
        if($player->isSneaking()){
            CrateItemsInventory::sendTo($player, $crate);
            return;
        }

        $item = $crate->getRandomItem();

        $hand->pop();
        $player->getInventory()->setItemInHand($hand);
        if($item->doGiveItem()){
            $user->safeGive($item->getItem());
        }

        if(($rewardDollars = $item->getRewardDollars()) > 0){
            $user->addMoney($rewardDollars);
        }

        if(($rewardPrestige = $item->getRewardPrestige()) > 0){
            $user->setPrestigePoints($user->getPrestigePoints() + $rewardPrestige);
        }

        if(($callback = $item->getCallback()) !== null){
            $callback($user);
        }

        if($item->doSendMessage()){
            $player->sendMessage("§r§b§l> §r§7You received §b".$item->getItem()->getCount()."x ".($item->getCrateName() ?? $item->getItem()->getName())."§r§7 from the crate.");
        }

        Core::getInstance()->getScheduler()->scheduleRepeatingTask(new CrateAnimation($player, $block->getPos(), $item), 1);
    }
}