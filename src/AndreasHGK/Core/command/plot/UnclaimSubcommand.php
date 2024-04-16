<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\plot;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class UnclaimSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("unclaim", "unclaim a plot", "unclaim [x] [z]", "nightfall.command.plot.unclaim");
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

        $user = UserManager::getInstance()->getOnline($sender);

        if(isset($args[0]) && isset($args[1])){
            $plotX = $args[0];
            $plotZ = $args[1];
            if(!is_numeric($plotX) || !is_numeric($plotZ)){
                $sender->sendMessage("§r§c§l>§r§7 Please enter a valid plotX and plotZ coordinate.");
                return true;
            }
            $plotX = (int)$plotX;
            $plotZ = (int)$plotZ;
            if(!PlotManager::getInstance()->get($plotX, $plotZ)->isClaimed()){
                $sender->sendMessage("§r§c§l>§r§7 This plot isn't claimed!");
                return true;
            }
            if(!$user->getAdminMode() && PlotManager::getInstance()->get($plotX, $plotZ)->getOwner() !== $sender->getName()){
                $sender->sendMessage("§r§c§l>§r§7 You can't unclaim this plot!");
                return true;
            }
        }elseif(isset($args[0]) xor isset($args[1])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter either both plot coordinates or no coordinates.");
            return true;
        }else{
            $x = (int)floor($sender->getLocation()->getX());
            $z = (int)floor($sender->getLocation()->getZ());
            if(PlotManager::getInstance()->getPlotAt($x, $z) === null){
                $sender->sendMessage("§r§c§l>§r§7 Please stand on a plot.");
                return true;
            }
            if(!PlotManager::getInstance()->isClaimed($x, $z)){
                $sender->sendMessage("§r§c§l>§r§7 This plot is already unclaimed.");
                return true;
            }
            if(PlotManager::getInstance()->getPlotAt($x, $z)->getOwner() !== $sender->getName()){
                $sender->sendMessage("§r§c§l>§r§7 You can't unclaim this plot!");
                return true;
            }
            $plot = PlotManager::getInstance()->getPlotAt($x ,$z);
            $plotX = $plot->getPlotX();
            $plotZ = $plot->getPlotZ();
        }

        PlotManager::getInstance()->unclaim($plotX, $plotZ);

        $sender->sendMessage("§r§b§l> §r§7You unclaimed the plot at §b".$plotX."§8:§b".$plotZ."§r§7.");
        return true;
    }
}