<?php

declare(strict_types=1);

namespace AndreasHGK\Core\mine;

use pocketmine\Server;

class RegenerationObserver {

    public static RegenerationObserver $instance;

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected array $observers = [];

    protected bool $globalObserve = false;

    protected int $globalResetCount = 0;

    protected int $completed = 0;

    public function isGlobalObserve() : bool {
        return $this->globalObserve;
    }

    public function isGlobalReset() : bool {
        return $this->globalResetCount > 0;
    }

    public function setGlobalReset(int $mineCount) : void {
        $this->observers = [];
        $this->globalResetCount = $mineCount;
    }

    public function setGlobalObserve(bool $bool = true) : void {
        $this->globalObserve = $bool;
    }

    public function addObserver(int $mineId, string $player) : void {
        $this->observers[$mineId][$player] = $player;
    }

    public function getObservers(int $mineId) : array {
        return $this->observers[$mineId];
    }

    public function completeRegeneration(int $mineId) : void {
        if($this->globalResetCount > 0) {
            ++$this->completed;
            if($this->completed = $this->globalResetCount){
                $this->completed = 0;
                $this->globalResetCount = 0;
                if($this->globalObserve){
                    foreach(Server::getInstance()->getOnlinePlayers() as $player){
                        $player->sendMessage("§r§8[§bNF§8] §r§7All mines have been reset.");
                    }
                }

                $this->globalObserve = false;
            }

            return;
        }

        $observers = isset($this->observers[$mineId]) ? $this->observers[$mineId] : [];
        $this->observers[$mineId] = [];
        $mine = MineManager::getInstance()->get($mineId);
        foreach ($observers as $name){
            $player = Server::getInstance()->getPlayerExact($name);
            if($player === null) {
                continue;
            }

            $player->sendMessage("§r§b§l> §r§7Mine §b".$mine->getName()."§r§7 has been regenerated.");
        }
    }

    public function completeClear(int $mineId) : void {
        $observers = isset($this->observers[$mineId]) ? $this->observers[$mineId] : [];
        $this->observers[$mineId] = [];
        $mine = MineManager::getInstance()->get($mineId);
        foreach ($observers as $name){
            $player = Server::getInstance()->getPlayerExact($name);
            if($player === null) {
                continue;
            }

            $player->sendMessage("§r§b§l> §r§7Mine §b".$mine->getName()."§r§7 has been cleared.");
        }
    }
}