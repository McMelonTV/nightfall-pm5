<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\IntUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class BalanceCommand extends Executor {

    public function __construct() {
        parent::__construct("balance", "see your balance", "/balance [player]", "nightfall.command.balance", ["bal", "mymoney", "seemoney"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, true, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if(isset($args[0])){
            $player = Server::getInstance()->getPlayerByPrefix($args[0]);
            if($player === null && UserManager::getInstance()->exist($args[0])){
                $player = Server::getInstance()->getOfflinePlayer($args[0]);
            }
            if($player === null){
                $sender->sendMessage("§c§l> §r§7That player was never connected.");
                return true;
            }
        }else{
            $player = $sender;
        }
        if(!$player instanceof Player){
            $sender->sendMessage("§c§l> §r§7Target needs to be a player.");
            return true;
        }

        $user = UserManager::getInstance()->get($player);
        $sender->sendMessage("§8§l<--§bNF§8--> ".
            "\n§r§7§b ".$player->getName()."§7's balance§r".
            "\n§r §b§l> §r§7Money: §b$".$user->getBalance()."§r §8(§8$".IntUtils::shortNumberRounded($user->getBalance())."§r§8)§r".
            "\n§r §b§l> §r§7Prestige points: §b".$user->getPrestigePoints()."§m§oPP".
            "\n§r§8§l<--++-->⛏");
        return true;
    }
}