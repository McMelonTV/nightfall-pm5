<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\IntUtils;
use AndreasHGK\Core\utils\TimeUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\OfflinePlayer;
use pocketmine\player\Player;
use pocketmine\Server;

class StatsCommand extends Executor{

    public function __construct(){
        parent::__construct("stats", "see someones stats", "/stats [player]", "nightfall.command.stats", ["profile"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, true, false);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player && !isset($args[0])){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        if(isset($args[0])){
            $player = Server::getInstance()->getOfflinePlayer($args[0]);
            if(!$player->hasPlayedBefore()){
                $sender->sendMessage("§c§l> §r§7That player was not found.");
                return true;
            }

            $user = UserManager::getInstance()->get($player);
            if($user === null){
                $sender->sendMessage("§c§l> §r§7That player was not found.");
                return true;
            }
        }else{
            $player = $sender;
            $user = UserManager::getInstance()->get($sender);
        }

        $string = "§8§l<--§bNF§8-->§r".
            "\n§b ".$user->getName()."§r§7's stats§r".
            "\n§b > §r§7Rank: §b".$user->getRank()->getRank()->getPrefix()."§r".
            "\n§b > §r§7Mine: §b".$user->getMineRank()->getTag()."§r".
            "\n§b > §r§7Gang: §b".($user->getGang() === null ? "None" : $user->getGang()->getName())."§r".
            "\n§b > §r§7Prestige: §b".IntUtils::toRomanNumerals($user->getPrestige())."§r".
            "\n§b > §r§7Money: §b$".$user->getBalance()."§r".
            "\n§b > §r§7Prestige points: §b".$user->getPrestigePoints()."§opp§r".
            "\n§b > §r§7Kills: §b".$user->getKills()."§r".
            "\n§b > §r§7Deaths: §b".$user->getDeaths()."§r".
            "\n§b > §r§7K/D Ratio: §b".round($user->getKDR(), 2)."§r".
            "\n§b > §r§7Votes: §b".$user->getVotes()."§r".
            "\n§b > §r§7Total money earned: §b".$user->getTotalEarnedMoney()."§r".
            "\n§b > §r§7Total blocks mined: §b".$user->getMinedBlocks()."§r".
            "\n§b > §r§7Join date: §b".date("Y-m-d H:i:s", (int)($player->getFirstPlayed()/1000))."§r".
            "\n§b > §r§7Last seen: §b".($user->isOnline() ? "§bnow" : date("Y-m-d H:i:s", (int)($player->getLastPlayed()/1000)))."§r".
            "\n§b > §r§7Total online time: §b" . TimeUtils::intToShortTimeString($user->getTotalOnlineTime()) . "§r".
            "\n§r§8§l<--++-->⛏";

        $sender->sendMessage($string);
        return true;
    }
}