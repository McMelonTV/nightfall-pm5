<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InfoSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("info", "display info for a gang", "info [gang]", "nightfall.command.gang.info");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        if(!$user->isInGang() && !isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a gang to see info for.");
            return true;
        }

        $gang = $user->getGang();
        if(isset($args[0])){
            $name = implode($args);
            if(!GangManager::getInstance()->exists($name)){
                $sender->sendMessage("§r§c§l>§r§7 No such gang exists.");
                return true;
            }
            $gang = GangManager::getInstance()->getByName($name);
        }

        $memberNamesByRole = [];
        foreach ($gang->getMembers() as $m) {
            $user = $gang->getMember($m);
            if($user === null){
                continue;
            }
            $memberNamesByRole[$user->getGangRank()->name()][] = $user->getName();
        }

        $string = "§8§l<--§bNF§8--> "."\n§r§7§7 Info for gang §b".$gang->getName()."§r";
        $string .= "\n§r§8 > §r§7ID: §b".$gang->getId();
        $string .= "\n§r§8 > §r§7Leader: §b".$gang->getLeader()->getName();
        $string .= "\n§r§8 > §r§7Description: §b".$gang->getDescription();
        $string .= "\n§r§8 > §r§7Date of creation: §b".date("d/m/y", $gang->getCreationDate())." at ".date("h:i", $gang->getCreationDate());
        $string .= "\n§r§8 > §r§7Officers: §b".implode("§7, §b", $memberNamesByRole[GangRank::OFFICER()->name()] ?? []);
        $string .= "\n§r§8 > §r§7Members: §b".implode("§7, §b", $memberNamesByRole[GangRank::MEMBER()->name()] ?? []);
        $string .= "\n§r§8 > §r§7Recruits: §b".implode("§7, §b", $memberNamesByRole[GangRank::RECRUIT()->name()] ?? []);
        $string .= "\n§r§8 > §r§7Allies: §b".implode("§7, §b", $gang->getAllies());
        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");
        return true;
    }
}