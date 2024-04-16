<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\shop\ShopCategoryManager;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\IntUtils;
use muqsit\invmenu\inventory\InvMenuInventory;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\world\sound\ClickSound;

class ShopInventory {

    public static function sendTo(Player $sender) : void {
        $user = UserManager::getInstance()->get($sender);

        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName("§8Shop");
        $menu->setListener($menu->readonly(static function (InvMenuTransaction $ts) use ($menu){
            $player = $ts->getPlayer();
            $itemClicked = $ts->getOut();

            if($itemClicked->getNamedTag()->getTag("shopItem") !== null){
                $user = UserManager::getInstance()->getOnline($player);
                //if(!$user instanceof User) return false;
                $categoryName = $user->getOpenCategory();
                $category = ShopCategoryManager::getInstance()->get($categoryName);
                $cItem = $category->getItem($itemClicked->getNamedTag()->getString("shopItem"));
                $item = $cItem->getItem();
                if($cItem->isOneTime() && $user->hasShopPurchased($cItem)){
                    $player->sendMessage("§c§l> §r§7You have already purchased this.");
                    return new InvMenuTransactionResult(true);
                }

                if($user->getBalance() < $cItem->getPriceDollars()){
                    $player->sendMessage("§c§l> §r§7You don't have enough money to buy this item.");
                    return new InvMenuTransactionResult(true);
                }

                if($user->getPrestigePoints() < $cItem->getPricePrestige()){
                    $player->sendMessage("§c§l> §r§7You don't have enough prestige points to buy this item.");
                    return new InvMenuTransactionResult(true);
                }

                if($cItem->doGiveItem()) {
                    if($player->getInventory()->firstEmpty() !== -1){
                        $player->getInventory()->addItem($item);
                    }else{
                        $player->sendMessage("§c§l> §r§7You don't have enough space in your inventory to buy this item.");
                        return new InvMenuTransactionResult(true);
                    }
                }

                if($cItem->getShopName() === null){
                    $shopname = "§b".$item->getCount()."x §r".$item->getName()."§r";
                }else{
                    $shopname = $cItem->getShopName();
                }

                if($cItem->getPricePrestige() > 0 && $cItem->getPriceDollars() > 0){
                    $priceName = "§r§b$".$cItem->getPriceDollars()." §r§7and §b".$cItem->getPricePrestige()."PP§r";
                }else{
                    if($cItem->getPricePrestige() > 0){
                        $priceName = "§b".$cItem->getPricePrestige()."PP§r";
                    }else{
                        $priceName = "§r§b$".$cItem->getPriceDollars();
                    }
                }

                $user->takeMoney($cItem->getPriceDollars());
                $user->setPrestigePoints($user->getPrestigePoints() - $cItem->getPricePrestige());
                if($cItem->getCallback() !== null){
                    $cItem->getCallback()($user);
                }

                $user->addShopPurchase($cItem);

                $player->sendMessage("§b§l> §r§7You have bought ".$shopname."§r§7 for ".$priceName."§r§7.");

                $user->playSound(new ClickSound());

                self::update($player);

                return new InvMenuTransactionResult(true);
            }

            if($itemClicked->getNamedTag()->getTag("shopMenuItem") !== null){
                $user = UserManager::getInstance()->get($player);
                if(!$user instanceof User) {
                    return new InvMenuTransactionResult(true);
                }

                //if($itemClicked->getNamedTag()->getTagValue("shopMenuItem", StringTag::class) === "previous"){

                //}

                if($itemClicked->getNamedTag()->getString("shopMenuItem") === "exit"){
                    //$player->getNetworkSession()->sendDataPacket(ContainerOpenPacket::blockInv(ContainerIds::NONE, WindowTypes::NONE, 0, 0, 0));
                    $player->removeCurrentWindow();
                    $user->setOpenCategory(null);
                    $user->setShopInv(null);
                    return new InvMenuTransactionResult(true);
                }

                if($itemClicked->getNamedTag()->getString("shopMenuItem") === "back"){
                    $user->setOpenCategory(null);
                    self::update($player);
                    return new InvMenuTransactionResult(true);
                }

                return new InvMenuTransactionResult(true);
            }

            if($itemClicked->getNamedTag()->getTag("shopMenuCategory") !== null){
                $categoryName = $itemClicked->getNamedTag()->getString("shopMenuCategory");
                if(!ShopCategoryManager::getInstance()->exist($categoryName)){
                    $player->sendMessage("§c§l> §r§7The selected category is missing.");
                }

                $user = UserManager::getInstance()->get($player);
                if(!$user instanceof User) {
                    return new InvMenuTransactionResult(true);
                }

                $user->setOpenCategory($categoryName);
                self::update($player);

                return new InvMenuTransactionResult(true);
            }

            return new InvMenuTransactionResult(true);
        }));

        $menu->setInventoryCloseListener(static function(Player $player, InvMenuInventory $inventory) use ($menu){
            $user = UserManager::getInstance()->get($player);
            if(!$user instanceof User) {
                return;
            }

            $user->setOpenCategory(null);
            $user->setShopInv(null);
        });

        $categories = ShopCategoryManager::getInstance()->getAll();

        $key = 0;
        foreach ($categories as $category){
            $item = ItemFactory::getInstance()->get($category->getItemId());
            $item->setCustomName("§r".$category->getTag());
            $item->setNamedTag($item->getNamedTag()->setString("shopMenuCategory", $category->getName()));
            $menu->getInventory()->setItem($key, $item);
            ++$key;
        }

        $backItem = ItemFactory::getInstance()->get(ItemIds::BARRIER, 0, 1);
        $backItem->setCustomName("§cExit shop");
        $backItem->setNamedTag($backItem->getNamedTag()->setString("shopMenuItem", "exit"));
        $menu->getInventory()->setItem(53, $backItem);

        $menu->send($sender);
        if($user instanceof User){
            $user->setShopInv($menu);
        }
    }

