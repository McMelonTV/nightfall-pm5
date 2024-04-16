<?php

namespace AndreasHGK\Core\listener;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;

class NoStackedItemsListener implements Listener{

    /**
     * @param InventoryTransactionEvent $ev
     * @priority LOW
     */
    public function onClick(InventoryTransactionEvent $ev) : void {
        foreach($ev->getTransaction()->getActions() as $action){
            if($action instanceof SlotChangeAction){
                $inv = $action->getInventory();
                $item = $inv->getItem($action->getSlot());
                if($item->getCount() > $item->getMaxStackSize()){
                    $inv->setItem($action->getSlot(), $item->setCount($item->getMaxStackSize()));
                    $ev->cancel();
                }
            }
        }
    }
}