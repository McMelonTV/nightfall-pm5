<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\Core;
use AndreasHGK\Core\tag\TagManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class GiveTagCommand extends Executor{

    public function __construct(){
        parent::__construct("givetag", "give someone a tag", "/givetag <player> <tag>", Core::PERM_MAIN."command.givetag");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addArrayParameter(0, 1, "tag", "Tag", TagManager::getInstance()->getAllNames(),false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l> §r§7Please enter a target to give a tag to.");
            return true;
        }

        $target = Server::getInstance()->getPlayerByPrefix(array_shift($args));
        if($target === null){
            $sender->sendMessage("§r§c§l> §r§7That player could not be found.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l> §r§7Please enter a tag to give.");
            return true;
        }

        $tag = TagManager::getInstance()->get($args[0]);
        if($tag === null){
            $sender->sendMessage("§r§c§l> §r§7That tag could not be found.");
            return true;
        }

        $user = UserManager::getInstance()->getOnline($target);
        $user->grantTag($tag);
        $sender->sendMessage("§r§b§l> §r§7You have given §b".$target->getName()."§r§7 the §r".$tag->getTag()."§r§7 tag.");
        return true;
    }
}