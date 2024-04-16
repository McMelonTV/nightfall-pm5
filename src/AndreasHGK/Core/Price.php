<?php

declare(strict_types=1);

namespace AndreasHGK\Core;

use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\item\Item;
use pocketmine\player\Player;

class Price {

    private $steeldust;

    private $obsidianShard;

    private $magicdust;

    private $stardust;

    private $money;

    private $prestigePoints;

    private $xpLevels;

    public function toString() : string {
        $str = "";
        if($this->getSteeldust() > 0){
            $str .= "§r§b".$this->steeldust." steeldust";
        }

        if($this->getObsidianShard() > 0){
            if($str !== "") {
                $str .= " ";
            }

            $str .= "§r§b".$this->obsidianShard." obsidian shards";
        }

        if($this->getMagicdust() > 0){
            if($str !== "") {
                $str .= " ";
            }

            $str .= "§r§b".$this->magicdust." magicdust";
        }

        if($this->getStardust() > 0){
            if($str !== "") {
                $str .= " ";
            }

            $str .= "§r§b".$this->stardust." stardust";
        }

        if($this->getMoney() > 0){
            if($str !== "") {
                $str .= " ";
            }

            $str .= "§r§b$".$this->money;
        }

        if($this->getPrestigePoints() > 0){
            if($str !== "") {
                $str .= " ";
            }

            $str .= "§r§b".$this->prestigePoints."§opp";
        }

        if($this->getXPLevels() > 0){
            if($str !== "") {
                $str .= " ";
            }

            $str .= "§r§b".$this->xpLevels." levels";
        }

        if($str === ""){
            $str = "§7Free";
        }

        return $str;
    }

    public function pay(Player $player) : void {
        $steeldust = CustomItemManager::getInstance()->get(CustomItem::STEELDUST);
        $obsidianshard = CustomItemManager::getInstance()->get(CustomItem::OBSIDIANSHARD);
        $magicdust = CustomItemManager::getInstance()->get(CustomItem::MAGICDUST);
        $stardust = CustomItemManager::getInstance()->get(CustomItem::STARDUST);

        $user = UserManager::getInstance()->getOnline($player);
        $inv = $player->getInventory();
        if($this->getSteeldust() > 0){
            $inv->removeItem($steeldust->getItem()->setCount($this->getSteeldust()));
        }

        if($this->getObsidianShard() > 0){
            $inv->removeItem($obsidianshard->getItem()->setCount($this->getObsidianShard()));
        }

        if($this->getMagicdust() > 0){
            $inv->removeItem($magicdust->getItem()->setCount($this->getMagicdust()));
        }

        if($this->getStardust() > 0){
            $inv->removeItem($stardust->getItem()->setCount($this->getStardust()));
        }

        if($this->getMoney() > 0){
            $user->takeMoney($this->getMoney());
        }

        if($this->getPrestigePoints() > 0){
            $user->setPrestigePoints($user->getPrestigePoints() - $this->prestigePoints);
        }

        if($this->getXPLevels() > 0){
            $player->getXpManager()->subtractXpLevels($this->xpLevels);
        }
    }

    public function canAfford(Player $player) : bool {
        $steeldust = CustomItemManager::getInstance()->get(CustomItem::STEELDUST);
        $obsidianshard = CustomItemManager::getInstance()->get(CustomItem::OBSIDIANSHARD);
        $magicdust = CustomItemManager::getInstance()->get(CustomItem::MAGICDUST);
        $stardust = CustomItemManager::getInstance()->get(CustomItem::STARDUST);

        $user = UserManager::getInstance()->getOnline($player);
        $inv = $player->getInventory();
        if($this->getSteeldust() > 0){
            $required = $this->getSteeldust();
            $count = 0;
            $slots = $inv->all(clone $steeldust->getItem());
            foreach($slots as $slot){
                if(!$slot instanceof Item) {
                    continue;
                }

                $count += $slot->getCount();
            }
            if($count < $required) {
                return false;
            }
        }

        if($this->getObsidianShard() > 0){
            $required = $this->getObsidianShard();
            $count = 0;
            $slots = $inv->all(clone $obsidianshard->getItem());
            foreach($slots as $slot){
                if(!$slot instanceof Item) {
                    continue;
                }

                $count += $slot->getCount();
            }
            if($count < $required) {
                return false;
            }
        }

        if($this->getMagicdust() > 0){
            $required = $this->getMagicdust();
            $count = 0;
            $slots = $inv->all(clone $magicdust->getItem());
            foreach($slots as $slot){
                if(!$slot instanceof Item) {
                    continue;
                }

                $count += $slot->getCount();
            }

            if($count < $required) {
                return false;
            }
        }
        if($this->getStardust() > 0){
            $required = $this->getStardust();
            $count = 0;
            $slots = $inv->all(clone $stardust->getItem());
            foreach($slots as $slot){
                if(!$slot instanceof Item) {
                    continue;
                }

                $count += $slot->getCount();
            }

            if($count < $required) {
                return false;
            }
        }
        if($this->getMoney() > 0 && $user->getBalance() < $this->money) {
            return false;
        }

        if($this->getPrestigePoints() > 0 && $user->getPrestigePoints() < $this->getPrestigePoints()) {
            return false;
        }

        if($this->getXPLevels() > 0 && $player->getXpManager()->getXpLevel() < $this->getXPLevels()) {
            return false;
        }

        return true;
    }

    public function getSteeldust() : int {
        return $this->steeldust;
    }

    public function setSteeldust(int $dust) : void {
        $this->steeldust = $dust;
    }

    public function getObsidianShard() : int {
        return $this->obsidianShard;
    }

    public function setObsidianShard(int $shard) : void {
        $this->obsidianShard = $shard;
    }

    public function getMagicdust() : int {
        return $this->magicdust;
    }

    public function setMagicdust(int $magicdust) : void {
        $this->magicdust = $magicdust;
    }

    public function getStardust() : int {
        return $this->stardust;
    }

    public function setStardust(int $stardust) : void {
        $this->stardust = $stardust;
    }

    public function getMoney() : int {
        return $this->money;
    }

    public function setMoney(int $money) : void {
        $this->money = $money;
    }

    public function getPrestigePoints() : int {
        return $this->prestigePoints;
    }

    public function setPrestigePoints(int $points) : void {
        $this->prestigePoints = $points;
    }

    public function getXPLevels() : int {
        return $this->xpLevels;
    }

    public function setXPLevels(int $levels) : void {
        $this->xpLevels = $levels;
    }

    public function __construct(int $steeldust = 0, int $obsidianShard = 0, int $magicdust = 0, int $stardust = 0, int $money = 0, int $prestigePoints = 0, int $xpLevels = 0){
        $this->steeldust = $steeldust;
        $this->obsidianShard = $obsidianShard;
        $this->magicdust = $magicdust;
        $this->stardust = $stardust;
        $this->money = $money;
        $this->prestigePoints = $prestigePoints;
        $this->xpLevels = $xpLevels;
    }
}