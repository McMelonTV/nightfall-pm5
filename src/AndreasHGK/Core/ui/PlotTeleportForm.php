<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\user\UserManager;
use Closure;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\world\Position;

class PlotTeleportForm {

    public static function sendTo(Player $sender) : void {
        $ui = new SimpleForm(static function (Player $sender, ?string $data) {
            if($data === null){
                return;
            }

            if(PlotManager::getInstance()->getById($data) === null){
                $sender->sendMessage("§r§c§l> §r§7Could not teleport you to that plot.");
                return;
            }

            $plot = PlotManager::getInstance()->getById($data);

            $vector = $plot->getVectorB()->subtract(1.5, 0, 1.5);

            $world = PlotManager::getInstance()->getWorld();
            $pos = new Position($vector->getX(), 65, $vector->getZ(), $world);
            $world->orderChunkPopulation($vector->getFloorX() >> 4, $vector->getFloorZ() >> 4, null)->onCompletion(Closure::fromCallable(static function() use($sender, $plot, $pos) : void{
                $sender->teleport($pos, -45, 0);
                $sender->sendMessage("§r§a§l> §r§7You have been teleported to plot §a".$plot->getPlotX()."§8:§a".$plot->getPlotZ()."§r§7.");
            }), Closure::fromCallable(static function() : void{}));
        });

        $ui->setTitle("§bplot teleport menu");
        $ui->setContent("§r§7Select the plot you want to teleport to.");

        $user = UserManager::getInstance()->getOnline($sender);

        foreach($user->getOwnedPlots() as $plot){
            if($plot->isNamed()){
                $ui->addButton("§r§7Plot §b".$plot->getName()." §r§7at §b".$plot->getPlotX()."§8:§b".$plot->getPlotZ()."§r§7\n§8[§bOwner§8]", -1, "", $plot->getId());
                continue;
            }

            $ui->addButton("§r§7Unnamed plot at §b".$plot->getPlotX()."§8:§b".$plot->getPlotZ()."§r§7\n§8[§bOwner§8]", -1, "", $plot->getId());
        }

        foreach($user->getAccessiblePlots() as $plot){
            if($plot->getOwner() === $sender->getName()) {
                continue;
            }

            if($plot->isNamed()){
                $ui->addButton("§r§8Plot §b".$plot->getName()." §r§8at §b".$plot->getPlotX()."§8:§b".$plot->getPlotZ()."§r\n§8[§bMember§8]", -1, "", $plot->getId());
                continue;
            }
            $ui->addButton("§r§8Unnamed plot at §b".$plot->getPlotX()."§8:§b".$plot->getPlotZ()."§r\n§8[§bMember§8]", -1, "", $plot->getId());
        }

        $sender->sendForm($ui);
    }
}