<?php

declare(strict_types=1);

namespace AndreasHGK\Core\koth;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use function intdiv;

final class KothTask extends Task {

    private int $i = 0;

    public function onRun() : void{
        $kothManager = KothManager::getInstance();
        if(($running = $kothManager->getRunning()) !== null){
            if($running->tick()){
                $kothManager->stop();
            }

            return;
        }

        ++$this->i;

        $time = time();
        $nextKoth = $kothManager->getNextKoth();
        $diff = $nextKoth - $time;
        switch($diff){
            case 60*60*4:
            case 60*60*3:
            case 60*60*2:
                $str = ((string) intdiv(intdiv($diff, 60), 60))." hours";
                break;
            case 60*60:
                $str = "1 hour";
                break;
            case 60*30:
            case 60*10:
            case 60*5:
                $str = ((string) intdiv($diff, 60))." minutes";
                break;
            case 60:
                $str = "1 minute";
                break;
            case 15:
            case 3:
            case 2:
                $str = $diff . " seconds";
                break;
            case 1:
                $str = "1 second";
                break;
        }

        if($time >= $nextKoth){
            $kothManager->startRandom();
        }elseif(isset($str) && $this->i%2 === 0){
            if($kothManager->spawnNext()) {
                Server::getInstance()->broadcastMessage("§r§8§l[§aKOTH§8]§r§7 Spawn KOTH is starting in §a" . $str . "§r§7.");
            }else{
                Server::getInstance()->broadcastMessage("§r§8§l[§aKOTH§8]§r§7 Mine PvP KOTH is starting in §a" . $str . "§r§7.");
            }
        }
    }
}