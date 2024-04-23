<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\manager\DataManager;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class BroadcastTask extends Task {

    private static $instance;

    public static function getInstance() : ?self {
        return self::$instance;
    }

    private $time = 0;

    private $messages = 0;

    public function addMessage() : void {
        ++$this->messages;
    }

    public function getInterval() : int {
        return 20;
    }

    public function onRun() : void {
        ++$this->time;
        if($this->time >= 60*6) {
            $this->broadcast();
            return;
        }

        if($this->time >= 60*5 && $this->messages >= 10) {
            $this->broadcast();
            return;
        }

        if($this->time >= 60*4 && $this->messages >= 40) {
            $this->broadcast();
            return;
        }

        if($this->time >= 60*3 && $this->messages >= 75) {
            $this->broadcast();
            return;
        }

        if($this->time >= 60 && $this->messages >= 150) {
            $this->broadcast();
            return;
        }
    }

    public function broadcast() : void {
        $this->time = 0;
        $this->messages = 0;
        $array = DataManager::getKey(DataManager::BROADCAST, "broadcasts", []);
        Server::getInstance()->broadcastMessage($array[array_rand($array)], Server::getInstance()->getOnlinePlayers());
    }

    public function __construct(){
        self::$instance = $this;
    }
}