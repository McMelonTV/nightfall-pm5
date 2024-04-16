<?php

namespace AndreasHGK\Core\command\plot;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\user\UserManager;
use Closure;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;

class AutoclaimCommand extends Subcommand{

    public function __construct(){
        parent::__construct("autoclaim", "auto claim a plot", "autoclaim", "nightfall.command.plot.autoclaim", ["auto", "a"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        $world = PlotManager::getInstance()->getWorld();
        if($sender->getWorld() !== $world) {
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command in the plot world.");
            return true;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        if($user->getEffectiveMaxPlots() <= $user->countPlots()){
            $sender->sendMessage("§r§c§l>§r§7 You have reached your max plots of §c".$user->getEffectiveMaxPlots()."§r§7.");
            return true;
        }

        $plot = PlotManager::getInstance()->getUnclaimedPlot();
        if($plot === null){
            $sender->sendMessage("§r§c§l>§r§7 All of the plots are taken.");
            return true;
        }

        $vector = $plot->getVectorB()->subtract(1.5, 0, 1.5);

        $pos = new Position($vector->getX(), 65, $vector->getZ(), $world);
        $world->orderChunkPopulation($vector->getFloorX() >> 4, $vector->getFloorZ() >> 4, null)->onCompletion(Closure::fromCallable(static function() use($sender, $plot, $pos) : void{
            $user = UserManager::getInstance()->getOnline($sender);
            PlotManager::getInstance()->claim(($plotX = $plot->getPlotX()), ($plotZ = $plot->getPlotZ()), $user);

            $sender->teleport($pos, -45, 0);
            $sender->sendMessage("§r§a§l> §r§7You claimed the plot at §a".$plotX."§8:§a".$plotZ."§r§7.");
        }), Closure::fromCallable(static function() : void{}));

        return true;
    }
}