<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\auctionhouse\AuctionItem;
use AndreasHGK\Core\auctionhouse\AuctionManager;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\Server;

class AuctionConfirmForm {

    public static function sendTo(Player $sender, AuctionItem $aucItem) : void {
        $ui = new SimpleForm(static function (Player $player, ?string $data) use($aucItem){
            if($data === null){
                return;
            }

            if($data !== "confirm") {
                return;
            }

            $user = UserManager::getInstance()->getOnline($player);
            if(!AuctionManager::getInstance()->exists($aucItem->getFullId())){
                $player->sendMessage("§r§c§l> §r§7That item is expired or has already been sold!");
                return;
            }

            if($aucItem->getSeller() === $player->getName()){
                $player->sendMessage("§r§c§l> §r§7You can't buy your own items.");
                return;
            }

            if($user->getBalance() < $aucItem->getPrice()){
                $player->sendMessage("§r§c§l> §r§7You don't have enough money to buy that item!");
                return;
            }

            if($player->getInventory()->firstEmpty() === -1){
                $player->sendMessage("§r§c§l> §r§7You need a free slot to buy items.");
                return;
            }

            $user->takeMoney($aucItem->getPrice());
            $receiver = UserManager::getInstance()->get(Server::getInstance()->getOfflinePlayer($aucItem->getSeller()));
            $receiver->addMoney($aucItem->getPrice());
            UserManager::getInstance()->save($receiver);
            $player->getInventory()->addItem($aucItem->getItem());
            AuctionManager::getInstance()->remove($aucItem->getSeller(), $aucItem->getId());
            $player->sendMessage("§r§b§l> §r§7You bought §b".$aucItem->getItem()->getCount()."x ".$aucItem->getItem()->getName()." §7for §b$".$aucItem->getPrice()."§r§7.");
            if($receiver instanceof User){
                $receiver->getPlayer()->sendMessage("§r§b§l> §r§7§b".$aucItem->getItem()->getCount()."x §r§7of your §b".$aucItem->getItem()->getName()."§r§7 have been sold for §b$".$aucItem->getPrice()."§r§7.");
            }
        });

        $ui->setTitle("§bAuction confirm");

        $ui->setContent("§r§7Are you sure you want to buy §b".$aucItem->getItem()->getCount()."x §r".$aucItem->getItem()->getName()." §r§7for §b$".$aucItem->getPrice()."§r§7?");

        $ui->addButton("§8Confirm purchase", -1, "", "confirm");
        $ui->addButton("§8Cancel", -1, "", "cancel");

        $sender->sendForm($ui);
    }
}