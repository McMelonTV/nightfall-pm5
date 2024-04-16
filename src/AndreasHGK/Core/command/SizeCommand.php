<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\ui\SizeForm;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SizeCommand extends Executor{

    public function __construct(){
        parent::__construct("size", "change your size", "/size [size]", "nightfall.command.size");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "size", CustomCommandParameter::ARG_TYPE_INT, true, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        if(isset($args[0])){
            if(!is_numeric($args[0])){
                $sender->sendMessage("§c§l> §r§7Please enter a valid vault page number.");
                return true;
            }

            $size = (int)$args[0];
            if($size < 50){
                $sender->sendMessage("§b§l> §r§7Your size can't be lower than §b50§7.");
                return true;
            }

            if($size > 150){
                $sender->sendMessage("§b§l> §r§7Your size can't be higher than §b150§7.");
                return true;
            }

            $user = UserManager::getInstance()->getOnline($sender);

            if($user instanceof User){
                $user->setSize($size);
            }

            $sender->setScale($size/100);
            $sender->sendMessage("§b§l> §r§7Your set your size to §b".$size."§7.");
            return true;
        }

        SizeForm::sendTo($sender);
        return true;
    }
}