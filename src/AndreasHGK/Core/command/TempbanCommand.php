<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\BannedUserManager;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\TimeUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class TempbanCommand extends Executor{

    public function __construct(){
        parent::__construct("tempban", "temporarily ban a player from the server", "/tempban <player> <duration> [reason]", "nightfall.command.tempban");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addNormalParameter(0, 1, "duration", CustomCommandParameter::ARG_TYPE_STRING, false, true);
        $this->addNormalParameter(0, 2, "reason", CustomCommandParameter::ARG_TYPE_STRING, true, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r §7Please enter a player to ban.");
            return true;
        }

        if($args[0] === "silent"){
            array_shift($args);
            $silent = true;
        }else{
            $silent = false;
        }

        if($args[0] === "super"){
            array_shift($args);
            $super = true;
        }else{
            $super = false;
        }

        $target = Server::getInstance()->getOfflinePlayer(array_shift($args));
        if(!$target->hasPlayedBefore() && !$target instanceof Player){
            $sender->sendMessage("§r§c§l>§r §7That player has never connected.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r §7Please enter a ban duration.");
            return true;
        }

        if(TimeUtils::shortTimeStringToInt($args[0]) !== false
            && isset($args[1]) && TimeUtils::shortTimeStringToInt($args[1]) !== false
            && isset($args[2]) && TimeUtils::shortTimeStringToInt($args[2]) !== false
            && isset($args[3]) && TimeUtils::shortTimeStringToInt($args[3]) !== false){
            $array = [];
            for($i = 0; $i < 4; ++$i){
                $array[] = array_shift($args);
            }
        }elseif(TimeUtils::shortTimeStringToInt($args[0]) !== false
            && isset($args[1]) && TimeUtils::shortTimeStringToInt($args[1]) !== false
            && isset($args[2]) && TimeUtils::shortTimeStringToInt($args[2]) !== false){
            $array = [];
            for($i = 0; $i < 3; ++$i){
                $array[] = array_shift($args);
            }
        }elseif(TimeUtils::shortTimeStringToInt($args[0]) !== false
            && isset($args[1]) && TimeUtils::shortTimeStringToInt($args[1]) !== false){
            $array = [];
            for($i = 0; $i < 2; ++$i){
                $array[] = array_shift($args);
            }
        }elseif(TimeUtils::shortTimeStringToInt($args[0]) !== false){
            $array = [];
            for($i = 0; $i < 1; ++$i){
                $array[] = array_shift($args);
            }
        }else{
            $sender->sendMessage("§r§c§l>§r §7Please enter a correct time string.");
            return true;
        }

        $duration = TimeUtils::shortTimeStringToInt(implode(" ", $array));
        if($duration === false){
            $sender->sendMessage("§r§c§l>§r §7Please enter a correct time string.");
            return true;
        }

        if(isset($args[0])){
            $reason = implode(" ", $args);
        }else{
            $reason = "you have been banned";
        }

        $user = UserManager::getInstance()->get($target);
        $ban = BannedUserManager::getInstance()->ban($user, $reason, $duration, $sender->getName());
        if($super) $ban->setSuperban($super);

        if($silent){
            $sender->sendMessage("§r§b§l> §r§7You banned §b".$target->getName()."§r§7 for §b".TimeUtils::intToTimeString($duration)."§r§7 with reason: §b".$reason."§r§7.");
        }else{
            if($super){
                Server::getInstance()->broadcastMessage("§r§8[§bAEGIS§8] §r§b".$target->getName()."§r§7 has been §4S§cU§bP§eE§aR§2B§3A§bN§9N§dE§5D §r§7by §b".($sender->getName() === "CONSOLE" ? "AEGIS v1" : $sender->getName())."§r§7 for §b".TimeUtils::intToTimeString($duration)."§r§7 with reason: §b".$reason."§r§7.");

            }else{
                Server::getInstance()->broadcastMessage("§r§8[§bNF§8] §r§b".$target->getName()."§r§7 has been banned by §b".$sender->getName()."§r§7 for §b".TimeUtils::intToTimeString($duration)."§r§7 with reason: §b".$reason."§r§7.");
            }
        }

        return true;
    }

}