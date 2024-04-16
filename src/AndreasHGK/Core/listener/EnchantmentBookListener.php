<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\item\EnchantmentBook;
use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\IntUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

class EnchantmentBookListener implements Listener{

    /**
     * @param InventoryTransactionEvent $ev
     *
     * @handleCancelled false
     * @priority HIGH
     */
    public function onClick(InventoryTransactionEvent $ev) : void {
        foreach($ev->getTransaction()->getActions() as $action){
            if($action instanceof SlotChangeAction){
                $sourceItem = $action->getSourceItem();
                $targetItem = $action->getTargetItem();
                if($targetItem->getId() === ItemIds::AIR || $sourceItem->getId() === ItemIds::AIR) {
                    continue;
                }

                if($sourceItem->getId() === ItemIds::ENCHANTED_BOOK && $targetItem->getId() === ItemIds::ENCHANTED_BOOK){
                    if(!isset($book1)){
                        $book1 = $sourceItem;
                        $book2 = $targetItem;
                    }

                    if(isset($slot1)){
                        $slot2 = $action->getSlot();
                        $inv2 = $action->getInventory();
                    }else{
                        $slot1 = $action->getSlot();
                        $inv1 = $action->getInventory();
                    }
                }elseif($sourceItem->getId() === ItemIds::ENCHANTED_BOOK){
                    $book = $sourceItem;
                    $apply = $targetItem;
                    $applySlot = $action->getSlot();
                    $applyInv = $action->getInventory();
                }elseif($targetItem->getId() === ItemIds::ENCHANTED_BOOK){
                    $bookSlot = $action->getSlot();
                    $bookInv = $action->getInventory();
                }
            }
        }
        $player = $ev->getTransaction()->getSource();

        if(isset($book1) && isset($book2)){
            if(!isset($slot1) || !isset($slot2)){
                return;
            }
            if($book1->getNamedTag()->getTag("nfenchantid") === null) {
                return;
            }

            if($book2->getNamedTag()->getTag("nfenchantid") === null) {
                return;
            }

            $ench1 = CustomEnchantsManager::getInstance()->get($book1->getNamedTag()->getInt("nfenchantid"));
            if($ench1 === null) {
                return;
            }

            $ench1->setLevel($book1->getNamedTag()->getInt("nfenchantlvl"));

            $ench2 = CustomEnchantsManager::getInstance()->get($book2->getNamedTag()->getInt("nfenchantid"));
            if($ench2 === null) {
                return;
            }

            $ench2->setLevel($book2->getNamedTag()->getInt("nfenchantlvl"));
            if($ench1->getId() !== $ench2->getId()){
                $player->sendMessage("§r§l§c> §r§7You can only combine books with the same enchantment type.");
                return;
            }

            if($ench1->getLevel() !== $ench2->getLevel()){
                $player->sendMessage("§r§l§c> §r§7You can only combine books with the same enchantment level.");
                return;
            }

            if($ench1->getMaxLevel() <= $ench1->getLevel() || $ench2->getMaxLevel() <= $ench2->getLevel()){
                $player->sendMessage("§r§l§c> §r§7This enchantment already reached the max level.");
                return;
            }

            $book2->setNamedTag($book2->getNamedTag()->setInt("nfenchantlvl", $ench2->getLevel()+1));
            $book2->setCustomName("§r".EnchantmentUtils::rarityColor($ench2->getRarity()).$ench2->getName().($ench2->getMaxLevel() > 1 ? " ".IntUtils::toRomanNumerals($ench2->getLevel()+1) : "")." §r§fBook");

            $ench2->setLevel($ench2->getLevel()+1);

            $book2->setLore([EnchantmentBook::lore($ench2)]);

            if($slot1 === 0){
                $inv1->setItem($slot1, ItemFactory::air());
                $inv2->setItem($slot2, $book2);
            }elseif($slot2 === 0){
                $inv2->setItem($slot2, ItemFactory::air());
                $inv1->setItem($slot1, $book2);
            }else{
                //pocket edition?
                $inv1->setItem($slot1, ItemFactory::air());
                $inv2->setItem($slot2, $book2);
            }

            $ev->cancel();
            $player->sendMessage("§r§l§b> §r§7You successfully leveled up the enchantment.");

            $sound = LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_NOTE, $player->getPosition(), (15 << 8) | 255);

            $player->getNetworkSession()->sendDataPacket($sound);
            return;
        }elseif(isset($book) && isset($apply)){
            if(!isset($applySlot) || !isset($bookSlot)){
                return;
            }
            if($book->getNamedTag()->getTag("nfenchantid") === null) {
                return;
            }

            $enchant = CustomEnchantsManager::getInstance()->get($book->getNamedTag()->getInt("nfenchantid"));
            if($enchant === null) {
                return;
            }

            $enchant->setLevel($book->getNamedTag()->getInt("nfenchantlvl"));
            if(ItemUtils::getType($apply) === "") {
                $player->sendMessage("§r§l§c> §r§7Cannot apply this enchantment to that item.");
                return;
            }

            if(!ItemUtils::isCompatible($enchant->getCompatible(), $apply)){
                $player->sendMessage("§r§l§c> §r§7That item is not compatible with the enchantment.");
                return;
            }

            $interface = ItemInterface::fromItem($apply);
            if($interface->hasEnchantment($enchant)){
                $appliedEnchant = $interface->getCustomEnchants()[$enchant->getId()];
                if($appliedEnchant->getLevel() > $enchant->getLevel()){
                    $player->sendMessage("§r§l§c> §r§7That item already has a higher level of the enchantment applied.");
                    return;
                }

                if($appliedEnchant->getLevel() === $enchant->getLevel()){
                    if($appliedEnchant->getLevel() >= $appliedEnchant->getMaxLevel()){
                        $player->sendMessage("§r§l§c> §r§7That item already has the highest level of the enchantment.");
                        return;
                    }

                    $enchant->setLevel($enchant->getLevel()+1);
                }
            }

            $price = $enchant->getApplyPrice();

            if(!$price->canAfford($player)){
                $player->sendMessage("§r§l§c> §r§7You can't afford the §b{$price->getXPLevels()} XP levels §r§7required to apply this enchant.");
                return;
            }

            $interface->enchant($enchant);
            $interface->recalculateLore();
            $interface->saveStats();
            $apply = $interface->getItem();

            $applyInv->setItem($applySlot, ItemFactory::air());
            $bookInv->setItem($bookSlot, $apply);

            $price->pay($player);

            $ev->cancel();
            $player->sendMessage("§r§l§b> §r§7You successfully applied §r§b".$enchant->getName()." ".$enchant->getLevel()."§r§7 to the item.");
            return;
        }
    }
}