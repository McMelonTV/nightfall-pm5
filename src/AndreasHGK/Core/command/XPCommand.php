<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandParameterTypes;
use pocketmine\player\Player;
use pocketmine\Server;

class XPCommand extends Executor{

    public function __construct(){
        parent::__construct("xp", "give xp levels to a user", "/xp <amount: int> [levels: bool] [target: Player]", "nightfall.command.xp", ["givexp", "addxp"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "amount", CustomCommandParameter::ARG_TYPE_INT, false, true);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, true, false);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§c§l> §r§7Please enter an amount.");
            return true;
        }

        if(isset($args[1])){
            $target = Server::getInstance()->getPlayerExact($args[1]);
            if($target === null){
                $sender->sendMessage("§c§l> §r§7That player was not found.");
                return true;
            }
        }else{
            $target = $sender;
        }

        $xpmanager = $target->getXpManager();
        $amount = (int)$args[0];

        $xpmanager->setXpLevel($xpmanager->getXpLevel() + $amount);
        $sender->sendMessage("§r§b§l>§r§7 You have given " . $target->getName() . " " . $amount . " XP levels.");

        return true;
    }

}