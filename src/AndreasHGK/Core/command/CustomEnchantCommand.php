<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\Core;
use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\utils\EnchantmentUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CustomEnchantCommand extends Executor{

    public function __construct(){
        parent::__construct("customenchant", "apply custom enchants", "/customenchant <id> <level>", Core::PERM_MAIN."command.customenchant");
        $this->addParameterMap(0);
        $this->addArrayParameter(0, 0, "id", "CustomEnchantment", CustomEnchantsManager::getInstance()->getAllIds(), false, true);
        $this->addNormalParameter(0, 1, "level",  CustomCommandParameter::ARG_TYPE_INT,false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        if(count($args) < 2 || !is_numeric($args[0]) || !is_numeric($args[1])){
            $sender->sendMessage("§c§l> §r§7Usage: §c".$this->usage."§7.");
            return true;
        }

        $id = (int)$args[0];
        $level = (int)$args[1];

        $enchantment = CustomEnchantsManager::getInstance()->get($id);
        if($enchantment === null){
            $sender->sendMessage("§c§l> §r§7That enchantment was not found.");
            return true;
        }

        $enchantment->setLevel($level);

        $interface = ItemInterface::fromItem($sender->getInventory()->getItemInHand());
        $ce = $interface->getCustomEnchants();
        $ce[$id] = $enchantment;

        $interface->setCustomEnchants($ce);
        $interface->saveStats();
        $interface->recalculateLore();

        $item = $interface->getItem();
        $sender->getInventory()->setItemInHand(EnchantmentUtils::applyGlow($item));
        $sender->sendMessage("§c§l> §r§7Enchanting succeeded.");
        return true;
    }

}