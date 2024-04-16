<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\enchant\CEAttackEvent;
use AndreasHGK\Core\enchant\CustomEnchant;
use AndreasHGK\Core\enchant\CustomEnchantIds;
use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerAttackListener implements Listener {

    /**
     * @param EntityDamageByEntityEvent $ev
     *
     * @handleCancelled false
     * @priority High
     */
    public function onHit(EntityDamageByEntityEvent $ev) : void{
        if($ev->getModifier(EntityDamageEvent::MODIFIER_PREVIOUS_DAMAGE_COOLDOWN) !== 0.0){
            $ev->cancel();
            return;
        }
        $attacker = $ev->getDamager();
        if(!$attacker instanceof Player){
            return;
        }

        $handInterface = ItemInterface::fromItem($attacker->getInventory()->getItemInHand());
        $user = UserManager::getInstance()->getOnline($attacker);

        $durable = $handInterface->isDurable();
        if($durable && $handInterface->getMaxDamage() <= $handInterface->getDamage() + 1){
            $ev->cancel();
            $user->sendTip("§r§8[§bNF§8]\n§r§7Your weapon has no durability left.\n§r§7Repair it using §b/forge§r§7.");

            return;
        }

        $customEnchants = $handInterface->getCustomEnchants();
        $attackEvent = new CEAttackEvent($ev);
        foreach($customEnchants as $customEnchant){
            $customEnchant->onHit($attackEvent, $handInterface->getItem(), true);
        }

        $dmg = $ev->getBaseDamage();
        $target = $ev->getEntity();
        if(!$target instanceof Player){
            return;
        }

        if($handInterface->hasEnchantment(CustomEnchantsManager::getInstance()->get(CustomEnchantIds::DAMAGE))){
            $ev->setModifier($dmg / 25 * $handInterface->getCustomEnchants()[CustomEnchantIds::DAMAGE]->getLevel(), 10);
            //$dmg = $dmg + $dmg / 30 * $interface->getCustomEnchants()[CustomEnchantIds::DAMAGE]->getLevel();
        }

        if($handInterface->hasEnchantment(CustomEnchantsManager::getInstance()->get(CustomEnchantIds::AERIAL)) and !$target->isOnGround()){
            $ev->setModifier($dmg / 40 * $handInterface->getCustomEnchants()[CustomEnchantIds::AERIAL]->getLevel(), 11);
        }

        if($handInterface->hasEnchantment(CustomEnchantsManager::getInstance()->get(CustomEnchantIds::DEATHBRINGER))){
            if(mt_rand(0, 100) < 10+3*$handInterface->getCustomEnchants()[CustomEnchantIds::DEATHBRINGER]->getLevel()){
                $ev->setModifier($dmg * (mt_rand(35, 65) / 100), 12);
                //$dmg = $dmg + $dmg * (mt_rand(40, 75) / 100);
            }
        }

        if($durable && $handInterface->doUseDurability()){
            $handInterface->applyDamage(1);
            $handInterface->recalculateDamage();
            $handInterface->recalculateLore();
            $handInterface->saveStats();
            $attacker->getInventory()->setItemInHand($handInterface->getItem());
        }

        $targetUser = UserManager::getInstance()->getOnline($target);
        $targetInventory = $target->getInventory();
        $armorInventory = $target->getArmorInventory();
        $attackEvent = new CEAttackEvent($ev);
        foreach($armorInventory->getContents(true) as $slot => $item){
            $interface = ItemInterface::fromItem($item);
            if($durable && $interface->getMaxDamage() <= $interface->getDamage() + 1){
                $targetUser->sendTip("§r§8[§bNF§8]\n§r§7Your armor has no durability left.\n§r§7Repair it using §b/forge§r§7.");

                $targetInventory->addItem($item);
                $armorInventory->setItem($slot, ItemFactory::air());
                continue;
            }

            foreach($interface->getCustomEnchants() as $customEnchant){
                $customEnchant->onHit($attackEvent, $item, false);
            }

            if($durable && $interface->doUseDurability()){
                $interface->applyDamage(1);
                $interface->recalculateDamage();
                $interface->recalculateLore();
                $interface->saveStats();

                $armorInventory->setItem($slot, $interface->getItem());
            }
        }

        if(ItemUtils::getType($attacker->getInventory()->getItemInHand()) === CustomEnchant::TYPE_SWORD){
            $ev->setModifier(- ($dmg / 50) * $attackEvent->getToughness(), 12);
            //$dmg = $dmg - ($dmg / 65) * $attackEvent->getToughness();
        }

        $targetHand = $targetInventory->getItemInHand();
        $interface = ItemInterface::fromItem($targetHand);
        $attackEvent = new CEAttackEvent($ev);
        foreach($interface->getCustomEnchants() as $customEnchant){
            $customEnchant->onHit($attackEvent, $targetHand, false);
        }

        foreach($handInterface->getCustomEnchants() as $customEnchant){
            $customEnchant->onHit2($attackEvent);
        }

        $ev->setBaseDamage($dmg);

        /*if($attackEvent->getDeflect()){
            $ev->cancel();
            $attacker->setHealth($attacker->getHealth() - $ev->getFinalDamage() * (mt_rand(10, 25) / 100));
        }*/

        $ev->setKnockBack($attackEvent->getKnockback());
    }

    /**
     * @param EntityDamageEvent $ev
     *
     * @priority HIGHEST
     */
    public function onDeath(EntityDamageEvent $ev) : void {
        $target = $ev->getEntity();
        if (!$target instanceof Player) {
            return;
        }

        if ($ev->getFinalDamage() < $ev->getEntity()->getHealth()) {
            return;
        }

        $user = UserManager::getInstance()->getOnline($target);
        if($user === null){
            return;
        }

        if ($user->getLastHitter() === "" || $user->getLastHit() + 20 < time()) {
            return;
        }

        $attacker = Server::getInstance()->getPlayerExact($user->getLastHitter());

        $itemInHand = $attacker->getInventory()->getItemInHand();
        $interface = ItemInterface::fromItem($itemInHand);
        foreach($interface->getCustomEnchants() as $customEnchant){
            $customEnchant->onKill($ev, $attacker, $itemInHand, true);
        }

        $armorInventory = $target->getArmorInventory();
        foreach($armorInventory->getContents(true) as $slot => $item){
            $interface = ItemInterface::fromItem($item);

            foreach($interface->getCustomEnchants() as $customEnchant){
                $customEnchant->onKill($ev, $attacker, $item, false);
            }
        }
    }
}