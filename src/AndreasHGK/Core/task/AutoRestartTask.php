<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\Core;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class AutoRestartTask extends Task {

    public $time = 6*3601;

    public $lastTime = 0;

    public $lastAnnounce = 0;

    public function getInterval() : int {
        return 12;
    }

    public function onRun() : void {
        $this->time = Core::$restart; //cba to rename it
        if($this->lastTime === 0) $this->lastTime = microtime(true) - 1;

        $diff = microtime(true) - $this->lastTime;
        $this->lastTime = microtime(true);
        //var_dump($diff);
        Core::$restart -= $diff;

        switch ((int)$this->time){
            case 24*3600:
            case 12*3600:
            case 11*3600:
            case 10*3600:
            case 9*3600:
            case 8*3600:
            case 7*3600:
            case 6*3600:
            case 5*3600:
            case 4*3600:
            case 3*3600:
            case 2*3600:
                $str = ((int)$this->time/3600)." hours";
                $this->lastAnnounce = (int)$this->time;
                break;
            case 1*3600:
                $str = "1 hour";

                break;
            case 1800:
                $str = "30 minutes";
                break;
            case 900:
                $str = "15 minutes";
                break;
            case 600:
                $str = "10 minutes";
                break;
            case 300:
                $str = "5 minutes";
                break;
            case 180:
                $str = "3 minutes";
                break;
            case 120:
                $str = "2 minutes";
                break;
            case 60:
                $str = "1 minute";
                break;
            case 30:
            case 10:
            case 5:
            case 4:
            case 3:
            case 2:
                $str = (int)$this->time." seconds";
                break;
            case 1:
                $str = "1 second";
                break;
        }

        if((int)$this->time === 0){
            Server::getInstance()->broadcastMessage("§r§8[§bNF§8]§r§7 The server is now restarting...");
            Core::getInstance()->restart();
            return;
        }

        if(!isset($str)) {
            return;
        }

        if($this->lastAnnounce === (int)$this->time) return;
        $this->lastAnnounce = (int)$this->time;
        Server::getInstance()->broadcastMessage("§r§8[§bNF§8]§r§7 The server is restarting in §b".$str."§r§7.");
    }

}