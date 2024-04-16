<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\mine\Mine;
use AndreasHGK\Core\mine\MineManager;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class AutoMineRegenerateTask extends Task {

    /** @var Mine[] */
    public array $mines = [];

    public function getInterval() : int {
        return 11;
    }

    public function onRun() : void {
        /*if(empty($this->mines)) $this->mines = MineManager::getInstance()->getAll();
        if($currentTick < 100)return;
        $mine = array_shift($this->mines);
        if($mine->isRegenerating()) return;
        if($mine->isDisabled()) return;
        $totalBlocks = $mine->getTotalBlocks();
        $air = $mine->countAirBlocks();
        if(($air/$totalBlocks)*100 > 90){
            Server::getInstance()->getLogger()->info("regenerating mine ".$mine->getName());
            $mine->regenerate();
        }*/
        if(empty($this->mines)) {
            $this->mines = MineManager::getInstance()->getAll();
        }

        $mine = array_shift($this->mines);
        if($mine === null){
            return;
        }
        if($mine->isRegenerating()) {
            return;
        }

        if($mine->isDisabled()) {
            return;
        }

        $totalBlocks = $mine->getTotalBlocks();
        $blockCount = $mine->getBlockCount();
        if($blockCount/$totalBlocks <= 0.15){
            Server::getInstance()->getLogger()->info("regenerating mine ".$mine->getName());
            $mine->regenerate();
        }
    }
}