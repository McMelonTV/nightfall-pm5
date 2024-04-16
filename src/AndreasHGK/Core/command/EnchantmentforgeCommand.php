<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\ui\EnchantmentForgeForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EnchantmentforgeCommand extends Executor{

    public function __construct(){
        parent::__construct("enchantmentforge", "open the enchantmentforge", "/enchantmentforge", "nightfall.command.enchantmentforge", ["eforge", "ceshop", "enchantshop"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        EnchantmentForgeForm::sendTo($sender);
        return true;
    }

}