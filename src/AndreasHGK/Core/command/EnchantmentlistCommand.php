<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\ui\EnchantmentlistForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EnchantmentlistCommand extends Executor{

    public function __construct(){
        parent::__construct("enchantmentlist", "see all the custom enchants", "/enchantmentlist", Core::PERM_MAIN."command.enchantmentlist", ["celist", "customenchants"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        EnchantmentlistForm::sendTo($sender);
        return true;
    }
}