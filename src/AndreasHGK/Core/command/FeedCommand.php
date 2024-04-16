<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\Core;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class FeedCommand extends Executor{

    public const COOLDOWN = 60;

    public function __construct(){
        parent::__construct("feed", "feed a player", "/feed [player]", "nightfall.command.feed");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, true, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player && !isset($args[0])){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        if(UserManager::getInstance()->get($sender)->hasCooldownFor("feed", self::COOLDOWN)){
            $sender->sendMessage("§c§l> §r§7That you still have to wait §c".ceil(UserManager::getInstance()->get($sender)->getCooldownFor("feed", self::COOLDOWN))." seconds§r§7 before you can use this command again.");
            return true;
        }

        if(isset($args[0]) && $sender->hasPermission(Core::PERM_MAIN."command.feed.others")){
            $player = Server::getInstance()->getOfflinePlayer($args[0]);
            if(!$player->hasPlayedBefore()){
                $sender->sendMessage("§c§l> §r§7That player was not found.");
                return true;
            }
        }else{
            $player = $sender;
        }

        $hunger = $player->getHungerManager();
        $hunger->setFood($hunger->getMaxFood());
        $hunger->setSaturation(15);
        UserManager::getInstance()->get($sender)->setCooldownFor("feed");
        if($player !== $sender){
            $sender->sendMessage("§b§l> §r§7You fed §b".$player->getName()."§r§7.");
        }

        $player->sendMessage("§b§l> §r§7You have been fed.");
        return true;
    }
}