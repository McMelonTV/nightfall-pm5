<?php
declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\auctionhouse\AuctionManager;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\IntUtils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\world\sound\ClickSound;

class AuctionInventory {

    public static function sendTo(Player $sender) : void {
        $user = UserManager::getInstance()->getOnline($sender);
        if(AuctionManager::getInstance()->countItems() < 1 && empty($user->getExpiredAuctionItems())){
            $sender->sendMessage("§r§c§l> §r§7There are no items being actioned.");
            return;
        }

        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName("§8Auction");
        $menu->setListener($menu->readonly(static function (InvMenuTransaction $ts) use ($menu){
            $player = $ts->getPlayer();
            $itemClicked = $ts->getOut();

            $user = UserManager::getInstance()->getOnline($player);
            if($itemClicked->getNamedTag()->getTag("auctionMenuItem") !== null){
                if($itemClicked->getNamedTag()->getString("auctionMenuItem") === "previous"){
                    $user->setAucPage($user->getAucPage()-1);
                }elseif($itemClicked->getNamedTag()->getString("auctionMenuItem") === "next"){
                    $user->setAucPage($user->getAucPage()+1);
                }elseif($itemClicked->getNamedTag()->getString("auctionMenuItem") === "expired"){
                    $user->setAucPage(1);
                    $user->setViewingOwnItems(false);
                    $user->setViewingExpiredAuc(true);
                }elseif($itemClicked->getNamedTag()->getString("auctionMenuItem") === "owned"){
                    $user->setAucPage(1);
                    $user->setViewingOwnItems(true);
                    $user->setViewingExpiredAuc(false);
                }elseif($itemClicked->getNamedTag()->getString("auctionMenuItem") === "back"){
                    $user->setAucPage(1);
                    $user->setViewingExpiredAuc(false);
                    $user->setViewingOwnItems(false);
                }

                self::update($user);
                return new InvMenuTransactionResult(true);
            }
            if($itemClicked->getNamedTag()->getTag("auctionItem") !== null){
                $fullId = $itemClicked->getNamedTag()->getString("auctionItem");
                if(!AuctionManager::getInstance()->exists($fullId)){
                    $player->sendMessage("§r§c§l> §r§7That item is expired or has already been sold!");
                    return new InvMenuTransactionResult(true);
                }

                $aucItem = AuctionManager::getInstance()->getByFullId($fullId);
                if($aucItem->getSeller() === $player->getName()){
                    $player->sendMessage("§r§c§l> §r§7You can't buy your own items.");
                    return new InvMenuTransactionResult(true);
                }

                if($user->getBalance() < $aucItem->getPrice()){
                    $player->sendMessage("§r§c§l> §r§7You don't have enough money to buy that item!");
                    return new InvMenuTransactionResult(true);
                }

                if($player->getInventory()->firstEmpty() === -1){
                    $player->sendMessage("§r§c§l> §r§7You need a free slot to buy items.");
                    return new InvMenuTransactionResult(true);
                }

                $player->removeCurrentWindow();
                AuctionConfirmForm::sendTo($player, $aucItem);
               /* $user->takeMoney($aucItem->getPrice());
                $receiver = UserManager::getInstance()->get(Server::getInstance()->getOfflinePlayer($aucItem->getSeller()));
                $receiver->addMoney($aucItem->getPrice());
                UserManager::getInstance()->save($receiver);
                $player->getInventory()->addItem($aucItem->getItem());
                AuctionManager::getInstance()->remove($aucItem->getSeller(), $aucItem->getId());
                $player->sendMessage("§r§b§l> §r§7You bought §b".$aucItem->getItem()->getCount()."x ".$aucItem->getItem()->getName()." §7for §b$".$aucItem->getPrice()."§r§7.");
                if($receiver instanceof User){
                    $receiver->getPlayer()->sendMessage("§r§b§l> §r§7§b".$aucItem->getItem()->getCount()."x §r§7of your §b".$aucItem->getItem()->getName()."§r§7 have been sold for §b$".$aucItem->getPrice()."§r§7.");
                }*/
                return new InvMenuTransactionResult(true);
            }
            if($itemClicked->getNamedTag()->getTag("expiredItem") !== null){
                $id = $itemClicked->getNamedTag()->getInt("expiredItem");
                $aucItem = $user->getExpiredAuctionItems()[$id];
                if($player->getInventory()->firstEmpty() < 0){
                    $player->sendMessage("§r§c§l> §r§7You need a free slot to reclaim items.");
                    return new InvMenuTransactionResult(true);
                }

                $player->getInventory()->addItem($aucItem->getItem());
                $items = $user->getExpiredAuctionItems();
                unset($items[$id]);
                $newArray = [];
                foreach($items as $item){
                    $newArray[] = $item;
                }

                $user->setExpiredAuctionItems($newArray);
                $player->sendMessage("§r§b§l> §r§7You reclaimed your §b".$aucItem->getItem()->getName()."§r§7.");

                $user->playSound(new ClickSound());

                self::update($user);
            }
            if($itemClicked->getNamedTag()->getTag("sellingItem") !== null){
                $id = $itemClicked->getNamedTag()->getString("sellingItem");
                $aucItem = AuctionManager::getInstance()->get($player->getName(), $id);
                if($aucItem === null){
                    $player->sendMessage("§r§c§l> §r§7That item has already been bought.");
                    return new InvMenuTransactionResult(true);
                }

                if($player->getInventory()->firstEmpty() < 0){
                    $player->sendMessage("§r§c§l> §r§7You need a free slot to reclaim items.");
                    return new InvMenuTransactionResult(true);
                }

                $player->getInventory()->addItem($aucItem->getItem());
                $items = AuctionManager::getInstance()->getAllSellerItems($player->getName());
                unset($items[$id]);
                AuctionManager::getInstance()->setAllSellerItems($player->getName(), $items);
                $player->sendMessage("§r§b§l> §r§7You removed your §b".$aucItem->getItem()->getName()."§r§7 from the auction.");

                $user->playSound(new ClickSound());

                self::update($user);
            }
            return new InvMenuTransactionResult(true);
        }));
        /*$menu->setInventoryCloseListener(function(Player $player, InvMenuInventory $inventory) use ($menu){

        });*/

        $user->setAucPage(1);
        $user->setViewingExpiredAuc(false);
        $user->setAucInv($menu);

        self::update($user);

        $menu->send($sender);
    }

