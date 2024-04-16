<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\rank\MineRankManager;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\IntUtils;
use AndreasHGK\Core\utils\MineUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class RanksCommand extends Executor{

    public function __construct(){
        parent::__construct("ranks", "see the mines", "/ranks", "nightfall.command.ranks");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        $string = "§8§l<--§bNF§8--> "."\n§r§7§7 Available mines";
        $user = UserManager::getInstance()->get($sender);
        foreach(MineRankManager::getInstance()->getAll() as $mr){
            $price = (int)($mr->getPrice() + ($mr->getPrice() * 0.6 * ($user->getPrestige() - 1 )));
            if($price <= 0){
                $string .= "\n§r §a§l> §r".$mr->getTag()."§r§7 mine";
            }elseif($mr->isHigherThan($user->getMineRank())){
                $string .= "\n§r §c§l> §r".$mr->getTag()."§r§7 mine §8[§b$".IntUtils::shortNumberRounded($price)."§r§8]";
            }else{
                $string .= "\n§r §a§l> §r".$mr->getTag()."§r§7 mine §8[§b$".IntUtils::shortNumberRounded($price)."§r§8]";
            }
        }

        $string .= "\n§r §c§l> §r§7§lPrestige ".IntUtils::toRomanNumerals($user->getPrestige()+1)." §r§8[§b\$".IntUtils::shortNumberRounded(MineUtils::getPrestigePrice($user->getPrestige()+1))."§r§l§8]";
        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");
        return true;
    }

}