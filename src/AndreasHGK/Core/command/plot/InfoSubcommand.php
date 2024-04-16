<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\plot;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\plot\PlotManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InfoSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("info", "get info about a plot", "info [x] [z]", "nightfall.command.plot.info", ["about"]);
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

        if(isset($args[0]) && isset($args[1])){
            $plotX = $args[0];
            $plotZ = $args[1];
            if(!is_numeric($plotX) || !is_numeric($plotZ)){
                $sender->sendMessage("§r§c§l>§r§7 Please enter a valid plotX and plotZ coordinate.");
                return true;
            }

            $plotX = (int)$plotX;
            $plotZ = (int)$plotZ;
            $plot = PlotManager::getInstance()->get($plotX, $plotZ);
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

            $plot = PlotManager::getInstance()->getPlotAt($x ,$z);
            $plotX = $plot->getPlotX();
            $plotZ = $plot->getPlotZ();
        }

        $string = "§8§l<--§bNF§8--> ".
            "\n§r§7§7 Info for plot §b".$plotX."§8:§b".$plotZ."§r";
        if($plot->isClaimed()){
            $string .= "\n§r§8 > §r§7Plot name: §r§b".($plot->isNamed() ? $plot->getName() : "§r§cunnamed");
            $string .= "\n§r§8 > §r§7Owner: §r§b".$plot->getOwner();
            $string .= "\n§r§8 > §r§7Members: §r§b".(empty($plot->getMembers()) ? "§cnone" : implode("§r§7,§r§b ", $plot->getMembers()));
        }else{
            $string .= "\n§r§8 > §r§7This plot is not claimed.";
        }

        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");
        return true;
    }
}