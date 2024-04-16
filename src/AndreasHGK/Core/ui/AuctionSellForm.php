<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\auctionhouse\AuctionItem;
use AndreasHGK\Core\auctionhouse\AuctionManager;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class AuctionSellForm {

    public static function sendTo(Player $sender, int $price = 0) : void {
        $ui = new CustomForm(static function (Player $player, ?array $data){
            if($data === null || empty($data)){
                return;
            }

            if(!isset($data["price"])){
                $player->sendMessage("§r§c§l> §r§7Please enter an item price.");
                return;
            }

            if(!isset($data["count"])){
                $player->sendMessage("§r§c§l> §r§7Please enter an item count.");
                return;
            }

            $price = $data["price"];
            $count = $data["count"];
            if(!is_numeric($price)){
                $player->sendMessage("§r§c§l> §r§7Please enter a valid item price.");
                return;
            }

            if(!is_numeric($count)){
                $player->sendMessage("§r§c§l> §r§7Please enter a valid item count.");
                return;
            }

            $price = (int)$price;
            $count = (int)$count;
            if($price < 1){
                $player->sendMessage("§r§c§l> §r§7Please enter a price higher than 0.");
                return;
            }

            if($count < 1){
                $player->sendMessage("§r§c§l> §r§7Please enter an item count higher than 0.");
                return;
            }

            $item = $player->getInventory()->getItemInHand();
            if($item->getId() === ItemIds::AIR){
                $player->sendMessage("§r§c§l>§r§7 You can't sell air.");
                return;
            }

            if($count > $item->getMaxStackSize()){
                $player->sendMessage("§r§c§l>§r§7 You can't sell more than §c".$item->getMaxStackSize()."§7 items for this item type.");
                return;
            }

            if($count > $item->getCount()){
                $player->sendMessage("§r§c§l>§r§7 You can't sell more items than you are holding!");
                return;
            }

            $aucItem = new AuctionItem((string)(time()+microtime(true)), $item->setCount($count), $player->getName(), time(), $price);
            AuctionManager::getInstance()->addItem($aucItem);
            $player->getInventory()->removeItem($item);
            $player->sendMessage("§r§b§l> §r§7You are now auctioning off §b".$count."x ".$item->getName()." §7for §b$".$price."§r§7.");
        });

        $ui->setTitle("§b/auction sell");

        $ui->addLabel("§r§7Enter a price and how much of the items in your hand you cant to sell in pv.");

        $ui->addInput("§r§b§l> §r§7Price", "", $price > 0 ? (string)$price : null, "price");
        $ui->addInput("§r§b§l> §r§7Item count", "", (string)$sender->getInventory()->getItemInHand()->getCount(), "count");

        $sender->sendForm($ui);
    }

}