<?php

declare(strict_types=1);

namespace AndreasHGK\Core\lottery;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\user\OfflineUser;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\Server;

class Lottery{

    public const TICKET_PRICE = 100000;

    public const DRAW_TIME = 3600;

    /** @var self */
    private static $instance;

    public static function getInstance() : self {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $ticketCount = 0;

    private $tickets = [];

    private $time = self::DRAW_TIME;

    public function getTotalMoney() : int {
        return (int)(($this->ticketCount * self::TICKET_PRICE) * 0.75);
    }

    public function getTime() : int {
        return $this->time;
    }

    public function setTime(int $time) : void {
        $this->time = $time;
    }

    public function getTicketCount() : int {
        return $this->ticketCount;
    }

    public function getTickets() : array {
        return $this->tickets;
    }

    public function setTickets(array $tickets) : void {
        $this->tickets = $tickets;
    }

    public function buyTickets(User $user, int $count) : void {
        if(!isset($this->tickets[$user->getName()])){
            $this->tickets[$user->getName()] = 0;
        }
        $this->tickets[$user->getName()] += $count;
        $this->ticketCount += $count;
    }

    public function drawWinner() : string {
        $allTickets = [];
        foreach ($this->tickets as $buyer => $ticket){
            $allTickets = array_merge($allTickets, array_fill(0, $ticket, $buyer));
        }

        shuffle($allTickets);
        return $allTickets[array_rand($allTickets)];
    }

    public function reset() : void {
        $this->tickets = [];
        $this->ticketCount = 0;
        $this->time = self::DRAW_TIME;
    }

    /**
     * This resets the tickets, and gives everyone their money back. This gets called on server shutdown.
     */
    public function refundAll() : void {
        foreach($this->tickets as $buyer => $count) {
            $player = Server::getInstance()->getOfflinePlayer($buyer);
            if($player === null){
                continue;
            }

            $user = UserManager::getInstance()->get($player);
            if($user === null){
                continue;
            }

            $user->addMoney(self::TICKET_PRICE * $count);
            if(!$user->isOnline()){
                UserManager::getInstance()->save($user);
            }
        }
        $this->reset();
    }

    public function setup() : void {
        Core::getInstance()->getScheduler()->scheduleRepeatingTask(new LotteryTask(), 20);
    }

}