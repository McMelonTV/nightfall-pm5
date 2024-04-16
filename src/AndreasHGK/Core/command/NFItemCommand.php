<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\item\EnchantmentBook;
use AndreasHGK\Core\item\TieredItem;
use AndreasHGK\Core\item\VariantItem;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class NFItemCommand extends Executor{

    public function __construct(){
        parent::__construct("nfitem", "give a custom item", "/nfitem <id|name> [count] [target]", "nightfall.command.nfitem", ["customitem"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player && !isset($args[1])){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§c§l> §r§7Please enter an item to give.");
            return true;
        }

        $itemName = array_shift($args);
        $customitem = null;
        if(is_numeric($itemName)){
            $customitem = CustomItemManager::getInstance()->get((int)$itemName);
        }

        if($customitem === null){
            $customitem = CustomItemManager::getInstance()->getFromName($itemName);
        }

        if($customitem === null){
            $sender->sendMessage("§c§l> §r§7That item could not be found.");
            return true;
        }

        if(isset($args[0])){
            $count = array_shift($args);
        }else{
            $count = 1;
        }

        if(!is_numeric($count)){
            $sender->sendMessage("§c§l> §r§7Please enter a numeric item count.");
            return true;
        }else{
            $count = (int)$count;
        }

        if(isset($args[0])){
            $extraData = array_shift($args);
        }

        if(isset($args[0])){
            $targetName = implode(" ", $args);
            $target = Server::getInstance()->getPlayerExact($targetName);
        }else{
            $target = $sender;
        }

        if($target === null){
            $sender->sendMessage("§c§l> §r§7The target player could not be found.");
            return true;
        }

        if(isset($extraData)){
            if($customitem instanceof TieredItem){
                $item = clone $customitem->getTier((int)$extraData);
            }elseif($customitem instanceof EnchantmentBook){
                $item = clone $customitem->getVariant((int)$extraData, $count);
            }elseif($customitem instanceof VariantItem){
                $item = clone $customitem->getVariant((int)$extraData);
            }else{
                $item = clone $customitem->getItem();
            }
        }else{
            $item = clone $customitem->getItem();
        }

        $item->setCount($count);
        if($customitem instanceof EnchantmentBook){
            $item->setCount(1);
        }

        $target->getInventory()->addItem($item);
        if($sender === $target){
            $sender->sendMessage("§b§l> §r§7You have been given §b".$item->getCount()."x §r".$item->getName()."§r§7.");
        }else{
            $sender->sendMessage("§b§l> §r§7You have given §b".$item->getCount()."x §r".$item->getName()."§r§7 to §b".$target->getName()."§r§7.");
        }

        return true;
    }

}