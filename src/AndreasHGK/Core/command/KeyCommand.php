<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\crate\CrateManager;
use AndreasHGK\Core\item\CrateKey;
use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class KeyCommand extends Executor{

    public function __construct(){
        parent::__construct("key", "give someone a key", "/key <target> <key> [count]", "nightfall.command.key");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false);
        $this->addNormalParameter(0, 1, "crate", CustomCommandParameter::ARG_TYPE_INT, false);
        $this->addNormalParameter(0, 2, "count", CustomCommandParameter::ARG_TYPE_INT, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a target.");
            return true;
        }

        $targetName = array_shift($args);

        $target = Server::getInstance()->getPlayerExact($targetName);

        if($target === null){
            $sender->sendMessage("§r§c§l>§r§7 Please player could not be found.");
            return true;
        }

        $targetUser = UserManager::getInstance()->getOnline($target);

        if($target === null){
            $sender->sendMessage("§r§c§l>§r§7 Please player could not be found.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a crate ID.");
            return true;
        }

        $id = $args[0];
        if(!is_numeric($id)){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a valid ID.");
            return true;
        }

        $id = (int)$id;
        $count = 1;
        if(isset($args[1])){
            $count = $args[1];
        }

        if(!is_numeric($count)){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a valid key count.");
            return true;
        }

        $count = (int)$count;
        $crate = CrateManager::getInstance()->get($id);
        if($crate === null){
            $sender->sendMessage("§r§c§l>§r§7 That crate doesnt exist.");
            return true;
        }

        /** @var CrateKey $cItem */
        $cItem = CustomItemManager::getInstance()->get(CustomItem::CRATEKEY);
        $item = $cItem->getVariant($id);
        $item->setCount($count);
        $targetUser->safeGive($item);

        $sender->sendMessage("§r§b§l>§r§b $targetName §r§7has been given §b".$item->getCount()."§7 §b".$crate->getName()."§7 ".($count > 1 ? "keys." : "key."));
        $target->sendMessage("§r§b§l>§r§7 You have been given §b".$item->getCount()."§7 §b".$crate->getName()."§7 ".($count > 1 ? "keys." : "key."));
        return true;
    }
}