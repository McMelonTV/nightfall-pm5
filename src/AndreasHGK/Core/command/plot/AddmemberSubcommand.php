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

class AddmemberSubcommand extends Subcommand{
    public function __construct(){
        parent::__construct("addmember", "add a member to a plot", "addmember <member> [x] [z]", "nightfall.command.plot.addmember", ["trust", "add"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command in-game.");
            return true;
        }

        if($sender->getWorld() !== PlotManager::getInstance()->getWorld()) {
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command in the plot world.");
            return true;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a member to add.");
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

        $name = $target->getName();
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

            if(!$user->getAdminMode() && $plot->getOwner() !== $sender->getName()){
                $sender->sendMessage("§r§c§l>§r§7 You can't add members to this plot!");
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

            if(!$user->getAdminMode() && $plot->getOwner() !== $sender->getName()){
                $sender->sendMessage("§r§c§l>§r§7 You can't add members to this plot!");
                return true;
            }

            $plotX = $plot->getPlotX();
            $plotZ = $plot->getPlotZ();
        }

        if($plot->isMember($name)){
            $sender->sendMessage("§r§c§l>§r§7 This person is already added to the plot.");
            return true;
        }

        $plot->addMember($name);

        $sender->sendMessage("§r§b§l> §r§7You added §b".$name."§7 to the plot at §b".$plotX."§8:§b".$plotZ."§7.");
        if($target instanceof Player){
            $target->sendMessage("§r§b§l> §r§7You have been added to §b".$plot->getOwner()."§7's plot at §b".$plotX."§8:§b".$plotZ."§7.");
        }

        return true;
    }

}