<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\Core;
use AndreasHGK\Core\ui\NickForm;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class NickCommand extends Executor{

    public function __construct(){
        parent::__construct("nick", "change your nickname", "/nick [nick|clear]", "nightfall.command.nick", ["nickname"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "nickname", CustomCommandParameter::ARG_TYPE_STRING, false, true);
        $this->addParameterMap(1);
        $this->addSingleParameter(1, 0, "nickname", "Nickname", "clear", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) return false;

        if(count($args) > 1 && $sender->hasPermission(Core::PERM_MAIN."command.nick.others")){
            $pname = array_shift($args);
            $player = Server::getInstance()->getPlayerByPrefix($pname);
            if($player === null){
                $sender->sendMessage("§c§l> §r§7Player with name §c".$pname."§r§7 not found.");
                return true;
            }

            $nick = implode(" ", $args);
        }else{
            if(!isset($args[0])){
                NickForm::sendTo($sender);
                return true;
            }

            $player = $sender;
            $nick = implode(" ", $args);
        }

        $user = UserManager::getInstance()->get($player);
        if($nick === "clear"){
            $user->setNick("");
            if($player !== $sender){
                $sender->sendMessage("§r§b§l> §r§7You cleared §b".$player->getName()."§r§7's nickname.");
            }

            $player->sendMessage("§r§b§l>§r§7 Your nickname has been cleared.");
            return true;
        }

        if(strlen(TextFormat::clean(TextFormat::colorize($nick))) > 20){
            $player->sendMessage("§r§c§l>§r§7 Please enter a shorter nickname.");
        }

        try{
            $user->setNick($nick);
        }catch(\Throwable $e){

        }

        if($player !== $sender){
            $sender->sendMessage("§r§b§l> §r§7You set §b".$player->getName()."§r§7's nickname to §b".$nick."§r§7.");
        }

        $player->sendMessage("§r§b§l>§r§7 Your nickname has been set to §b".$nick."§r§7.");

        return true;
    }
}