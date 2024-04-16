<?php

namespace AndreasHGK\Core\crate;

use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\item\CrateKey;
use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\item\EnchantmentBook;
use AndreasHGK\Core\tag\TagManager;
use AndreasHGK\Core\user\User;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\Server;
use pocketmine\world\Position;

class CrateManager {

    private static $instance;

    /**
     * @var array|Crate[]
     */
    private $crates = [];

    /** @var array|Position[] */
    private $crateLocations = [];

    /**
     * @return array|Position[]
     */
    public function getAllLocations() : array {
        return $this->crateLocations;
    }

    /**
     * @return array|Crate[]
     */
    public function getAll() : array {
        return $this->crates;
    }

    public function get(int $id) : ?Crate {
        return $this->crates[$id] ?? null;
    }

    public function register(Crate $crate) : void {
        $this->crates[$crate->getId()] = $crate;
    }

    public function exists(string $kit) : bool {
        return isset($this->crates[$kit]);
    }

    public function registerDefaults() : void
    {
        $this->register(new Crate(10, "Iron", [
            new CrateItem(130, VanillaItems::PAPER(), "$500 money drop", 500, 500, 0, false),
            new CrateItem(140, VanillaItems::PAPER(), "$1000 money drop", 150, 1000, 0, false),
            new CrateItem(150, VanillaItems::PAPER(), "$1500 money drop", 50, 1500, 0, false),
            new CrateItem(160, "6:1:2", "T2 basic pickaxe", 250, 0, 0, true),
            new CrateItem(170, "13:1:2", "T2 basic sword", 250, 0, 0, true),
            new CrateItem(180, "12:1:2", "T2 basic boots", 250, 0, 0, true),
            new CrateItem(190, "11:1:2", "T2 basic leggings", 250, 0, 0, true),
            new CrateItem(200, "10:1:2", "T2 basic chestplate", 250, 0, 0, true),
            new CrateItem(210, "9:1:2", "T2 basic helmet", 250, 0, 0, true),
            new CrateItem(220, "6:1:4", "T4 basic pickaxe", 100, 0, 0, true),
            new CrateItem(230, "6:1:3", "T3 basic pickaxe", 150, 0, 0, true),
            new CrateItem(240, "13:1:3", "T3 basic sword", 100, 0, 0, true),
            new CrateItem(250, "12:1:3", "T3 basic boots", 100, 0, 0, true),
            new CrateItem(260, "11:1:3", "T3 basic leggings", 100, 0, 0, true),
            new CrateItem(270, "10:1:3", "T3 basic chestplate", 100, 0, 0, true),
            new CrateItem(280, "9:1:3", "T3 basic helmet", 100, 0, 0, true),
            new CrateItem(290, "6:1:5", "T5 basic pickaxe", 25, 0, 0, true),
            new CrateItem(300, "13:1:4", "T4 basic sword", 25, 0, 0, true),
            new CrateItem(310, "12:1:4", "T4 basic boots", 25, 0, 0, true),
            new CrateItem(320, "11:1:4", "T4 basic leggings", 25, 0, 0, true),
            new CrateItem(330, "10:1:4", "T4 basic chestplate", 25, 0, 0, true),
            new CrateItem(340, "9:1:4", "T4 basic helmet", 25, 0, 0, true),
            new CrateItem(350, "4:10", "steeldust", 500, 0, 0, true),
            new CrateItem(360, "4:16", "steeldust", 200, 0, 0, true),
            new CrateItem(370, "4:25", "steeldust", 50, 0, 0, true),
            new CrateItem(380, "2:5", "magicdust", 500, 0, 0, true),
            new CrateItem(390, "2:10", "magicdust", 200, 0, 0, true),
            new CrateItem(400, "2:15", "magicdust", 50, 0, 0, true),
            new CrateItem(410, "3:5", "obsidian shard", 500, 0, 0, true),
            new CrateItem(420, "3:10", "obsidian shard", 200, 0, 0, true),
            new CrateItem(430, "3:15", "obsidian shard", 50, 0, 0, true),
            new CrateItem(440, ItemFactory::getInstance()->get(ItemIds::NAMETAG), "random tag", 100, 0, 0, false, function(User $user) {
                $user->grantRandomTag();
            }, false),
            new CrateItem(450, "20:1:20", "Gold key", 50, 0, 0, true),
        ], 10000));

        $this->register(new Crate(20, "Gold", [
            new CrateItem(130, VanillaItems::PAPER(), "$2500 money drop", 500, 2500, 0, false),
            new CrateItem(140, VanillaItems::PAPER(), "$5000 money drop", 150, 5000, 0, false),
            new CrateItem(150, VanillaItems::PAPER(), "$7500 money drop", 50, 7500, 0, false),
            new CrateItem(160, "6:1:3", "T3 basic pickaxe", 250, 0, 0, true),
            new CrateItem(170, "13:1:3", "T3 basic sword", 250, 0, 0, true),
            new CrateItem(180, "12:1:3", "T3 basic boots", 250, 0, 0, true),
            new CrateItem(190, "11:1:3", "T3 basic leggings", 250, 0, 0, true),
            new CrateItem(200, "10:1:3", "T3 basic chestplate", 250, 0, 0, true),
            new CrateItem(210, "9:1:3", "T3 basic helmet", 250, 0, 0, true),
            new CrateItem(220, "6:1:5", "T5 basic pickaxe", 100, 0, 0, true),
            new CrateItem(230, "6:1:4", "T4 basic pickaxe", 150, 0, 0, true),
            new CrateItem(240, "13:1:4", "T4 basic sword", 100, 0, 0, true),
            new CrateItem(250, "12:1:4", "T4 basic boots", 100, 0, 0, true),
            new CrateItem(260, "11:1:4", "T4 basic leggings", 100, 0, 0, true),
            new CrateItem(270, "10:1:4", "T4 basic chestplate", 100, 0, 0, true),
            new CrateItem(280, "9:1:4", "T4 basic helmet", 100, 0, 0, true),
            new CrateItem(290, "6:1:6", "T6 basic pickaxe", 25, 0, 0, true),
            new CrateItem(300, "13:1:5", "T5 basic sword", 25, 0, 0, true),
            new CrateItem(310, "12:1:5", "T5 basic boots", 25, 0, 0, true),
            new CrateItem(320, "11:1:5", "T5 basic leggings", 25, 0, 0, true),
            new CrateItem(330, "10:1:5", "T5 basic chestplate", 25, 0, 0, true),
            new CrateItem(340, "9:1:5", "T5 basic helmet", 25, 0, 0, true),
            new CrateItem(380, "2:15", "magicdust", 500, 0, 0, true),
            new CrateItem(390, "2:30", "magicdust", 200, 0, 0, true),
            new CrateItem(400, "2:45", "magicdust", 50, 0, 0, true),
            new CrateItem(410, "3:15", "obsidian shard", 500, 0, 0, true),
            new CrateItem(420, "3:30", "obsidian shard", 200, 0, 0, true),
            new CrateItem(430, "3:45", "obsidian shard", 50, 0, 0, true),
            new CrateItem(440, ItemFactory::getInstance()->get(ItemIds::NAMETAG), "random tag", 175, 0, 0, false, function(User $user) {
                $user->grantRandomTag();
            }, false),
            new CrateItem(450, "20:1:30", "Diamond key", 50, 0, 0, true),
        ], 750));

        $this->register(new Crate(30, "Diamond", [
            new CrateItem(130, VanillaItems::PAPER(), "$7500 money drop", 500, 7500, 0, false),
            new CrateItem(140, VanillaItems::PAPER(), "$15000 money drop", 150, 15000, 0, false),
            new CrateItem(150, VanillaItems::PAPER(), "$30000 money drop", 50, 30000, 0, false),
            new CrateItem(160, "6:1:4", "T4 basic pickaxe", 250, 0, 0, true),
            new CrateItem(170, "13:1:4", "T4 basic sword", 250, 0, 0, true),
            new CrateItem(180, "12:1:4", "T4 basic boots", 250, 0, 0, true),
            new CrateItem(190, "11:1:4", "T4 basic leggings", 250, 0, 0, true),
            new CrateItem(200, "10:1:4", "T4 basic chestplate", 250, 0, 0, true),
            new CrateItem(210, "9:1:4", "T4 basic helmet", 250, 0, 0, true),
            new CrateItem(220, "6:1:6", "T6 basic pickaxe", 100, 0, 0, true),
            new CrateItem(230, "6:1:5", "T5 basic pickaxe", 150, 0, 0, true),
            new CrateItem(240, "13:1:5", "T5 basic sword", 100, 0, 0, true),
            new CrateItem(250, "12:1:5", "T5 basic boots", 100, 0, 0, true),
            new CrateItem(260, "11:1:5", "T5 basic leggings", 100, 0, 0, true),
            new CrateItem(270, "10:1:5", "T5 basic chestplate", 100, 0, 0, true),
            new CrateItem(280, "9:1:5", "T5 basic helmet", 100, 0, 0, true),
            new CrateItem(290, "6:1:7", "T7 basic pickaxe", 25, 0, 0, true),
            new CrateItem(300, "15:1:1", "T1 advanced sword", 25, 0, 0, true),
            new CrateItem(310, "16:1:1", "T1 advanced boots", 25, 0, 0, true),
            new CrateItem(320, "17:1:1", "T1 advanced leggings", 25, 0, 0, true),
            new CrateItem(330, "18:1:1", "T1 advanced chestplate", 25, 0, 0, true),
            new CrateItem(340, "19:1:1", "T1 advanced helmet", 25, 0, 0, true),
            new CrateItem(350, "1:10", "stardust", 500, 0, 0, true),
            new CrateItem(360, "1:16", "stardust", 200, 0, 0, true),
            new CrateItem(370, "1:24", "stardust", 50, 0, 0, true),
            new CrateItem(380, "2:45", "magicdust", 500, 0, 0, true),
            new CrateItem(390, "2:64", "magicdust", 200, 0, 0, true),
            new CrateItem(400, "2:80", "magicdust", 50, 0, 0, true),
            new CrateItem(440, ItemFactory::getInstance()->get(ItemIds::NAMETAG), "random tag", 250, 0, 0, false, function(User $user) {
                $user->grantRandomTag();
            }, false),
            new CrateItem(450, "20:1:40", "Emerald key", 50, 0, 0, true),
        ], 50));

        $this->register(new Crate(40, "Emerald", [
            new CrateItem(130, VanillaItems::PAPER(), "$75000 money drop", 500, 75000, 0, false),
            new CrateItem(140, VanillaItems::PAPER(), "$150000 money drop", 150, 150000, 0, false),
            new CrateItem(150, VanillaItems::PAPER(), "$250000 money drop", 50, 250000, 0, false),
            new CrateItem(160, "6:1:5", "T5 basic pickaxe", 250, 0, 0, true),
            new CrateItem(170, "13:1:5", "T5 basic sword", 250, 0, 0, true),
            new CrateItem(180, "12:1:5", "T5 basic boots", 250, 0, 0, true),
            new CrateItem(190, "11:1:5", "T5 basic leggings", 250, 0, 0, true),
            new CrateItem(200, "10:1:5", "T5 basic chestplate", 250, 0, 0, true),
            new CrateItem(210, "9:1:5", "T5 basic helmet", 250, 0, 0, true),
            new CrateItem(220, "6:1:7", "T7 basic pickaxe", 100, 0, 0, true),
            new CrateItem(230, "6:1:6", "T6 basic pickaxe", 150, 0, 0, true),
            new CrateItem(240, "15:1:1", "T1 advanced sword", 100, 0, 0, true),
            new CrateItem(250, "16:1:1", "T1 advanced boots", 100, 0, 0, true),
            new CrateItem(260, "17:1:1", "T1 advanced leggings", 100, 0, 0, true),
            new CrateItem(270, "18:1:1", "T1 advanced chestplate", 100, 0, 0, true),
            new CrateItem(280, "19:1:1", "T1 advanced helmet", 100, 0, 0, true),
            new CrateItem(290, "6:1:8", "T8 basic pickaxe", 25, 0, 0, true),
            new CrateItem(300, "15:1:2", "T2 advanced sword", 25, 0, 0, true),
            new CrateItem(310, "16:1:2", "T2 advanced boots", 25, 0, 0, true),
            new CrateItem(320, "17:1:2", "T2 advanced leggings", 25, 0, 0, true),
            new CrateItem(330, "18:1:2", "T2 advanced chestplate", 25, 0, 0, true),
            new CrateItem(340, "19:1:2", "T2 advanced helmet", 25, 0, 0, true),
            new CrateItem(350, "1:20", "stardust", 500, 0, 0, true),
            new CrateItem(360, "1:30", "stardust", 200, 0, 0, true),
            new CrateItem(370, "1:45", "stardust", 50, 0, 0, true),
            new CrateItem(380, "2:85", "magicdust", 500, 0, 0, true),
            new CrateItem(390, "2:100", "magicdust", 200, 0, 0, true),
            new CrateItem(400, "2:120", "magicdust", 50, 0, 0, true),
            new CrateItem(440, ItemFactory::getInstance()->get(ItemIds::NAMETAG), "random tag", 400, 0, 0, false, function(User $user) {
                $user->grantRandomTag();
            }, false),
            new CrateItem(450, "20:1:50", "Netherrite key", 50, 0, 0, true),
        ], 20));

        $this->register(new Crate(50, "Netherrite", [
            //new CrateItem(100, CustomItemManager::getInstance()->get(CustomItem::ADVANCEDPICKAXE)->getTier(8), "T8 advanced pickaxe", 100, 0, 0, true),
            //hey raptor
            new CrateItem(100, "7:1:8", "T8 advanced pickaxe", 100, 0, 0, true),
            new CrateItem(110, "15:1:4", "T4 advanced sword", 100, 0, 0, true),
            new CrateItem(120, "16:1:4", "T4 advanced boots", 100, 0, 0, true),
            new CrateItem(130, "17:1:4", "T4 advanced leggings", 100, 0, 0, true),
            new CrateItem(140, "18:1:4", "T4 advanced chestplate", 100, 0, 0, true),
            new CrateItem(150, "19:1:4", "T4 advanced helmet", 100, 0, 0, true),
            new CrateItem(160, "7:1:7", "T7 advanced pickaxe", 200, 0, 0, true),
            new CrateItem(180, "15:1:3", "T3 advanced sword", 200, 0, 0, true),
            new CrateItem(190, "16:1:3", "T3 advanced boots", 200, 0, 0, true),
            new CrateItem(200, "17:1:3", "T3 advanced leggings", 200, 0, 0, true),
            new CrateItem(210, "18:1:3", "T3 advanced chestplate", 200, 0, 0, true),
            new CrateItem(220, "19:1:3", "T3 advanced helmet", 200, 0, 0, true),
            new CrateItem(170, "7:1:6", "T6 advanced pickaxe", 250, 0, 0, true),
            new CrateItem(230, VanillaItems::PAPER(), "7500 prestige points", 500, 0, 7500, false),
            new CrateItem(240, VanillaItems::PAPER(), "$1M money drop", 300, 1000000, 0, false),
            new CrateItem(241, VanillaItems::PAPER(), "$750K money drop", 500, 750000, 0, false),
            new CrateItem(250, "1:128", "stardust", 300, 0, 0, true),
            new CrateItem(260, "2:192", "magicdust", 400, 0, 0, true),
            new CrateItem(270, ItemFactory::getInstance()->get(ItemIds::NAMETAG, 0, 10), "random tag", 300, 0, 0, false, function(User $user) {
                $user->grantRandomTag();
                $user->grantRandomTag();
                $user->grantRandomTag();
                $user->grantRandomTag();
                $user->grantRandomTag();
                $user->grantRandomTag();
                $user->grantRandomTag();
                $user->grantRandomTag();
                $user->grantRandomTag();
                $user->grantRandomTag();
            }, false),
            new CrateItem(280, VanillaItems::PAPER(), "10000 prestige points", 300, 0, 10000, false),
        ], 1));

        $this->register(new Crate(99, "Vote", [
            new CrateItem(10, ItemFactory::getInstance()->get(ItemIds::NAMETAG), "random tag", 500, 0, 0, false, function(User $user) {
                $user->grantRandomTag();
            }, false),
            new CrateItem(20, ItemFactory::getInstance()->get(ItemIds::NAMETAG, 0, 2), "random tag", 200, 0, 0, false, function(User $user) {
                $user->grantRandomTag();
                $user->grantRandomTag();
            }, false),
            new CrateItem(30, ItemFactory::getInstance()->get(ItemIds::NAMETAG, 0, 3), "random tag", 50, 0, 0, false, function(User $user) {
                $user->grantRandomTag();
                $user->grantRandomTag();
                $user->grantRandomTag();
            }, false),
            new CrateItem(40, VanillaItems::PAPER(), "200 prestige points", 500, 0, 200, false),
            new CrateItem(50, VanillaItems::PAPER(), "325 prestige points", 200, 0, 325, false),
            new CrateItem(60, VanillaItems::PAPER(), "500 prestige points", 50, 0, 500, false),
            new CrateItem(70, "20:1:50", "Netherrite key", 25, 0, 0, true),
            new CrateItem(80, "20:1:40", "Emerald key", 50, 0, 0, true),
        ], 0));

        $this->register(new Crate(120, "KOTH", [
            new CrateItem(10, ItemFactory::getInstance()->get(ItemIds::NAMETAG), "KOTH Tag", 350, 0, 0, false, function(User $user) {
                $user->grantTag(TagManager::getInstance()->get("koth"));
            }, false),
            new CrateItem(40, VanillaItems::BOOK(), "5 High End Enchant Forges", 300, 0, 0, false, function(User $user) {
                $bookClass = CustomItemManager::getInstance()->get(CustomItem::ENCHANTMENTBOOK);
                if(!$bookClass instanceof EnchantmentBook){
                    return;
                }
                for($i = 0; $i < 5; ++$i) {
                    $enchant = CustomEnchantsManager::getInstance()->getRandomEnchantment(true);
                    $item = $bookClass->getVariant($enchant->getId(), $enchant->getLevel());
                    $user->safeGive($item);
                }
            }),
            new CrateItem(50, VanillaItems::BOOK(), "6 High End Enchant Forges", 200, 0, 0, false, function(User $user) {
                $bookClass = CustomItemManager::getInstance()->get(CustomItem::ENCHANTMENTBOOK);
                if(!$bookClass instanceof EnchantmentBook){
                    return;
                }
                for($i = 0; $i < 6; ++$i) {
                    $enchant = CustomEnchantsManager::getInstance()->getRandomEnchantment(true);
                    $item = $bookClass->getVariant($enchant->getId(), $enchant->getLevel());
                    $user->safeGive($item);
                }
            }),
            new CrateItem(60, VanillaItems::BOOK(), "7 High End Enchant Forges", 150, 0, 0, false, function(User $user) {
                $bookClass = CustomItemManager::getInstance()->get(CustomItem::ENCHANTMENTBOOK);
                if(!$bookClass instanceof EnchantmentBook){
                    return;
                }
                for($i = 0; $i < 7; ++$i) {
                    $enchant = CustomEnchantsManager::getInstance()->getRandomEnchantment(true);
                    $item = $bookClass->getVariant($enchant->getId(), $enchant->getLevel());
                    $user->safeGive($item);
                }
            }),
            new CrateItem(70, "20:5:50", "Netherrite key", 250, 0, 0, true),
            new CrateItem(80, "20:5:40", "Emerald key", 300, 0, 0, true),
            new CrateItem(90, "2:320", "320x Magic Dust", 250, 0, 0, true),
            new CrateItem(100, "2:384", "384x Magic Dust", 200, 0, 0, true),
            new CrateItem(110, "2:416", "416x Magic Dust", 100, 0, 0, true),
            new CrateItem(120, "28:1", "KOTH pickaxe", 20, 0, 0, true),
            new CrateItem(130, "29:1", "KOTH Chestplate", 20, 0, 0, true),
            new CrateItem(140, VanillaItems::PAPER(), "$25M money drop", 250, 25000000, 0, false),
            new CrateItem(150, VanillaItems::PAPER(), "$10M money drop", 300, 10000000, 0, false),
        ], 0));

        $spawn = Server::getInstance()->getWorldManager()->getWorldByName("spawn");
        $this->crateLocations = [
            new Position(1571, 25, 623, $spawn),
            new Position(1572, 25, 620, $spawn),
            new Position(1574, 25, 618, $spawn),
            new Position(1577, 25, 617, $spawn),
            new Position(1580, 25, 618, $spawn),
            new Position(1582, 25, 620, $spawn),
            new Position(1583, 25, 623, $spawn),
        ];

        CustomItemManager::getInstance()->register(new CrateKey());
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}