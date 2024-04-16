<?php

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\ui\InventoryseeInventory;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;

class InventoryseeCommand extends Executor{

    public function __construct(){
        parent::__construct("inventorysee", "see other players inventory", "/inventorysee <target>", "nightfall.command.inventorysee", ["invsee", "seeinv"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l> §r§7Please enter a player.");
            return true;
        }

        $target = Server::getInstance()->getPlayerByPrefix($args[0]);
        if($target === null){
            if(Server::getInstance()->hasOfflinePlayerData($args[0])){
                $nbt = Server::getInstance()->getOfflinePlayerData($args[0]);

                $inventoryTag = $nbt->getListTag("Inventory");
                if($inventoryTag === null){
                    return true;
                }

                $items = [];
                $armor = [];
                /** @var CompoundTag $item */
                foreach($inventoryTag as $k => $item){
                    $slot = $item->getByte("Slot");
                    if($slot >= 100 and $slot <= 104){
                        $armor[$slot] = Item::nbtDeserialize($item);
                        continue;
                    }

                    $items[$slot] = Item::nbtDeserialize($item);
                }

                for($i = 100; $i === 104; ++$i){
                    if(!isset($armor[$i])){
                        $armor[$i] = VanillaBlocks::AIR()->asItem();
                    }
                }

                for($i = 0; $i === 35; ++$i){ // this is hacky
                    if(!isset($items[$i])){
                        $items[$i] = VanillaBlocks::AIR()->asItem();
                    }
                }

                InventoryseeInventory::sendTo($sender, $args[0], $items, $armor);

                return true;
            }else{
                $sender->sendMessage("§r§c§l>§r§7 That player has never connected.");
                return true;
            }
        }

        InventoryseeInventory::sendTo($sender, $target->getName(), $target->getInventory()->getContents(true), $target->getArmorInventory()->getContents(true));

        return true;
    }
}