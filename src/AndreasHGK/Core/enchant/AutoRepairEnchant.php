<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\ItemInterface;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\Item;
use pocketmine\player\Player;

class AutoRepairEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::GROUP_ALL];
    }

    public function getDescription() : string {
        return "Repair the item with 1 obsidian shard whenever it's durability passes below 50%.";
    }

    public function getName() : string {
        return "Autorepair";
    }

    public function getId() : int {
        return CustomEnchantIds::AUTO_REPAIR;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::ALL;
    }

    public function getRarity() : int {
        return self::RARITY_VERY_RARE;
    }

    public function getMaxLevel() : int {
        return 1;
    }

    //events

    public function onMine(CEMineEvent $ev) : void{
        $ev->setAutoRepair(true);
    }

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        $interface = ItemInterface::fromItem($item);
        if($interface->getDamage()+$interface->getMaxDamage() > 0.5){
            $obsidianShard = clone CustomItemManager::getInstance()->get(CustomItem::OBSIDIANSHARD)->getItem();
            if($isAttacker){
                $player = $ev->getEvent()->getDamager();
            }else{
                /** @var Player $player */
                $player = $ev->getEvent()->getEntity();
            }

            if(!$player->getInventory()->contains($obsidianShard)){
                return;
            }

            $player->getInventory()->removeItem($obsidianShard);
            $interface->applyDamage(min(-400, $interface->getDamage()));
        }
    }
}