    public static function update(Player $player) : void {
        $user = UserManager::getInstance()->get($player);
        if(!$user instanceof User) {
            return;
        }

        if($user->getShopInv() === null){
            $player->sendMessage("§c§l> §r§7Could not update the shop.");
        }

        $menu = $user->getShopInv();
        $menu->getInventory()->clearAll();
        if($user->getOpenCategory() === null){
            $categories = ShopCategoryManager::getInstance()->getAll();

            $key = 0;
            foreach ($categories as $category){
                $item = ItemFactory::getInstance()->get($category->getItemId());
                $item->setCustomName("§r".$category->getTag());
                $item->setNamedTag($item->getNamedTag()->setString("shopMenuCategory", $category->getName()));
                $menu->getInventory()->setItem($key, $item);
                ++$key;
            }

            $backItem = ItemFactory::getInstance()->get(ItemIds::BARRIER, 0, 1);
            $backItem->setCustomName("§cExit shop");
            $backItem->setNamedTag($backItem->getNamedTag()->setString("shopMenuItem", "exit"));
            $menu->getInventory()->setItem(53, $backItem);
        }else{
            $categoryName = $user->getOpenCategory();
            $category = ShopCategoryManager::getInstance()->get($categoryName);

            $key = 0;
            foreach ($category->getItems() as $id => $cItem){
                $item = clone $cItem->getItem();

                //$item->setNamedTag($item->getNamedTag()->setString("shopItem", $categoryName.":".(string)$id));
                $item->setNamedTag($item->getNamedTag()->setString("shopItem", (string)$id));

                $lore = [];
                if($cItem->getDesc() !== ""){
                    $lore[] = "§r§8§l[§r§7".$cItem->getDesc()."§8§l]§r";
                }

                if($cItem->isOneTime() && $user->hasShopPurchased($cItem)){
                    $item = EnchantmentUtils::applyGlow($item);
                    $lore[] = "§r§8§l[§r§bAlready Purchased§8§l]§r";
                }else{
                    if($cItem->getPriceDollars() > 0){
                        $lore[] = "§r§8§l[§r§7Price: §b".IntUtils::shortNumberRounded($cItem->getPriceDollars())."\$§8§l]§r.";
                    }

                    if($cItem->getPricePrestige() > 0){
                        $lore[] = "§r§8§l[§r§7Price: §b".$cItem->getPricePrestige()."PP§8§l]§r";
                    }
                }

                $item->setLore($lore);

                $menu->getInventory()->setItem($key, $item);
                ++$key;
            }

            $backItem = ItemFactory::getInstance()->get(ItemIds::BARRIER, 0, 1);
            $backItem->setCustomName("§cReturn");
            $backItem->setNamedTag($backItem->getNamedTag()->setString("shopMenuItem", "back"));
            $menu->getInventory()->setItem(53, $backItem);
        }
    }
}