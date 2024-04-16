<?php

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\ui\ListGangsForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ListSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("list", "list of gangs", "list", "nightfall.command.gang.list");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        ListGangsForm::sendTo($sender);

        return true;
    }
}