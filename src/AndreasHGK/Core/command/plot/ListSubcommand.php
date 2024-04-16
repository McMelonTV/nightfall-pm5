<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\plot;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class ListSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("list", "list someones plots", "list [player]", "nightfall.command.plot.list");
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
            $targetPlayer = $sender;
            $target = UserManager::getInstance()->getOnline($sender);
        }else{
            $targetPlayer = Server::getInstance()->getPlayerByPrefix($args[0]);
            if($targetPlayer === null){
                $targetPlayer = Server::getInstance()->getOfflinePlayer($args[0]);
            }

            if($targetPlayer === null || !$targetPlayer->hasPlayedBefore()){
                $sender->sendMessage("§r§c§l>§r§7 That player was not found.");
                return true;
            }

            $target = UserManager::getInstance()->get($targetPlayer);
        }

        if($target->countPlots() === 0){
            if($targetPlayer === $sender){
                $sender->sendMessage("§r§c§l>§r§7 You don't have any plots.");
                return true;
            }
            $sender->sendMessage("§r§c§l>§r§7 This player does not have any plots.");
            return true;
        }

        $string = "§8§l<--§bNF§8--> ".
            "\n§r§7§7 Plot list for §r§b".$targetPlayer->getName()."§r §8(".$target->countPlots()." out of ".$target->getMaxPlots()." max plots)";

        foreach($target->getOwnedPlots() as $plot){
            if($plot->isNamed()){
                $string .= "\n§r§8 > §r§7Plot §b".$plot->getName()."§r§7 at §b".$plot->getPlotX()."§8:§b".$plot->getPlotZ()."§r";
                continue;
            }
            $string .= "\n§r§8 > §r§7Unnamed plot at §b".$plot->getPlotX()."§8:§b".$plot->getPlotZ()."§r";
        }

        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");
        return true;
    }

}