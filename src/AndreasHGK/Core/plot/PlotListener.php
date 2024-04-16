<?php

declare(strict_types=1);

namespace AndreasHGK\Core\plot;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use function floor;

class PlotListener implements Listener {

    public function onMove(PlayerMoveEvent $ev) : void {
        $player = $ev->getPlayer();
        if($player->getWorld() !== PlotManager::getInstance()->getWorld()) {
            return;
        }

        $to = $ev->getTo();
        $toPlot = PlotManager::getInstance()->getPlotAt((int)floor($to->x), (int)floor($to->z));
        if($toPlot === null) {
            return;
        }

        $from = $ev->getFrom();
        $fromPlot = PlotManager::getInstance()->getPlotAt((int)floor($from->x), (int)floor($from->z));
        if($fromPlot !== null && $fromPlot->getId() === $toPlot->getId()) {
            return;
        }

        if($toPlot->isBlocked($player->getName())){
            $player->sendTip("§8[§bNF§8]\n§r§7You are blocked from this plot");
            $ev->cancel();
            return;
        }

        $player->sendTitle("§8[§b".$toPlot->getPlotX()."§8:§b".$toPlot->getPlotZ()."§8]",($toPlot->isNamed() ? "§r§b".$toPlot->getName()."\n§7" : "")."§r§7".($toPlot->getOwner() === "" ? "unclaimed" : "by ".$toPlot->getOwner()));
    }
}