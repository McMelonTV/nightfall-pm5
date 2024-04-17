<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\item\Repairable;
use AndreasHGK\Core\item\RepairResource;
use AndreasHGK\Core\ItemInterface;
use muqsit\invmenu\inventory\InvMenuInventory;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\sound\AnvilUseSound;

class ForgeRepairInventory {

    public static function sendTo(Player $sender) : void {
        $menu = InvMenu::create(InvMenu::TYPE_HOPPER);
        $menu->setName("§8Item Forge");
        $menu->setListener(static function (InvMenuTransaction $ts) use ($menu){
            $player = $ts->getPlayer();
            $itemClicked = $ts->getOut();
            $itemClickedWith = $ts->getIn();
            $action = $ts->getAction();
            $inv = $menu->getInventory();

            if($itemClicked->getTypeId() === VanillaItems::AIR()->getTypeId() && $action->getSlot() === 4) {
                return new InvMenuTransactionResult(true);
            }

            if($action->getSlot() === 4){
                if($itemClickedWith->getTypeId() !== VanillaItems::AIR()->getTypeId()) {
                    return new InvMenuTransactionResult(true);
                }

                $interface1 = ItemInterface::fromItem($inv->getItem(0));
                $interface2 = ItemInterface::fromItem($inv->getItem(2));

                $customItem = $interface2->getCustomItem();
                if(!$customItem || !$customItem instanceof RepairResource){
                    $inv->setItem(4, VanillaItems::AIR());
                    return new InvMenuTransactionResult(false);
                }

                //$repair = $interface2->getCustomItem()->getRepairValue() * $interface2->getItem()->getCount();
                $repairMax = $interface1->getDamage();
                $repairCount = (int)ceil($repairMax / $customItem->getRepairValue());

                $inv->setItem(0, VanillaItems::AIR());
                if($repairCount >= $inv->getItem(2)->getCount()){
                    $inv->setItem(2, VanillaItems::AIR());
                }else{
                    $inv->setItem(2, $inv->getItem(2)->setCount($inv->getItem(2)->getCount() - $repairCount));
                }

                $sound = new AnvilUseSound();
                $pk = $sound->encode($player->getPosition()->asVector3());
                $player->getNetworkSession()->sendDataPacket($pk[0]);
                return new InvMenuTransactionResult(false);
            }

            if($itemClicked->getNamedTag()->getTag("menuItem") !== null){
                return new InvMenuTransactionResult(true);
            }

            $inv->setItem(4, VanillaItems::AIR());

            $item1 = clone $inv->getItem(0);
            $item2 = clone $inv->getItem(2);

            if($itemClickedWith->getTypeId() !== VanillaItems::AIR()->getTypeId()){
                switch ($action->getSlot()){
                    case 0:
                        $item = $item1;
                        break;
                    case 2:
                        $item = $item2;
                        break;
                    default:
                        $item = null;
                        break;
                }

                if($item !== null && $item->equals($itemClickedWith, true, true)){
                    $item->setCount($itemClickedWith->getCount());
                }

                if($item !== null && $item->getTypeId() === VanillaItems::AIR()->getTypeId()){
                    $item = $itemClickedWith;
                }

                if($item !== null && !$item->equals($itemClickedWith, true, true)){
                    $item = $itemClickedWith;
                }
                switch ($action->getSlot()){
                    case 0:
                        $item1 = $item;
                        break;
                    case 2:
                        $item2 = $item;
                        break;
                }
            }else{
                if($action->getSlot() === 0 || $action->getSlot() === 2){
                    $inv->setItem(4, VanillaItems::AIR());
                    return new InvMenuTransactionResult(false); //one of the slots is empty
                }
            }

            if($item1->getTypeId() === VanillaItems::AIR()->getTypeId() || $item2->getTypeId() === VanillaItems::AIR()->getTypeId()) {
                $inv->setItem(4, VanillaItems::AIR());
                return new InvMenuTransactionResult(false);
            }

            $interface1 = ItemInterface::fromItem($item1);
            $interface2 = ItemInterface::fromItem($item2);
            if(!$interface1->isCustomItem() || !$interface1->getCustomItem() instanceof Repairable){
                $inv->setItem(4, VanillaItems::AIR());
                return new InvMenuTransactionResult(false);
            }

            if(!$interface2->isCustomItem() || !$interface2->getCustomItem() instanceof RepairResource){
                $inv->setItem(4, VanillaItems::AIR());
                return new InvMenuTransactionResult(false);
            }

            $repair = $interface2->getCustomItem()->getRepairValue() * $item2->getCount();

            $interface1->setDamage(max(0, $interface1->getDamage() - $repair));
            $interface1->recalculateDamage();
            $interface1->recalculateLore();
            $interface1->saveStats();

            $inv->setItem(4, $interface1->getItem());

            return new InvMenuTransactionResult(false);
        });
        $menu->setInventoryCloseListener(static function(Player $player, InvMenuInventory $inventory) use ($menu){
            foreach([0, 2] as $index){
                if($inventory->getItem($index)->getTypeId() !== VanillaItems::AIR()->getTypeId()){
                    $item = $inventory->getItem($index);
                    if($player->getInventory()->canAddItem($item)){
                        $player->getInventory()->addItem($item);
                    }else{
                        $player->dropItem($item);
                    }
                }
            }
        });

		$menuItem = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem();
        $menuItem->setCustomName("§r§c<-- Item 1\n§r§cItem 2 -->");
        $menuItem->setNamedTag($menuItem->getNamedTag()->setString("menuItem", "locked"));
        $menu->getInventory()->setItem(1, $menuItem);

		$menuItem = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem();
        $menuItem->setCustomName("§r§c<-- Item 2\n§r§cOutput -->");
        $menuItem->setNamedTag($menuItem->getNamedTag()->setString("menuItem", "locked"));
        $menu->getInventory()->setItem(3, $menuItem);

        $menu->send($sender);
    }
}