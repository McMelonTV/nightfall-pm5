<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;

class EnchantCommand extends Executor {

    public function __construct() {
        parent::__construct("enchant", "enchant an item", "/enchant [player]", "nightfall.command.enchant", ["applyenchant"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addMagicParameter(0, 1, "enchantment", "Enchant", false, true);
        $this->addNormalParameter(0, 2, "enchantment", CustomCommandParameter::ARG_TYPE_INT, true, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l> §r§7Please enter a player to enchant an item for.");
            return true;
        }

        if(!isset($args[1])){
            $sender->sendMessage("§r§c§l> §r§7Please enter an enchantment to apply.");
            return true;
        }

        $player = $sender->getServer()->getPlayerByPrefix($args[0]);
        if($player === null){
            $sender->sendMessage("§r§c§l> §r§7The target player could not be found.");
            return true;
        }

        $item = $player->getInventory()->getItemInHand();
        if($item->isNull()){
            $sender->sendMessage("§r§c§l> §r§7Please hold an item to enchant.");
            return true;
        }

        if(is_numeric($args[1])){
            $enchantment = EnchantmentIdMap::getinstance()->fromId((int) $args[1]);
        }else{
            $enchantment = VanillaEnchantments::fromString($args[1]);
        }

        if(!($enchantment instanceof Enchantment)){
            $sender->sendMessage("§r§c§l> §r§7The selected enchantment does not exist or is a non-vanilla enchantment.");
            return true;
        }

        $level = 1;
        if(isset($args[2])){
            if(!is_numeric($args[2]) || (int)$args[2] < 1){
                $sender->sendMessage("§r§c§l> §r§7Please enter a valid level.");
                return true;
            }
            $level = (int)$args[2];
        }

        $item->addEnchantment(new EnchantmentInstance($enchantment, $level));
        $player->getInventory()->setItemInHand($item);

        $sender->sendMessage("§r§b§l> §r§7Successfully enchanted an item for §b".$player->getName()."§r§7.");
        return true;
    }

}