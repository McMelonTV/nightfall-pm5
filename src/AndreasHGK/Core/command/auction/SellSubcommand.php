<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\auction;

use AndreasHGK\Core\auctionhouse\AuctionItem;
use AndreasHGK\Core\auctionhouse\AuctionManager;
use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\ui\AuctionSellForm;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class SellSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("sell", "auction off an item", "sell <price> [count]", "nightfall.command.auction.sell", ["add"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command in-game.");
            return true;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        $auctionManager = AuctionManager::getInstance();
        if($user->getMaxAuc() <= count($auctionManager->getAllSellerItems($sender->getName()))){
            $sender->sendMessage("§r§c§l>§r§7 You have already reached your maximum auction items of §c".$user->getMaxAuc()."§r§7.");
            return true;
        }

        $item = $sender->getInventory()->getItemInHand();
        if($item->getId() === ItemIds::AIR){
            $sender->sendMessage("§r§c§l>§r§7 You can't sell air.");
            return true;
        }

        if(!isset($args[0])){
            AuctionSellForm::sendTo($sender);
            return true;
        }

        if(!isset($args[0]) || !is_numeric($args[0]) || $args[0] < 0){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a valid price for your item.");
            return true;
        }

        $price = (int)$args[0];
        if(!isset($args[1])){
            AuctionSellForm::sendTo($sender, $price);
            return true;
        }

        if(!isset($args[1])){
            $count = $item->getCount();
        }elseif(!is_numeric($args[1]) || !is_float((float)$args[1]) || $args[1] < 1){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a valid numeric item count.");
            return true;
        }else{
            $count = (int)$args[1];
        }

        if($count > $item->getMaxStackSize()){
            $sender->sendMessage("§r§c§l>§r§7 You can't sell more than §c".$item->getMaxStackSize()."§7 items for this item type.");
            return true;
        }

        if($count > $item->getCount()){
            $sender->sendMessage("§r§c§l>§r§7 You can't sell more items than you are holding!");
            return true;
        }

        $aucItem = new AuctionItem((string)(time()+microtime(true)), $item->setCount($count), $sender->getName(), time(), $price);
        $auctionManager->addItem($aucItem);
        $sender->getInventory()->removeItem($item);
        $sender->sendMessage("§r§b§l> §r§7You are now auctioning off §b".$count."x ".$item->getName()." §7for §b$".$price."§7.");
        return true;
    }
}