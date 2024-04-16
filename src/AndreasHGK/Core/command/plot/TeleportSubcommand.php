<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\plot;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\ui\PlotTeleportForm;
use AndreasHGK\Core\user\UserManager;
use Closure;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;

class TeleportSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("teleport", "teleport to a plot", "teleport [x] [z]", "nightfall.command.plot.teleport", ["tp"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        //$user = UserManager::getInstance()->getOnline($sender);

        if(isset($args[0]) && isset($args[1])){
            $plotX = $args[0];
            $plotZ = $args[1];
            if(!is_numeric($plotX) || !is_numeric($plotZ)){
                $sender->sendMessage("§r§c§l>§r§7 Please enter a valid plotX and plotZ coordinate.");
                return true;
            }
            $plotX = min(max((int)$plotX, -1000), 1000);
            $plotZ = min(max((int)$plotZ, -1000), 1000);
        }elseif(isset($args[0]) xor isset($args[1])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter both plot coordinates.");
            return true;
        }else{
            $user = UserManager::getInstance()->getOnline($sender);
            if(empty($user->getOwnedPlots())){
                $sender->sendMessage("§r§c§l>§r§7 You don't have any plots yet. Do §c/plots §r§7and claim one with §c/p claim§r§7.");
                return true;
            }

            PlotTeleportForm::sendTo($sender);
            return true;
        }

        $plot = PlotManager::getInstance()->get($plotX, $plotZ);

        $vector = $plot->getVectorB()->subtract(1.5, 0, 1.5);

        $world = PlotManager::getInstance()->getWorld();
        $pos = new Position($vector->getX(), 65, $vector->getZ(), $world);
        $world->orderChunkPopulation($vector->getFloorX() >> 4, $vector->getFloorZ() >> 4, null)->onCompletion(Closure::fromCallable(static function() use ($sender, $plotX, $plotZ, $pos) : void{
            if(!$sender->isConnected()){
                return;
            }
            $sender->teleport($pos, -45, 0);
            $sender->sendMessage("§r§a§l> §r§7You have been teleported to plot §a" . $plotX . "§8:§a" . $plotZ . "§r§7.");
        }), Closure::fromCallable(static function() : void{}));
        return true;
    }
}