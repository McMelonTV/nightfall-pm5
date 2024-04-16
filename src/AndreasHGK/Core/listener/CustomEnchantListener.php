<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\enchant\CustomEnchantIds;
use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\ItemInterface;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Armor;

class CustomEnchantListener implements Listener{

    public function onTransaction(InventoryTransactionEvent $ev) : void{
        $customEnchantsManager = CustomEnchantsManager::getInstance();
        $player = $ev->getTransaction()->getSource();
        $effects = $player->getEffects();
        foreach($ev->getTransaction()->getActions() as $action){
            if(!$action instanceof SlotChangeAction){
                continue;
            }

            if(!$action->getInventory() instanceof ArmorInventory){
                continue;
            }

            $sourceItem = $action->getSourceItem();
            $targetItem = $action->getTargetItem();
            if($sourceItem instanceof Armor){
                $interface = ItemInterface::fromItem($sourceItem);

                $customEnchants = $interface->getCustomEnchants();

                $ench = $customEnchantsManager->get(CustomEnchantIds::HEALTH);
                if($interface->hasEnchantment($ench)){
                    $player->setMaxHealth($player->getMaxHealth() - ($customEnchants[$ench->getId()]->getLevel() * 2));
                }

                if($sourceItem->getArmorSlot() === 3){
                    $ench = $customEnchantsManager->get(CustomEnchantIds::LEAPER);
                    if($interface->hasEnchantment($ench)){
                        $effects->remove(VanillaEffects::JUMP_BOOST());
                    }

                    $ench = $customEnchantsManager->get(CustomEnchantIds::RUNNER);
                    if($interface->hasEnchantment($ench)){
                        $effects->remove(VanillaEffects::SPEED());
                    }
                }

                if($sourceItem->getArmorSlot() === 0){
                    $ench = $customEnchantsManager->get(CustomEnchantIds::NIGHT_VISION);
                    if($interface->hasEnchantment($ench)){
                        $effects->remove(VanillaEffects::NIGHT_VISION());
                    }
                }

                if($sourceItem->getArmorSlot() === 1){
                    $ench = $customEnchantsManager->get(CustomEnchantIds::TANK);
                    if($interface->hasEnchantment($ench)){
                        $effects->remove(VanillaEffects::SLOWNESS());
                    }
                }
            }

            if($targetItem instanceof Armor){
                $interface = ItemInterface::fromItem($targetItem);
                $customEnchants = $interface->getCustomEnchants();

                $ench = $customEnchantsManager->get(CustomEnchantIds::HEALTH);
                if($interface->hasEnchantment($ench)){
                    $player->setMaxHealth($player->getMaxHealth() + ($customEnchants[$ench->getId()]->getLevel() * 2));
                }

                if($targetItem->getArmorSlot() === 3){
                    $ench = $customEnchantsManager->get(CustomEnchantIds::LEAPER);
                    if($interface->hasEnchantment($ench)){
                        $effects->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 2147483647, $customEnchants[$ench->getId()]->getLevel() - 1, false));
                    }

                    $ench = $customEnchantsManager->get(CustomEnchantIds::RUNNER);
                    if($interface->hasEnchantment($ench)){
                        $effects->add(new EffectInstance(VanillaEffects::SPEED(), 2147483647, $customEnchants[$ench->getId()]->getLevel() - 1, false));
                    }
                }

                if($targetItem->getArmorSlot() === 0){
                    $ench = $customEnchantsManager->get(CustomEnchantIds::NIGHT_VISION);
                    if($interface->hasEnchantment($ench)){
                        $effects->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 2147483647, 1, false));
                    }
                }

                if($targetItem->getArmorSlot() === 1){
                    $ench = $customEnchantsManager->get(CustomEnchantIds::TANK);
                    if($interface->hasEnchantment($ench)){
                        $effects->add(new EffectInstance(VanillaEffects::SLOWNESS(), 2147483647, 2, false));
                    }
                }
            }
        }
    }
}