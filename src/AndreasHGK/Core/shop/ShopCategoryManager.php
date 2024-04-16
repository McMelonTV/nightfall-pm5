<?php

namespace AndreasHGK\Core\shop;

use AndreasHGK\Core\user\User;
use AndreasHGK\RankSystem\rank\RankInstance;
use AndreasHGK\RankSystem\RankSystem;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;

class ShopCategoryManager {

    private static $instance;

    /**
     * @var array|ShopCategory[]
     */
    private $categories = [];

    /**
     * @return array|ShopCategory[]
     */
    public function getAll() : array {
        return $this->categories;
    }

    public function get(string $name) : ?ShopCategory {
        return $this->categories[$name] ?? null;
    }

    public function add(ShopCategory $shopCategory) : void {
        $this->categories[$shopCategory->getName()] = $shopCategory;
    }

    public function exist(string $category) : bool {
        return isset($this->categories[$category]);
    }

    public function registerDefaults() : void {
        $defaults = [];

        $rankManager = RankSystem::getInstance()->getRankManager();
        $category = new ShopCategory("Ranks", ItemIds::EMERALD, [
            "mercenary" => new ShopItem("mercenary", VanillaItems::EMERALD(), "Get a donator rank for a season", 0, 100000, false, "§eMercenary §r§frank", true, function (User $user) use ($rankManager) {$user->getRankComponent()->addRank(RankInstance::create($rankManager->get("mercenary"), -1, false));}),
            "warrior" => new ShopItem("warrior", VanillaItems::EMERALD(), "Get a donator rank for a season", 0, 200000, false, "§4Warrior §r§frank", true, function (User $user) use ($rankManager) {$user->getRankComponent()->addRank(RankInstance::create($rankManager->get("warrior"), -1, false));}),
            "knight" => new ShopItem("knight", VanillaItems::EMERALD(), "Get a donator rank for a season", 0, 300000, false, "§2Knight §r§frank", true, function (User $user) use ($rankManager) {$user->getRankComponent()->addRank(RankInstance::create($rankManager->get("knight"), -1, false));}),
            "lord" => new ShopItem("lord", VanillaItems::EMERALD(), "Get a donator rank for a season", 0, 400000, false, "§cLord §r§frank", true, function (User $user) use ($rankManager) {$user->getRankComponent()->addRank(RankInstance::create($rankManager->get("lord"), -1, false));}),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Upgrades", ItemIds::ANVIL, [
            "vault0" => new ShopItem("vault0", ItemFactory::getInstance()->get(ItemIds::CHEST_MINECART), "Get an extra vault to store items", 7500, 0, false, "Extra vault 1", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
            "vault1" => new ShopItem("vault1", ItemFactory::getInstance()->get(ItemIds::CHEST_MINECART), "Get an extra vault to store items", 75000, 0, false, "Extra vault 2", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
            "vault2" => new ShopItem("vault2", ItemFactory::getInstance()->get(ItemIds::CHEST_MINECART), "Get an extra vault to store items", 15000, 500, false, "Extra vault 3", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
            "vault3" => new ShopItem("vault3", ItemFactory::getInstance()->get(ItemIds::CHEST_MINECART), "Get an extra vault to store items", 15000, 2500, false, "Extra vault 4", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
            "plot0" => new ShopItem("plot0", VanillaItems::IRON_SHOVEL(), "Get an extra plot to build on", 7500, 0, false, "Extra plot 1", true, function (User $user){$user->setMaxPlots($user->getMaxPlots()+1);}),
            "plot1" => new ShopItem("plot1", VanillaItems::IRON_SHOVEL(), "Get an extra plot to build on", 75000, 0, false, "Extra plot 2", true, function (User $user){$user->setMaxPlots($user->getMaxPlots()+1);}),
            "plot2" => new ShopItem("plot2", VanillaItems::IRON_SHOVEL(), "Get an extra plot to build on", 15000, 500, false, "Extra plot 3", true, function (User $user){$user->setMaxPlots($user->getMaxPlots()+1);}),
            "plot3" => new ShopItem("plot3", VanillaItems::IRON_SHOVEL(), "Get an extra plot to build on", 15000, 2500, false, "Extra plot 4", true, function (User $user){$user->setMaxPlots($user->getMaxPlots()+1);}),
            "auction0" => new ShopItem("auction0", VanillaItems::GOLD_INGOT(), "Get an extra auction slot to sell items", 7500, 0, false, "Extra auction slot 1", true, function (User $user){$user->setMaxAuc($user->getMaxAuc()+1);}),
            "auction1" => new ShopItem("auction1", VanillaItems::GOLD_INGOT(), "Get an extra auction slot to sell items", 75000, 0, false, "Extra auction slot 2", true, function (User $user){$user->setMaxAuc($user->getMaxAuc()+1);}),
            "auction2" => new ShopItem("auction2", VanillaItems::GOLD_INGOT(), "Get an extra auction slot to sell items", 15000, 500, false, "Extra auction slot  3", true, function (User $user){$user->setMaxAuc($user->getMaxAuc()+1);}),
            "auction3" => new ShopItem("auction3", VanillaItems::GOLD_INGOT(), "Get an extra auction slot to sell items", 15000, 2500, false, "Extra auction slot  4", true, function (User $user){$user->setMaxAuc($user->getMaxAuc()+1);}),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("PvP Items", ItemIds::GOLDEN_APPLE, [
            "10" => new ShopItem("10", ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE), "", 1500, 0),
            "20" => new ShopItem("20", ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 8), "", 12000, 0),
            "30" => new ShopItem("30", ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 32), "", 48000, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Wool", ItemIds::WOOL, [
            "4" => new ShopItem("4", ItemFactory::getInstance()->get(ItemIds::WOOL), "", 50, 0),
            "5" => new ShopItem("5", ItemFactory::getInstance()->get(ItemIds::WOOL, 0, 32), "", 1600, 0),
            "6" => new ShopItem("6", ItemFactory::getInstance()->get(ItemIds::WOOL, 1, 1), "", 50, 0),
            "7" => new ShopItem("7", ItemFactory::getInstance()->get(ItemIds::WOOL, 1, 32), "", 1600, 0),
            "8" => new ShopItem("8", ItemFactory::getInstance()->get(ItemIds::WOOL, 2, 1), "", 50, 0),
            "9" => new ShopItem("9", ItemFactory::getInstance()->get(ItemIds::WOOL, 2, 32), "", 1600, 0),
            "10" => new ShopItem("10", ItemFactory::getInstance()->get(ItemIds::WOOL, 3, 1), "", 50, 0),
            "11" => new ShopItem("11", ItemFactory::getInstance()->get(ItemIds::WOOL, 3, 32), "", 1600, 0),
            "12" => new ShopItem("12", ItemFactory::getInstance()->get(ItemIds::WOOL, 4, 1), "", 50, 0),
            "13" => new ShopItem("13", ItemFactory::getInstance()->get(ItemIds::WOOL, 4, 32), "", 1600, 0),
            "14" => new ShopItem("14", ItemFactory::getInstance()->get(ItemIds::WOOL, 5, 1), "", 50, 0),
            "15" => new ShopItem("15", ItemFactory::getInstance()->get(ItemIds::WOOL, 5, 32), "", 1600, 0),
            "16" => new ShopItem("16", ItemFactory::getInstance()->get(ItemIds::WOOL, 6, 1), "", 50, 0),
            "17" => new ShopItem("17", ItemFactory::getInstance()->get(ItemIds::WOOL, 6, 32), "", 1600, 0),
            "18" => new ShopItem("18", ItemFactory::getInstance()->get(ItemIds::WOOL, 7, 1), "", 50, 0),
            "19" => new ShopItem("19", ItemFactory::getInstance()->get(ItemIds::WOOL, 7, 32), "", 1600, 0),
            "20" => new ShopItem("20", ItemFactory::getInstance()->get(ItemIds::WOOL, 8, 1), "", 50, 0),
            "21" => new ShopItem("21", ItemFactory::getInstance()->get(ItemIds::WOOL, 8, 32), "", 1600, 0),
            "22" => new ShopItem("22", ItemFactory::getInstance()->get(ItemIds::WOOL, 9, 1), "", 50, 0),
            "23" => new ShopItem("23", ItemFactory::getInstance()->get(ItemIds::WOOL, 9, 32), "", 1600, 0),
            "24" => new ShopItem("24", ItemFactory::getInstance()->get(ItemIds::WOOL, 10, 1), "", 50, 0),
            "25" => new ShopItem("25", ItemFactory::getInstance()->get(ItemIds::WOOL, 10, 32), "", 1600, 0),
            "26" => new ShopItem("26", ItemFactory::getInstance()->get(ItemIds::WOOL, 11, 1), "", 50, 0),
            "27" => new ShopItem("27", ItemFactory::getInstance()->get(ItemIds::WOOL, 11, 32), "", 1600, 0),
            "28" => new ShopItem("28", ItemFactory::getInstance()->get(ItemIds::WOOL, 12, 1), "", 50, 0),
            "29" => new ShopItem("29", ItemFactory::getInstance()->get(ItemIds::WOOL, 12, 32), "", 1600, 0),
            "30" => new ShopItem("30", ItemFactory::getInstance()->get(ItemIds::WOOL, 13, 1), "", 50, 0),
            "31" => new ShopItem("31", ItemFactory::getInstance()->get(ItemIds::WOOL, 13, 32), "", 1600, 0),
            "32" => new ShopItem("32", ItemFactory::getInstance()->get(ItemIds::WOOL, 14, 1), "", 50, 0),
            "33" => new ShopItem("33", ItemFactory::getInstance()->get(ItemIds::WOOL, 14, 32), "", 1600, 0),
            "34" => new ShopItem("34", ItemFactory::getInstance()->get(ItemIds::WOOL, 15, 1), "", 50, 0),
            "35" => new ShopItem("35", ItemFactory::getInstance()->get(ItemIds::WOOL, 15, 32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Glass", ItemIds::GLASS, [
            "4" => new ShopItem("4", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS), "", 50, 0),
            "5" => new ShopItem("5", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 0, 32), "", 1600, 0),
            "6" => new ShopItem("6", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 1, 1), "", 50, 0),
            "7" => new ShopItem("7", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 1, 32), "", 1600, 0),
            "8" => new ShopItem("8", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 2, 1), "", 50, 0),
            "9" => new ShopItem("9", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 2, 32), "", 1600, 0),
            "10" => new ShopItem("10", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 3, 1), "", 50, 0),
            "11" => new ShopItem("11", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 3, 32), "", 1600, 0),
            "12" => new ShopItem("12", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 4, 1), "", 50, 0),
            "13" => new ShopItem("13", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 4, 32), "", 1600, 0),
            "14" => new ShopItem("14", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 5, 1), "", 50, 0),
            "15" => new ShopItem("15", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 5, 32), "", 1600, 0),
            "16" => new ShopItem("16", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 6, 1), "", 50, 0),
            "17" => new ShopItem("17", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 6, 32), "", 1600, 0),
            "18" => new ShopItem("18", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 7, 1), "", 50, 0),
            "19" => new ShopItem("19", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 7, 32), "", 1600, 0),
            "20" => new ShopItem("20", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 8, 1), "", 50, 0),
            "21" => new ShopItem("21", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 8, 32), "", 1600, 0),
            "22" => new ShopItem("22", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 9, 1), "", 50, 0),
            "23" => new ShopItem("23", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 9, 32), "", 1600, 0),
            "24" => new ShopItem("24", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 10, 1), "", 50, 0),
            "25" => new ShopItem("25", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 10, 32), "", 1600, 0),
            "26" => new ShopItem("26", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 11, 1), "", 50, 0),
            "27" => new ShopItem("27", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 11, 32), "", 1600, 0),
            "28" => new ShopItem("28", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 12, 1), "", 50, 0),
            "29" => new ShopItem("29", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 12, 32), "", 1600, 0),
            "30" => new ShopItem("30", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 13, 1), "", 50, 0),
            "31" => new ShopItem("31", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 13, 32), "", 1600, 0),
            "32" => new ShopItem("32", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14, 1), "", 50, 0),
            "33" => new ShopItem("33", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14, 32), "", 1600, 0),
            "34" => new ShopItem("34", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 15, 1), "", 50, 0),
            "35" => new ShopItem("35", ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 15, 32), "", 1600, 0),
            "36" => new ShopItem("36", ItemFactory::getInstance()->get(ItemIds::GLASS, 0, 1), "", 50, 0),
            "37" => new ShopItem("37", ItemFactory::getInstance()->get(ItemIds::GLASS, 0, 32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Terracotta", ItemIds::TERRACOTTA, [
            "4" => new ShopItem("4", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA), "", 50, 0),
            "5" => new ShopItem("5", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 0, 32), "", 1600, 0),
            "6" => new ShopItem("6", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 1, 1), "", 50, 0),
            "7" => new ShopItem("7", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 1, 32), "", 1600, 0),
            "8" => new ShopItem("8", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 2, 1), "", 50, 0),
            "9" => new ShopItem("9", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 2, 32), "", 1600, 0),
            "10" => new ShopItem("10", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 3, 1), "", 50, 0),
            "11" => new ShopItem("11", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 3, 32), "", 1600, 0),
            "12" => new ShopItem("12", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 4, 1), "", 50, 0),
            "13" => new ShopItem("13", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 4, 32), "", 1600, 0),
            "14" => new ShopItem("14", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 5, 1), "", 50, 0),
            "15" => new ShopItem("15", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 5, 32), "", 1600, 0),
            "16" => new ShopItem("16", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 6, 1), "", 50, 0),
            "17" => new ShopItem("17", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 6, 32), "", 1600, 0),
            "18" => new ShopItem("18", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 7, 1), "", 50, 0),
            "19" => new ShopItem("19", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 7, 32), "", 1600, 0),
            "20" => new ShopItem("20", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 8, 1), "", 50, 0),
            "21" => new ShopItem("21", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 8, 32), "", 1600, 0),
            "22" => new ShopItem("22", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 9, 1), "", 50, 0),
            "23" => new ShopItem("23", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 9, 32), "", 1600, 0),
            "24" => new ShopItem("24", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 10, 1), "", 50, 0),
            "25" => new ShopItem("25", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 10, 32), "", 1600, 0),
            "26" => new ShopItem("26", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 11, 1), "", 50, 0),
            "27" => new ShopItem("27", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 11, 32), "", 1600, 0),
            "28" => new ShopItem("28", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 12, 1), "", 50, 0),
            "29" => new ShopItem("29", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 12, 32), "", 1600, 0),
            "30" => new ShopItem("30", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 13, 1), "", 50, 0),
            "31" => new ShopItem("31", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 13, 32), "", 1600, 0),
            "32" => new ShopItem("32", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 14, 1), "", 50, 0),
            "33" => new ShopItem("33", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 14, 32), "", 1600, 0),
            "34" => new ShopItem("34", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 15, 1), "", 50, 0),
            "35" => new ShopItem("35", ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 15, 32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Concrete", ItemIds::CONCRETE, [
            "4" => new ShopItem("4", ItemFactory::getInstance()->get(ItemIds::CONCRETE), "", 50, 0),
            "5" => new ShopItem("5", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 0, 32), "", 1600, 0),
            "6" => new ShopItem("6", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 1, 1), "", 50, 0),
            "7" => new ShopItem("7", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 1, 32), "", 1600, 0),
            "8" => new ShopItem("8", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 2, 1), "", 50, 0),
            "9" => new ShopItem("9", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 2, 32), "", 1600, 0),
            "10" => new ShopItem("10", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 3, 1), "", 50, 0),
            "11" => new ShopItem("11", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 3, 32), "", 1600, 0),
            "12" => new ShopItem("12", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 4, 1), "", 50, 0),
            "13" => new ShopItem("13", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 4, 32), "", 1600, 0),
            "14" => new ShopItem("14", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 5, 1), "", 50, 0),
            "15" => new ShopItem("15", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 5, 32), "", 1600, 0),
            "16" => new ShopItem("16", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 6, 1), "", 50, 0),
            "17" => new ShopItem("17", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 6, 32), "", 1600, 0),
            "18" => new ShopItem("18", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 7, 1), "", 50, 0),
            "19" => new ShopItem("19", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 7, 32), "", 1600, 0),
            "20" => new ShopItem("20", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 8, 1), "", 50, 0),
            "21" => new ShopItem("21", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 8, 32), "", 1600, 0),
            "22" => new ShopItem("22", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 9, 1), "", 50, 0),
            "23" => new ShopItem("23", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 9, 32), "", 1600, 0),
            "24" => new ShopItem("24", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 10, 1), "", 50, 0),
            "25" => new ShopItem("25", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 10, 32), "", 1600, 0),
            "26" => new ShopItem("26", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 11, 1), "", 50, 0),
            "27" => new ShopItem("27", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 11, 32), "", 1600, 0),
            "28" => new ShopItem("28", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 12, 1), "", 50, 0),
            "29" => new ShopItem("29", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 12, 32), "", 1600, 0),
            "30" => new ShopItem("30", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 13, 1), "", 50, 0),
            "31" => new ShopItem("31", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 13, 32), "", 1600, 0),
            "32" => new ShopItem("32", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 14, 1), "", 50, 0),
            "33" => new ShopItem("33", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 14, 32), "", 1600, 0),
            "34" => new ShopItem("34", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 15, 1), "", 50, 0),
            "35" => new ShopItem("35", ItemFactory::getInstance()->get(ItemIds::CONCRETE, 15, 32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Concrete Powder", ItemIds::CONCRETEPOWDER, [
            "4" => new ShopItem("4", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER), "", 50, 0),
            "5" => new ShopItem("5", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 0, 32), "", 1600, 0),
            "6" => new ShopItem("6", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 1, 1), "", 50, 0),
            "7" => new ShopItem("7", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 1, 32), "", 1600, 0),
            "8" => new ShopItem("8", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 2, 1), "", 50, 0),
            "9" => new ShopItem("9", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 2, 32), "", 1600, 0),
            "10" => new ShopItem("10", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 3, 1), "", 50, 0),
            "11" => new ShopItem("11", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 3, 32), "", 1600, 0),
            "12" => new ShopItem("12", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 4, 1), "", 50, 0),
            "13" => new ShopItem("13", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 4, 32), "", 1600, 0),
            "14" => new ShopItem("14", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 5, 1), "", 50, 0),
            "15" => new ShopItem("15", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 5, 32), "", 1600, 0),
            "16" => new ShopItem("16", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 6, 1), "", 50, 0),
            "17" => new ShopItem("17", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 6, 32), "", 1600, 0),
            "18" => new ShopItem("18", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 7, 1), "", 50, 0),
            "19" => new ShopItem("19", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 7, 32), "", 1600, 0),
            "20" => new ShopItem("20", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 8, 1), "", 50, 0),
            "21" => new ShopItem("21", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 8, 32), "", 1600, 0),
            "22" => new ShopItem("22", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 9, 1), "", 50, 0),
            "23" => new ShopItem("23", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 9, 32), "", 1600, 0),
            "24" => new ShopItem("24", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 10, 1), "", 50, 0),
            "25" => new ShopItem("25", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 10, 32), "", 1600, 0),
            "26" => new ShopItem("26", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 11, 1), "", 50, 0),
            "27" => new ShopItem("27", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 11, 32), "", 1600, 0),
            "28" => new ShopItem("28", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 12, 1), "", 50, 0),
            "29" => new ShopItem("29", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 12, 32), "", 1600, 0),
            "30" => new ShopItem("30", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 13, 1), "", 50, 0),
            "31" => new ShopItem("31", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 13, 32), "", 1600, 0),
            "32" => new ShopItem("32", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 14, 1), "", 50, 0),
            "33" => new ShopItem("33", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 14, 32), "", 1600, 0),
            "34" => new ShopItem("34", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 15, 1), "", 50, 0),
            "35" => new ShopItem("35", ItemFactory::getInstance()->get(ItemIds::CONCRETEPOWDER, 15, 32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Stone Blocks", ItemIds::STONE, [
            "0" => new ShopItem("0", ItemFactory::getInstance()->get(ItemIds::STONE), "", 20, 0),
            "1" => new ShopItem("1", ItemFactory::getInstance()->get(ItemIds::STONE, 0, 32), "", 640, 0),
            "2" => new ShopItem("2", ItemFactory::getInstance()->get(ItemIds::NORMAL_STONE_STAIRS), "", 20, 0),
            "3" => new ShopItem("3", ItemFactory::getInstance()->get(ItemIds::NORMAL_STONE_STAIRS, 0, 32), "", 640, 0),
            "4" => new ShopItem("4", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB), "", 20, 0),
            "5" => new ShopItem("5", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB, 0, 32), "", 640, 0),
            "6" => new ShopItem("6", ItemFactory::getInstance()->get(ItemIds::COBBLESTONE), "", 20, 0),
            "7" => new ShopItem("7", ItemFactory::getInstance()->get(ItemIds::COBBLESTONE, 0, 32), "", 640, 0),
            "8" => new ShopItem("8", ItemFactory::getInstance()->get(ItemIds::COBBLESTONE_STAIRS), "", 20, 0),
            "9" => new ShopItem("9", ItemFactory::getInstance()->get(ItemIds::COBBLESTONE_STAIRS, 0, 32), "", 640, 0),
            "10" => new ShopItem("10", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB, 3), "", 20, 0),
            "11" => new ShopItem("11", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB, 3, 32), "", 640, 0),
            "12" => new ShopItem("12", ItemFactory::getInstance()->get(ItemIds::STONEBRICK), "", 40, 0),
            "13" => new ShopItem("13", ItemFactory::getInstance()->get(ItemIds::STONEBRICK, 0, 32), "", 1280, 0),
            "14" => new ShopItem("14", ItemFactory::getInstance()->get(ItemIds::STONEBRICK, 1), "", 40, 0),
            "15" => new ShopItem("15", ItemFactory::getInstance()->get(ItemIds::STONEBRICK, 1, 32), "", 1280, 0),
            "16" => new ShopItem("16", ItemFactory::getInstance()->get(ItemIds::STONEBRICK, 2), "", 40, 0),
            "17" => new ShopItem("17", ItemFactory::getInstance()->get(ItemIds::STONEBRICK, 2,32), "", 1280, 0),
            "18" => new ShopItem("18", ItemFactory::getInstance()->get(ItemIds::STONEBRICK, 3), "", 40, 0),
            "19" => new ShopItem("19", ItemFactory::getInstance()->get(ItemIds::STONEBRICK, 3, 32), "", 1280, 0),
            "20" => new ShopItem("20", ItemFactory::getInstance()->get(ItemIds::STONE_BRICK_STAIRS), "", 40, 0),
            "21" => new ShopItem("21", ItemFactory::getInstance()->get(ItemIds::STONE_BRICK_STAIRS, 0, 32), "", 1280, 0),
            "22" => new ShopItem("22", ItemFactory::getInstance()->get(ItemIds::MOSSY_STONE_BRICK_STAIRS), "", 40, 0),
            "23" => new ShopItem("23", ItemFactory::getInstance()->get(ItemIds::MOSSY_STONE_BRICK_STAIRS, 0, 32), "", 1280, 0),
            "24" => new ShopItem("24", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB, 5,1), "", 40, 0),
            "25" => new ShopItem("25", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB, 5,32), "", 1280, 0),
            "26" => new ShopItem("26", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB4, 0), "", 40, 0),
            "27" => new ShopItem("27", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB4, 0, 32), "", 1280, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Wood", ItemIds::WOODEN_PLANKS, [
            "0" => new ShopItem("0", ItemFactory::getInstance()->get(ItemIds::LOG), "", 80, 0),
            "1" => new ShopItem("1", ItemFactory::getInstance()->get(ItemIds::LOG, 0, 32), "", 2580, 0),
            "2" => new ShopItem("2", ItemFactory::getInstance()->get(ItemIds::LOG, 1), "", 80, 0),
            "3" => new ShopItem("3", ItemFactory::getInstance()->get(ItemIds::LOG, 1, 32), "", 2580, 0),
            "4" => new ShopItem("4", ItemFactory::getInstance()->get(ItemIds::LOG, 2), "", 80, 0),
            "5" => new ShopItem("5", ItemFactory::getInstance()->get(ItemIds::LOG, 2, 32), "", 2580, 0),
            "6" => new ShopItem("6", ItemFactory::getInstance()->get(ItemIds::LOG, 3), "", 80, 0),
            "7" => new ShopItem("7", ItemFactory::getInstance()->get(ItemIds::LOG, 3, 32), "", 2580, 0),
            "8" => new ShopItem("8", ItemFactory::getInstance()->get(ItemIds::LOG2), "", 80, 0),
            "9" => new ShopItem("9", ItemFactory::getInstance()->get(ItemIds::LOG2, 0, 32), "", 2580, 0),
            "10" => new ShopItem("10", ItemFactory::getInstance()->get(ItemIds::LOG2, 1), "", 80, 0),
            "11" => new ShopItem("11", ItemFactory::getInstance()->get(ItemIds::LOG2, 1, 32), "", 2580, 0),
            "12" => new ShopItem("12", ItemFactory::getInstance()->get(ItemIds::STRIPPED_OAK_LOG), "", 80, 0),
            "13" => new ShopItem("13", ItemFactory::getInstance()->get(ItemIds::STRIPPED_OAK_LOG, 0, 32), "", 2580, 0),
            "14" => new ShopItem("14", ItemFactory::getInstance()->get(ItemIds::STRIPPED_SPRUCE_LOG, 0), "", 80, 0),
            "15" => new ShopItem("15", ItemFactory::getInstance()->get(ItemIds::STRIPPED_SPRUCE_LOG, 0, 32), "", 2580, 0),
            "16" => new ShopItem("16", ItemFactory::getInstance()->get(ItemIds::STRIPPED_BIRCH_LOG, 0), "", 80, 0),
            "17" => new ShopItem("17", ItemFactory::getInstance()->get(ItemIds::STRIPPED_BIRCH_LOG, 0, 32), "", 2580, 0),
            "18" => new ShopItem("18", ItemFactory::getInstance()->get(ItemIds::STRIPPED_JUNGLE_LOG, 0), "", 80, 0),
            "19" => new ShopItem("19", ItemFactory::getInstance()->get(ItemIds::STRIPPED_JUNGLE_LOG, 0, 32), "", 2580, 0),
            "20" => new ShopItem("20", ItemFactory::getInstance()->get(ItemIds::STRIPPED_ACACIA_LOG), "", 80, 0),
            "21" => new ShopItem("21", ItemFactory::getInstance()->get(ItemIds::STRIPPED_ACACIA_LOG, 0, 32), "", 2580, 0),
            "22" => new ShopItem("22", ItemFactory::getInstance()->get(ItemIds::STRIPPED_DARK_OAK_LOG, 0), "", 80, 0),
            "23" => new ShopItem("23", ItemFactory::getInstance()->get(ItemIds::STRIPPED_DARK_OAK_LOG, 0, 32), "", 2580, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Nature", ItemIds::LEAVES, [
            "0" => new ShopItem("0", ItemFactory::getInstance()->get(ItemIds::DIRT), "", 2, 0),
            "1" => new ShopItem("1", ItemFactory::getInstance()->get(ItemIds::DIRT, 0, 32), "", 64, 0),
            "2" => new ShopItem("2", ItemFactory::getInstance()->get(ItemIds::GRASS), "", 2, 0),
            "3" => new ShopItem("3", ItemFactory::getInstance()->get(ItemIds::GRASS, 0, 32), "", 64, 0),
            "4" => new ShopItem("4", ItemFactory::getInstance()->get(ItemIds::LEAVES, 0), "", 4, 0),
            "5" => new ShopItem("5", ItemFactory::getInstance()->get(ItemIds::LEAVES, 0, 32), "", 128, 0),
            "6" => new ShopItem("6", ItemFactory::getInstance()->get(ItemIds::LEAVES, 1), "", 4, 0),
            "7" => new ShopItem("7", ItemFactory::getInstance()->get(ItemIds::LEAVES, 1, 32), "", 128, 0),
            "8" => new ShopItem("8", ItemFactory::getInstance()->get(ItemIds::LEAVES, 2), "", 4, 0),
            "9" => new ShopItem("9", ItemFactory::getInstance()->get(ItemIds::LEAVES, 2, 32), "", 128, 0),
            "10" => new ShopItem("10", ItemFactory::getInstance()->get(ItemIds::LEAVES, 3), "", 4, 0),
            "11" => new ShopItem("11", ItemFactory::getInstance()->get(ItemIds::LEAVES, 3, 32), "", 128, 0),
            "12" => new ShopItem("12", ItemFactory::getInstance()->get(ItemIds::LEAVES2, 0), "", 4, 0),
            "13" => new ShopItem("13", ItemFactory::getInstance()->get(ItemIds::LEAVES2, 0, 32), "", 128, 0),
            "14" => new ShopItem("14", ItemFactory::getInstance()->get(ItemIds::LEAVES2, 1), "", 4, 0),
            "15" => new ShopItem("15", ItemFactory::getInstance()->get(ItemIds::LEAVES2, 1, 32), "", 128, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Nether", ItemIds::QUARTZ, [
            "0" => new ShopItem("0", ItemFactory::getInstance()->get(ItemIds::QUARTZ_BLOCK, 0, 1), "", 50, 0),
            "1" => new ShopItem("1", ItemFactory::getInstance()->get(ItemIds::QUARTZ_BLOCK, 0, 32), "", 1600, 0),
            "2" => new ShopItem("2", ItemFactory::getInstance()->get(ItemIds::QUARTZ_STAIRS, 0, 1), "", 50, 0),
            "3" => new ShopItem("3", ItemFactory::getInstance()->get(ItemIds::QUARTZ_STAIRS, 0, 32), "", 1600, 0),
            "4" => new ShopItem("4", ItemFactory::getInstance()->get(ItemIds::SMOOTH_QUARTZ_STAIRS, 0, 1), "", 50, 0),
            "5" => new ShopItem("5", ItemFactory::getInstance()->get(ItemIds::SMOOTH_QUARTZ_STAIRS, 0, 32), "", 1600, 0),
            "6" => new ShopItem("6", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB, 6, 1), "", 50, 0),
            "7" => new ShopItem("7", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB, 6, 32), "", 1600, 0),
            "8" => new ShopItem("8", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB4, 1, 1), "", 50, 0),
            "9" => new ShopItem("9", ItemFactory::getInstance()->get(ItemIds::STONE_SLAB4, 1, 32), "", 1600, 0),
            "10" => new ShopItem("10", ItemFactory::getInstance()->get(ItemIds::QUARTZ_ORE, 10, 1), "", 50, 0),
            "11" => new ShopItem("11", ItemFactory::getInstance()->get(ItemIds::QUARTZ_ORE, 0, 32), "", 1600, 0),
            "12" => new ShopItem("12", ItemFactory::getInstance()->get(ItemIds::NETHER_BRICK_BLOCK, 0, 1), "", 50, 0),
            "13" => new ShopItem("13", ItemFactory::getInstance()->get(ItemIds::NETHER_BRICK_BLOCK, 0, 32), "", 1600, 0),
            "14" => new ShopItem("14", ItemFactory::getInstance()->get(ItemIds::NETHER_BRICK_FENCE, 0, 1), "", 50, 0),
            "15" => new ShopItem("15", ItemFactory::getInstance()->get(ItemIds::NETHER_BRICK_FENCE, 0, 32), "", 1600, 0),
            "16" => new ShopItem("16", ItemFactory::getInstance()->get(ItemIds::NETHERRACK, 1, 1), "", 50, 0),
            "17" => new ShopItem("17", ItemFactory::getInstance()->get(ItemIds::NETHERRACK, 1, 32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        foreach ($defaults as $default){
            $this->add($default);
        }
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}