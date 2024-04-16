<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\plot;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\plot\PlotManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class BlockplayerSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("blockplayer", "block a player", "block <player>", "nightfall.command.plot.blockplayer", ["block"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        if($sender->getWorld() !== PlotManager::getInstance()->getWorld()) {
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command in the plot world.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage(("§r§c§l>§r§7 Please enter a player to block."));
            return true;
        }

        $member = array_shift($args);

        $target = Server::getInstance()->getPlayerByPrefix($member);
        if($target === null){
            if(Server::getInstance()->hasOfflinePlayerData($member)){
                $target = Server::getInstance()->getOfflinePlayer($member);
            }else{
                $sender->sendMessage("§r§c§l>§r§7 That player has never connected.");
                return true;
            }
        }

        $name = strtolower($target->getName());
        if(isset($args[0]) && isset($args[1])){
            $plotX = $args[0];
            $plotZ = $args[1];
            if(!is_numeric($plotX) || !is_numeric($plotZ)){
                $sender->sendMessage("§r§c§l>§r§7 Please enter a valid plotX and plotZ coordinate.");
                return true;
            }

            $plotX = (int)$plotX;
            $plotZ = (int)$plotZ;
            if(!($plot = PlotManager::getInstance()->get($plotX, $plotZ))->isClaimed()){
                $sender->sendMessage("§r§c§l>§r§7 This plot isn't claimed!");
                return true;
            }

            if($plot->getOwner() !== $sender->getName()){
                $sender->sendMessage("§r§c§l>§r§7 You can't block players at this plot!");
                return true;
            }
        }elseif(isset($args[0]) xor isset($args[1])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter either both plot coordinates or no coordinates.");
            return true;
        }else{
            $x = (int)floor($sender->getLocation()->getX());
            $z = (int)floor($sender->getLocation()->getZ());
            if(($plot = PlotManager::getInstance()->getPlotAt($x, $z)) === null){
                $sender->sendMessage("§r§c§l>§r§7 Please stand on a plot.");
                return true;
            }

            if($plot->getOwner() !== $sender->getName()){
                $sender->sendMessage("§r§c§l>§r§7 You can't block players at this plot!");
                return true;
            }

            $plotX = $plot->getPlotX();
            $plotZ = $plot->getPlotZ();
        }

        if(strtolower($sender->getName()) === $name){
            $sender->sendMessage("§r§c§l>§r§7 You cannot block yourself from your own plot.");
            return true;
        }

        if($plot->isBlocked($name)){
            $sender->sendMessage("§r§c§l>§r§7 This person is already blocked at the plot.");
            return true;
        }

        $plot->blockUser($name);

        $sender->sendMessage("§r§b§l> §r§7You blocked §b".$target->getName()." §7from the plot at §b".$plotX."§8:§b".$plotZ."§r§7.");
        if($target instanceof Player){
            $target->sendMessage("§r§b§l> §r§7You have been blocked from §b".$plot->getOwner()."§7's plot at §b".$plotX."§8:§b".$plotZ."§r§7.");
        }
        return true;
    }
}