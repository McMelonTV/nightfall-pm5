<?php

declare(strict_types=1);

namespace AndreasHGK\Core\lottery;

use AndreasHGK\Core\user\UserManager;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class LotteryTask extends Task {

    public $lottery;

    public function __construct(){
        $this->lottery = Lottery::getInstance();
    }

    public function getInterval() : int {
        return 20;
    }

    public function onRun() : void {
        $time = $this->lottery->getTime();
        $time -= 1;
        $this->lottery->setTime($time);
        if($time <= 0){
            if(empty($this->lottery->getTickets())) {
                $this->lottery->reset();
                return;
            }

            $winner = $this->lottery->drawWinner();

            $player = Server::getInstance()->getOfflinePlayer($winner);
            if($player === null) {
                $this->lottery->refundAll();
                Server::getInstance()->broadcastMessage("§r§8[§bNF§8] §r§7There was an error drawing a lottery ticket. Everyone has been refunded.");
                return;
            }

            $user = UserManager::getInstance()->get($player);
            if($user === null) {
                $this->lottery->refundAll();
                Server::getInstance()->broadcastMessage("§r§8[§bNF§8] §r§7There was an error drawing a lottery ticket. Everyone has been refunded.");
                return;
            }

            $user->addMoney($this->lottery->getTotalMoney());
            if(!$user->isOnline()){
                UserManager::getInstance()->save($user);
            }
            Server::getInstance()->broadcastMessage("§r§8[§bNF§8] §r§b{$player->getName()} §r§7won the jackpot! They received §b\${$this->lottery->getTotalMoney()}§r§7.");

            $this->lottery->reset();
        }
    }
}