    public static function update(User $user) : void {
        $if = ItemFactory::getInstance();
        $menu = $user->getAucInv();
        $menu->getInventory()->clearAll();
        $int = 0;
        $page = $user->getAucPage();
        $maxPages = $user->getViewingExpiredAuc() ? ceil(count($user->getExpiredAuctionItems())/45) : AuctionManager::getInstance()->countPages();
        if($user->getViewingExpiredAuc()){
            foreach($user->getExpiredAuctionItems() as $c => $auctionItem){
                if($int <= 44){
                    if($c+1 < ($page-1)*45) {
                        continue;
                    }

                    $item = clone $auctionItem->getItem();
                    $lore = $item->getLore();
                    $lore[] = "§r§8[§7Seller: §bYou§r§8]";
                    $lore[] = "§r§8[§bClick to remove§8]";
                    $item->setLore($lore);
                    $item->setNamedTag($item->getNamedTag()->setInt("expiredItem", $c));
                }else{
                    break;
                }

                $menu->getInventory()->setItem($int, $item);
                ++$int;
            }
        }elseif($user->getViewingOwnItems()){
            foreach(AuctionManager::getInstance()->getAllSellerItems($user->getPlayer()->getName()) as $c => $auctionItem){
                if($int <= 44){
                    if($c+1 < ($page-1)*45) {
                        continue;
                    }

                    $item = clone $auctionItem->getItem();
                    $lore = $item->getLore();
                    $lore[] = "§r§8[§7Seller: §bYou§r§8]";
                    $lore[] = "§r§8[§bClick to remove§r§8]";
                    $item->setLore($lore);
                    $item->setNamedTag($item->getNamedTag()->setString("sellingItem", $c));
                }else{
                    break;
                }
                $menu->getInventory()->setItem($int, $item);
                ++$int;
            }
        }else{
            foreach(AuctionManager::getInstance()->getAllArray() as $c => $auctionItem){
                if($int <= 44){
                    if($c+1 < ($page-1)*45) continue;
                    $item = clone $auctionItem->getItem();
                    $lore = $item->getLore();
                    $lore[] = "§r§8[§7Seller: §b".$auctionItem->getSeller()."§r§8]";
                    $lore[] = "§r§8[§7Price: §b$".IntUtils::shortNumberRounded($auctionItem->getPrice())."§r§8]";
                    $item->setLore($lore);
                    $item->setNamedTag($item->getNamedTag()->setString("auctionItem", $auctionItem->getFullId()));
                }else{
                    break;
                }
                $menu->getInventory()->setItem($int, $item);
                ++$int;
            }
        }

        for($i = 45; $i < 54; ++$i){
            $item = $if->get(ItemIds::STAINED_GLASS_PANE, 14, 1);
            $item->setCustomName("§r§c/");
            $menu->getInventory()->setItem($i, $item);
        }

        $item = $if->get(ItemIds::PAPER, 14, 1);
        $item->setCustomName("§r§7You can sell items\n§r§7here with:\n§r§b/ah sell");
        $menu->getInventory()->setItem(53, $item);

        if($user->getViewingExpiredAuc() || $user->getViewingOwnItems()){
            $back = $if->get(ItemIds::BARRIER, 0, 1);
            $back->setNamedTag($back->getNamedTag()->setString("auctionMenuItem", "back"));
            $back->setCustomName("§r§bGo back");
            $menu->getInventory()->setItem(53, $back);
        }

        if((!$user->getViewingOwnItems() && !$user->getViewingExpiredAuc()) && !empty($user->getExpiredAuctionItems())){
            $expired = $if->get(ItemIds::CHEST, 0, 1);
            $expired->setNamedTag($expired->getNamedTag()->setString("auctionMenuItem", "expired"));
            $expired->setCustomName("§r§bView expired items");
            $menu->getInventory()->setItem(46, $expired);
        }
        if((!$user->getViewingOwnItems() && !$user->getViewingExpiredAuc()) && !empty(AuctionManager::getInstance()->getAllSellerItems($user->getPlayer()->getName()))){
            $expired = $if->get(ItemIds::CHEST, 0, 1);
            $expired->setNamedTag($expired->getNamedTag()->setString("auctionMenuItem", "owned"));
            $expired->setCustomName("§r§bView owned items");
            $menu->getInventory()->setItem(45, $expired);
        }

        $next = $if->get($user->getAucPage() < $maxPages ? ItemIds::PAPER : ItemIds::BARRIER, 0, 1);
        $next->setNamedTag($next->getNamedTag()->setString("auctionMenuItem", $user->getAucPage() < $maxPages ? "next" : "disabled"));
        $next->setCustomName($user->getAucPage() < $maxPages ? "§r§bNext page" : "§r§c/");
        $menu->getInventory()->setItem(50, $next);

        $previous = $if->get($user->getAucPage() > 1 ? ItemIds::PAPER : ItemIds::BARRIER, 0, 1);
        $previous->setNamedTag($previous->getNamedTag()->setString("auctionMenuItem", $user->getAucPage() > 1 ? "previous" : "disabled"));
        $previous->setCustomName($user->getAucPage() > 1 ? "§r§bPrevious page" : "§r§c/");
        $menu->getInventory()->setItem(48, $previous);

        $chest = $if->get(ItemIds::CHEST, 0, 1);
        $chest->setNamedTag($chest->getNamedTag()->setString("auctionMenuItem", "pageCounter"));
        $chest->setCustomName("§r§bpage ".$user->getAucPage()."§8/§b".(AuctionManager::getInstance()->countPages() > 0 ? AuctionManager::getInstance()->countPages() : 1));
        $menu->getInventory()->setItem(49, $chest);
    }
}