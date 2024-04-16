<?php

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\Core;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ClearEntitiesTask extends Task{

    public $time = 610;

    public $lastTime = 0;

    public function getInterval() : int {
        return 10;
    }

    public function onRun() : void {
        if($this->lastTime === 0) $this->lastTime = microtime(true) - 1;

        $diff = microtime(true) - $this->lastTime;

        $this->time -= $diff;
        $this->lastTime = microtime(true);
        switch ($this->time){
            case 900:
                $str = "15 minutes";
                break;
            case 600:
                $str = "10 minutes";
                break;
            case 300:
                $str = "5 minutes";
                break;
            case 60:
                $str = "1 minute";
                break;
            case 30:
            case 10:
            case 5:
                $str = $this->time." seconds";
                break;
            case 1:
                $str = "1 second";
                break;
        }

        if($this->time === 0){
            Server::getInstance()->broadcastMessage("§r§8[§bNF§8]§r§7 Now clearing all item entities...");
            Core::getInstance()->clearItemEntities();
            $this->time = 610;
            return;
        }

        if(!isset($str)) {
            return;
        }

        Server::getInstance()->broadcastMessage("§r§8[§bNF§8]§r§7 All item entities clearing in §b".$str."§r§7.");
    }
}