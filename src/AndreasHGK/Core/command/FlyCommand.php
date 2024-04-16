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

class FlyCommand extends Executor
{

    public function __construct()
    {
        parent::__construct("fly", "toggle flight mode", "/fly [player]", Core::PERM_MAIN."command.fly", ["flight"]);
        $this->addParameterMap(0, Core::PERM_MAIN."command.fly.others");
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        if(isset($args[0]) && $sender->hasPermission(Core::PERM_MAIN."command.fly.others")){
            $pname = implode(" ", $args);
            $player = Server::getInstance()->getPlayerByPrefix($pname);
            if($player === null){
                $sender->sendMessage("§c§l> §r§7Player with name §c".$pname."§r§7 not found.");
                return true;
            }
        }else{
            $player = $sender;
        }

        $user = UserManager::getInstance()->get($player);

        $bool = $user->isFlying();
        if(!$bool){
            $str = "enabled";
        }else{
            $str = "disabled";
        }

        $user->setFly(!$bool);
        if($player !== $sender){
            $sender->sendMessage("§r§b§l> §r§7You §b".$str."§r§7 flight mode for §b".$player->getName()."§r§7.");
        }

        $player->sendMessage("§r§b§l>§r§7 Flight mode is now §b".$str."§r§7.");

        return true;
    }
}