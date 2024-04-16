<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\TimeUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class MuteCommand extends Executor{

    public function __construct(){
        parent::__construct("mute", "mute someone", "/mute <player> [time]", "nightfall.command.mute");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addNormalParameter(0, 1, "duration", CustomCommandParameter::ARG_TYPE_STRING, true, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l> §r§7Please enter a player to mute.");
            return true;
        }

        $name = array_shift($args);

        $target = Server::getInstance()->getPlayerByPrefix($name);
        if($target === null){
            if(Server::getInstance()->hasOfflinePlayerData($name)){
                $target = Server::getInstance()->getOfflinePlayer($name);
            }else{
                $sender->sendMessage("§r§c§l>§r§7 That player has never connected.");
                return true;
            }
        }

        $user = UserManager::getInstance()->get($target);
        if($user->isMuted()){
            $user->setMuteExpire(-1);
            $user->setMuted(false);
            $sender->sendMessage("§b§l> §r§7You have unmuted §b".$target->getName()."§r§7.");
            if($target === $sender) {
                return true;
            }

            $target->sendMessage("§r§b§l> §r§7You have been unmuted.");
            return true;
        }

        if(isset($args[0])){
            $timeArg = implode(" ", $args);
            $time = TimeUtils::shortTimeStringToInt($timeArg);
            if($time === false){
                $sender->sendMessage("§r§c§l> §r§7You have entered an incorrect time period.");
                return true;
            }

            $timeStr = TimeUtils::intToTimeString($time);
            $user->setMuted(true);
            $user->setMuteExpire(time()+$time);
            $sender->sendMessage("§b§l> §r§7You have muted §b".$target->getName()."§r§7 for §b".str_replace([",", "and"], ["§7,§b", "§7and§b"], $timeStr)."§r§7.");
            if($target === $sender) return true;
            if($user->isOnline()) {
                $target->sendMessage("§b§l> §r§7You have been muted for §b".str_replace([",", "and"], ["§7,§b", "§7and§b"], $timeStr)."§r§7.");
            }else{
                UserManager::getInstance()->save($user);
            }
        }else{
            $user->setMuted(true);
            $user->setMuteExpire(-1);
            $sender->sendMessage("§b§l> §r§7You have muted §b".$target->getName()."§r§7.");
            if($target === $sender) {
                return true;
            }
            if($target->isOnline()) {
                $target->sendMessage("§b§l> §r§7You have been muted.");
            }else{
                UserManager::getInstance()->save($user);
            }
        }

        return true;
    }

}