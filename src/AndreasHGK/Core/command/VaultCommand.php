<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\Core;
use AndreasHGK\Core\ui\VaultInventory;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\vault\VaultManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class VaultCommand extends Executor{

    public function __construct(){
        parent::__construct("vault", "open a vault", "/vault [number]", "nightfall.command.vault", ["pv"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "vault", CustomCommandParameter::ARG_TYPE_INT, true, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        $page = 1;
        $custom = false;
        if(isset($args[0])){
            if(!is_numeric($args[0])){
                $sender->sendMessage("§c§l> §r§7Please enter a valid vault page number.");
                return true;
            }

            $custom = true;
            $page = (int)$args[0];
        }

        if(isset($args[1]) && $sender->hasPermission(Core::PERM_MAIN."command.vault.others")){
            $target = $args[1];
            $user = UserManager::getInstance()->get(Server::getInstance()->getOfflinePlayer($target), false);
            if($user === null){
                $sender->sendMessage("§c§l> §r§7That player was not found.");
                return true;
            }
        }

        $vault = isset($user) ?  VaultManager::getInstance()->get($user) : UserManager::getInstance()->getOnline($sender)->getVault();
        if($vault->getEffectiveMaxPages() < $page || $page <= 0){
            $sender->sendMessage("§c§l> §r§7You do not have this amount of vault pages.");
            return true;
        }

        VaultInventory::sendTo($sender, $page, $user ?? null);
        if(isset($user)){
            $sender->sendMessage("§b§l> §r§7Opening §b".$user->getName()."§r§7's vault page §b".$page."§r§7.");
        }elseif($custom){
            $sender->sendMessage("§b§l> §r§7Opening vault page §b".$page."§r§7.");
        }else{
            $sender->sendMessage("§b§l> §r§7Opening your vault.");
        }

        return true;
    }
}