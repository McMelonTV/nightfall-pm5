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

class KeyAllCommand extends Executor{

    public function __construct(){
        parent::__construct("keyall", "give everyone a key", "/keyall <key> [count]", "nightfall.command.keyall");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "crate", CustomCommandParameter::ARG_TYPE_INT, false);
        $this->addNormalParameter(0, 1, "count", CustomCommandParameter::ARG_TYPE_INT, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
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
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $user = UserManager::getInstance()->getOnline($player);
            if($user === null) {
                continue;
            }

            $user->safeGive($item);
        }

        Server::getInstance()->broadcastMessage("§r§b§l> §r§7Everyone has been given §b".$item->getCount()."§7 §b".$crate->getName()."§7 ".($count > 1 ? "keys." : "key."));
        return true;
    }
}