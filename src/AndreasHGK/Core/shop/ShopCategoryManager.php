<?php

namespace AndreasHGK\Core\shop;

use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\user\User;
use AndreasHGK\RankSystem\rank\RankInstance;
use AndreasHGK\RankSystem\RankSystem;
use PresentKim\ItemSerialize\ItemSerializeUtils;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
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
        $category = new ShopCategory("Ranks", ItemSerializeUtils::jsonSerialize(VanillaItems::EMERALD()), [
            "mercenary" => new ShopItem("mercenary", VanillaItems::EMERALD(), "Get a donator rank for a season", 0, 100000, false, "§eMercenary §r§frank", true, function (User $user) use ($rankManager) {$user->getRankComponent()->addRank(RankInstance::create($rankManager->get("mercenary"), -1, false));}),
            "warrior" => new ShopItem("warrior", VanillaItems::EMERALD(), "Get a donator rank for a season", 0, 200000, false, "§4Warrior §r§frank", true, function (User $user) use ($rankManager) {$user->getRankComponent()->addRank(RankInstance::create($rankManager->get("warrior"), -1, false));}),
            "knight" => new ShopItem("knight", VanillaItems::EMERALD(), "Get a donator rank for a season", 0, 300000, false, "§2Knight §r§frank", true, function (User $user) use ($rankManager) {$user->getRankComponent()->addRank(RankInstance::create($rankManager->get("knight"), -1, false));}),
            "lord" => new ShopItem("lord", VanillaItems::EMERALD(), "Get a donator rank for a season", 0, 400000, false, "§cLord §r§frank", true, function (User $user) use ($rankManager) {$user->getRankComponent()->addRank(RankInstance::create($rankManager->get("lord"), -1, false));}),
        ]);
        $defaults[] = $category;

		$customitems = [];
		$allci = CustomItemManager::getInstance()->getAll();
		foreach($allci as $ci){
			$customitems[$ci->getId()] = new ShopItem($ci->getId(), $ci->getItem()->setCount($ci->getItem()->getMaxStackSize()), "", 1, 0);
		}

		$xp_bottle = new ShopItem("xp_bottle", VanillaItems::EXPERIENCE_BOTTLE()->setCount(64), "", 1, 0);
		$customitems["xp_bottle"] = $xp_bottle;

		$category = new ShopCategory("temporary stuff to mess around with", ItemSerializeUtils::jsonSerialize(VanillaItems::DIAMOND()), $customitems);
		$defaults[] = $category;

        $category = new ShopCategory("Upgrades", ItemSerializeUtils::jsonSerialize(VanillaBlocks::ANVIL()->asItem()), [
			"vault0" => new ShopItem("vault0", VanillaBlocks::CHEST()->asItem(), "Get an extra vault to store items", 7500, 0, false, "Extra vault 1", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
			"vault1" => new ShopItem("vault1", VanillaBlocks::CHEST()->asItem(), "Get an extra vault to store items", 75000, 0, false, "Extra vault 2", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
			"vault2" => new ShopItem("vault2", VanillaBlocks::CHEST()->asItem(), "Get an extra vault to store items", 15000, 500, false, "Extra vault 3", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
			"vault3" => new ShopItem("vault3", VanillaBlocks::CHEST()->asItem(), "Get an extra vault to store items", 15000, 2500, false, "Extra vault 4", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
			// "vault0" => new ShopItem("vault0", ItemFactory::getInstance()->get(ItemTypeIds::CHEST_MINECART), "Get an extra vault to store items", 7500, 0, false, "Extra vault 1", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
            // "vault1" => new ShopItem("vault1", ItemFactory::getInstance()->get(ItemTypeIds::CHEST_MINECART), "Get an extra vault to store items", 75000, 0, false, "Extra vault 2", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
            // "vault2" => new ShopItem("vault2", ItemFactory::getInstance()->get(ItemTypeIds::CHEST_MINECART), "Get an extra vault to store items", 15000, 500, false, "Extra vault 3", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
            // "vault3" => new ShopItem("vault3", ItemFactory::getInstance()->get(ItemTypeIds::CHEST_MINECART), "Get an extra vault to store items", 15000, 2500, false, "Extra vault 4", true, function (User $user){$user->getVault()->setMaxPages($user->getVault()->getMaxPages()+1);}),
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

        $category = new ShopCategory("PvP Items", ItemSerializeUtils::jsonSerialize(VanillaItems::GOLDEN_APPLE()), [
            "10" => new ShopItem("10", VanillaItems::GOLDEN_APPLE(), "", 1500, 0),
            "20" => new ShopItem("20", VanillaItems::GOLDEN_APPLE()->setCount(8), "", 12000, 0),
            "30" => new ShopItem("30", VanillaItems::GOLDEN_APPLE()->setCount(32), "", 48000, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Wool", ItemSerializeUtils::jsonSerialize(VanillaBlocks::WOOL()->asItem()), [
            "4" => new ShopItem("4", VanillaBlocks::WOOL()->setColor(DyeColor::WHITE)->asItem(), "", 50, 0),
            "5" => new ShopItem("5", VanillaBlocks::WOOL()->setColor(DyeColor::WHITE)->asItem()->setCount(32), "", 1600, 0),
            "6" => new ShopItem("6", VanillaBlocks::WOOL()->setColor(DyeColor::ORANGE)->asItem(), "", 50, 0),
            "7" => new ShopItem("7", VanillaBlocks::WOOL()->setColor(DyeColor::ORANGE)->asItem()->setCount(32), "", 1600, 0),
            "8" => new ShopItem("8", VanillaBlocks::WOOL()->setColor(DyeColor::MAGENTA)->asItem(), "", 50, 0),
            "9" => new ShopItem("9", VanillaBlocks::WOOL()->setColor(DyeColor::MAGENTA)->asItem()->setCount(32), "", 1600, 0),
            "10" => new ShopItem("10", VanillaBlocks::WOOL()->setColor(DyeColor::LIGHT_BLUE)->asItem(), "", 50, 0),
            "11" => new ShopItem("11", VanillaBlocks::WOOL()->setColor(DyeColor::LIGHT_BLUE)->asItem()->setCount(32), "", 1600, 0),
            "12" => new ShopItem("12", VanillaBlocks::WOOL()->setColor(DyeColor::YELLOW)->asItem(), "", 50, 0),
            "13" => new ShopItem("13", VanillaBlocks::WOOL()->setColor(DyeColor::YELLOW)->asItem()->setCount(32), "", 1600, 0),
            "14" => new ShopItem("14", VanillaBlocks::WOOL()->setColor(DyeColor::LIME)->asItem(), "", 50, 0),
            "15" => new ShopItem("15", VanillaBlocks::WOOL()->setColor(DyeColor::LIME)->asItem()->setCount(32), "", 1600, 0),
            "16" => new ShopItem("16", VanillaBlocks::WOOL()->setColor(DyeColor::PINK)->asItem(), "", 50, 0),
            "17" => new ShopItem("17", VanillaBlocks::WOOL()->setColor(DyeColor::PINK)->asItem()->setCount(32), "", 1600, 0),
            "18" => new ShopItem("18", VanillaBlocks::WOOL()->setColor(DyeColor::GRAY)->asItem(), "", 50, 0),
            "19" => new ShopItem("19", VanillaBlocks::WOOL()->setColor(DyeColor::GRAY)->asItem()->setCount(32), "", 1600, 0),
            "20" => new ShopItem("20", VanillaBlocks::WOOL()->setColor(DyeColor::LIGHT_GRAY)->asItem(), "", 50, 0),
            "21" => new ShopItem("21", VanillaBlocks::WOOL()->setColor(DyeColor::LIGHT_GRAY)->asItem()->setCount(32), "", 1600, 0),
            "22" => new ShopItem("22", VanillaBlocks::WOOL()->setColor(DyeColor::CYAN)->asItem(), "", 50, 0),
            "23" => new ShopItem("23", VanillaBlocks::WOOL()->setColor(DyeColor::CYAN)->asItem()->setCount(32), "", 1600, 0),
            "24" => new ShopItem("24", VanillaBlocks::WOOL()->setColor(DyeColor::PURPLE)->asItem(), "", 50, 0),
            "25" => new ShopItem("25", VanillaBlocks::WOOL()->setColor(DyeColor::PURPLE)->asItem()->setCount(32), "", 1600, 0),
            "26" => new ShopItem("26", VanillaBlocks::WOOL()->setColor(DyeColor::BLUE)->asItem(), "", 50, 0),
            "27" => new ShopItem("27", VanillaBlocks::WOOL()->setColor(DyeColor::BLUE)->asItem()->setCount(32), "", 1600, 0),
            "28" => new ShopItem("28", VanillaBlocks::WOOL()->setColor(DyeColor::BROWN)->asItem(), "", 50, 0),
            "29" => new ShopItem("29", VanillaBlocks::WOOL()->setColor(DyeColor::BROWN)->asItem()->setCount(32), "", 1600, 0),
            "30" => new ShopItem("30", VanillaBlocks::WOOL()->setColor(DyeColor::GREEN)->asItem(), "", 50, 0),
            "31" => new ShopItem("31", VanillaBlocks::WOOL()->setColor(DyeColor::GREEN)->asItem()->setCount(32), "", 1600, 0),
            "32" => new ShopItem("32", VanillaBlocks::WOOL()->setColor(DyeColor::RED)->asItem(), "", 50, 0),
            "33" => new ShopItem("33", VanillaBlocks::WOOL()->setColor(DyeColor::RED)->asItem()->setCount(32), "", 1600, 0),
            "34" => new ShopItem("34", VanillaBlocks::WOOL()->setColor(DyeColor::BLACK)->asItem(), "", 50, 0),
            "35" => new ShopItem("35", VanillaBlocks::WOOL()->setColor(DyeColor::BLACK)->asItem()->setCount(32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Glass", ItemSerializeUtils::jsonSerialize(VanillaBlocks::GLASS()->asItem()), [
            "4" => new ShopItem("4", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE)->asItem(), "", 50, 0),
            "5" => new ShopItem("5", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE)->asItem()->setCount(32), "", 1600, 0),
            "6" => new ShopItem("6", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::ORANGE)->asItem(), "", 50, 0),
            "7" => new ShopItem("7", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::ORANGE)->asItem()->setCount(32), "", 1600, 0),
            "8" => new ShopItem("8", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::MAGENTA)->asItem(), "", 50, 0),
            "9" => new ShopItem("9", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::MAGENTA)->asItem()->setCount(32), "", 1600, 0),
            "10" => new ShopItem("10", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIGHT_BLUE)->asItem(), "", 50, 0),
            "11" => new ShopItem("11", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIGHT_BLUE)->asItem()->setCount(32), "", 1600, 0),
            "12" => new ShopItem("12", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::YELLOW)->asItem(), "", 50, 0),
            "13" => new ShopItem("13", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::YELLOW)->asItem()->setCount(32), "", 1600, 0),
            "14" => new ShopItem("14", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIME)->asItem(), "", 50, 0),
            "15" => new ShopItem("15", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIME)->asItem()->setCount(32), "", 1600, 0),
            "16" => new ShopItem("16", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::PINK)->asItem(), "", 50, 0),
            "17" => new ShopItem("17", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::PINK)->asItem()->setCount(32), "", 1600, 0),
            "18" => new ShopItem("18", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GRAY)->asItem(), "", 50, 0),
            "19" => new ShopItem("19", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GRAY)->asItem()->setCount(32), "", 1600, 0),
            "20" => new ShopItem("20", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIGHT_GRAY)->asItem(), "", 50, 0),
            "21" => new ShopItem("21", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIGHT_GRAY)->asItem()->setCount(32), "", 1600, 0),
            "22" => new ShopItem("22", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::CYAN)->asItem(), "", 50, 0),
            "23" => new ShopItem("23", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::CYAN)->asItem()->setCount(32), "", 1600, 0),
            "24" => new ShopItem("24", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::PURPLE)->asItem(), "", 50, 0),
            "25" => new ShopItem("25", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::PURPLE)->asItem()->setCount(32), "", 1600, 0),
            "26" => new ShopItem("26", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLUE)->asItem(), "", 50, 0),
            "27" => new ShopItem("27", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLUE)->asItem()->setCount(32), "", 1600, 0),
            "28" => new ShopItem("28", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BROWN)->asItem(), "", 50, 0),
            "29" => new ShopItem("29", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BROWN)->asItem()->setCount(32), "", 1600, 0),
            "30" => new ShopItem("30", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN)->asItem(), "", 50, 0),
            "31" => new ShopItem("31", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN)->asItem()->setCount(32), "", 1600, 0),
            "32" => new ShopItem("32", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED)->asItem(), "", 50, 0),
            "33" => new ShopItem("33", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED)->asItem()->setCount(32), "", 1600, 0),
            "34" => new ShopItem("34", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK)->asItem(), "", 50, 0),
            "35" => new ShopItem("35", VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK)->asItem()->setCount(32), "", 1600, 0),
            "36" => new ShopItem("36", VanillaBlocks::GLASS()->asItem(), "", 50, 0),
            "37" => new ShopItem("37", VanillaBlocks::GLASS()->asItem()->setCount(32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Terracotta", ItemSerializeUtils::jsonSerialize(VanillaBlocks::HARDENED_CLAY()->asItem()), [
            "4" => new ShopItem("4", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::WHITE)->asItem(), "", 50, 0),
            "5" => new ShopItem("5", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::WHITE)->asItem()->setCount(32), "", 1600, 0),
            "6" => new ShopItem("6", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::ORANGE)->asItem(), "", 50, 0),
            "7" => new ShopItem("7", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::ORANGE)->asItem()->setCount(32), "", 1600, 0),
            "8" => new ShopItem("8", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::MAGENTA)->asItem(), "", 50, 0),
            "9" => new ShopItem("9", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::MAGENTA)->asItem()->setCount(32), "", 1600, 0),
            "10" => new ShopItem("10", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIGHT_BLUE)->asItem(), "", 50, 0),
            "11" => new ShopItem("11", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIGHT_BLUE)->asItem()->setCount(32), "", 1600, 0),
            "12" => new ShopItem("12", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::YELLOW)->asItem(), "", 50, 0),
            "13" => new ShopItem("13", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::YELLOW)->asItem()->setCount(32), "", 1600, 0),
            "14" => new ShopItem("14", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIME)->asItem(), "", 50, 0),
            "15" => new ShopItem("15", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIME)->asItem()->setCount(32), "", 1600, 0),
            "16" => new ShopItem("16", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::PINK)->asItem(), "", 50, 0),
            "17" => new ShopItem("17", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::PINK)->asItem()->setCount(32), "", 1600, 0),
            "18" => new ShopItem("18", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::GRAY)->asItem(), "", 50, 0),
            "19" => new ShopItem("19", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::GRAY)->asItem()->setCount(32), "", 1600, 0),
            "20" => new ShopItem("20", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIGHT_GRAY)->asItem(), "", 50, 0),
            "21" => new ShopItem("21", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIGHT_GRAY)->asItem()->setCount(32), "", 1600, 0),
            "22" => new ShopItem("22", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::CYAN)->asItem(), "", 50, 0),
            "23" => new ShopItem("23", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::CYAN)->asItem()->setCount(32), "", 1600, 0),
            "24" => new ShopItem("24", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::PURPLE)->asItem(), "", 50, 0),
            "25" => new ShopItem("25", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::PURPLE)->asItem()->setCount(32), "", 1600, 0),
            "26" => new ShopItem("26", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BLUE)->asItem(), "", 50, 0),
            "27" => new ShopItem("27", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BLUE)->asItem()->setCount(32), "", 1600, 0),
            "28" => new ShopItem("28", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BROWN)->asItem(), "", 50, 0),
            "29" => new ShopItem("29", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BROWN)->asItem()->setCount(32), "", 1600, 0),
            "30" => new ShopItem("30", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::GREEN)->asItem(), "", 50, 0),
            "31" => new ShopItem("31", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::GREEN)->asItem()->setCount(32), "", 1600, 0),
            "32" => new ShopItem("32", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::RED)->asItem(), "", 50, 0),
            "33" => new ShopItem("33", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::RED)->asItem()->setCount(32), "", 1600, 0),
            "34" => new ShopItem("34", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BLACK)->asItem(), "", 50, 0),
            "35" => new ShopItem("35", VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BLACK)->asItem()->setCount(32), "", 1600, 0),
			"36" => new ShopItem("36", VanillaBlocks::HARDENED_CLAY()->asItem(), "", 50, 0),
			"37" => new ShopItem("37", VanillaBlocks::HARDENED_CLAY()->asItem()->setCount(32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Concrete", ItemSerializeUtils::jsonSerialize(VanillaBlocks::CONCRETE()->asItem()), [
            "4" => new ShopItem("4", VanillaBlocks::CONCRETE()->setColor(DyeColor::WHITE)->asItem(), "", 50, 0),
            "5" => new ShopItem("5", VanillaBlocks::CONCRETE()->setColor(DyeColor::WHITE)->asItem()->setCount(32), "", 1600, 0),
            "6" => new ShopItem("6", VanillaBlocks::CONCRETE()->setColor(DyeColor::ORANGE)->asItem(), "", 50, 0),
            "7" => new ShopItem("7", VanillaBlocks::CONCRETE()->setColor(DyeColor::ORANGE)->asItem()->setCount(32), "", 1600, 0),
            "8" => new ShopItem("8", VanillaBlocks::CONCRETE()->setColor(DyeColor::MAGENTA)->asItem(), "", 50, 0),
            "9" => new ShopItem("9", VanillaBlocks::CONCRETE()->setColor(DyeColor::MAGENTA)->asItem()->setCount(32), "", 1600, 0),
            "10" => new ShopItem("10", VanillaBlocks::CONCRETE()->setColor(DyeColor::LIGHT_BLUE)->asItem(), "", 50, 0),
            "11" => new ShopItem("11", VanillaBlocks::CONCRETE()->setColor(DyeColor::LIGHT_BLUE)->asItem()->setCount(32), "", 1600, 0),
            "12" => new ShopItem("12", VanillaBlocks::CONCRETE()->setColor(DyeColor::YELLOW)->asItem(), "", 50, 0),
            "13" => new ShopItem("13", VanillaBlocks::CONCRETE()->setColor(DyeColor::YELLOW)->asItem()->setCount(32), "", 1600, 0),
            "14" => new ShopItem("14", VanillaBlocks::CONCRETE()->setColor(DyeColor::LIME)->asItem(), "", 50, 0),
            "15" => new ShopItem("15", VanillaBlocks::CONCRETE()->setColor(DyeColor::LIME)->asItem()->setCount(32), "", 1600, 0),
            "16" => new ShopItem("16", VanillaBlocks::CONCRETE()->setColor(DyeColor::PINK)->asItem(), "", 50, 0),
            "17" => new ShopItem("17", VanillaBlocks::CONCRETE()->setColor(DyeColor::PINK)->asItem()->setCount(32), "", 1600, 0),
            "18" => new ShopItem("18", VanillaBlocks::CONCRETE()->setColor(DyeColor::GRAY)->asItem(), "", 50, 0),
            "19" => new ShopItem("19", VanillaBlocks::CONCRETE()->setColor(DyeColor::GRAY)->asItem()->setCount(32), "", 1600, 0),
            "20" => new ShopItem("20", VanillaBlocks::CONCRETE()->setColor(DyeColor::LIGHT_GRAY)->asItem(), "", 50, 0),
            "21" => new ShopItem("21", VanillaBlocks::CONCRETE()->setColor(DyeColor::LIGHT_GRAY)->asItem()->setCount(32), "", 1600, 0),
            "22" => new ShopItem("22", VanillaBlocks::CONCRETE()->setColor(DyeColor::CYAN)->asItem(), "", 50, 0),
            "23" => new ShopItem("23", VanillaBlocks::CONCRETE()->setColor(DyeColor::CYAN)->asItem()->setCount(32), "", 1600, 0),
            "24" => new ShopItem("24", VanillaBlocks::CONCRETE()->setColor(DyeColor::PURPLE)->asItem(), "", 50, 0),
            "25" => new ShopItem("25", VanillaBlocks::CONCRETE()->setColor(DyeColor::PURPLE)->asItem()->setCount(32), "", 1600, 0),
            "26" => new ShopItem("26", VanillaBlocks::CONCRETE()->setColor(DyeColor::BLUE)->asItem(), "", 50, 0),
            "27" => new ShopItem("27", VanillaBlocks::CONCRETE()->setColor(DyeColor::BLUE)->asItem()->setCount(32), "", 1600, 0),
            "28" => new ShopItem("28", VanillaBlocks::CONCRETE()->setColor(DyeColor::BROWN)->asItem(), "", 50, 0),
            "29" => new ShopItem("29", VanillaBlocks::CONCRETE()->setColor(DyeColor::BROWN)->asItem()->setCount(32), "", 1600, 0),
            "30" => new ShopItem("30", VanillaBlocks::CONCRETE()->setColor(DyeColor::GREEN)->asItem(), "", 50, 0),
            "31" => new ShopItem("31", VanillaBlocks::CONCRETE()->setColor(DyeColor::GREEN)->asItem()->setCount(32), "", 1600, 0),
            "32" => new ShopItem("32", VanillaBlocks::CONCRETE()->setColor(DyeColor::RED)->asItem(), "", 50, 0),
            "33" => new ShopItem("33", VanillaBlocks::CONCRETE()->setColor(DyeColor::RED)->asItem()->setCount(32), "", 1600, 0),
            "34" => new ShopItem("34", VanillaBlocks::CONCRETE()->setColor(DyeColor::BLACK)->asItem(), "", 50, 0),
            "35" => new ShopItem("35", VanillaBlocks::CONCRETE()->setColor(DyeColor::BLACK)->asItem()->setCount(32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Concrete Powder", ItemSerializeUtils::jsonSerialize(VanillaBlocks::CONCRETE_POWDER()->asItem()), [
            "4" => new ShopItem("4", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::WHITE)->asItem(), "", 50, 0),
            "5" => new ShopItem("5", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::WHITE)->asItem()->setCount(32), "", 1600, 0),
            "6" => new ShopItem("6", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::ORANGE)->asItem(), "", 50, 0),
            "7" => new ShopItem("7", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::ORANGE)->asItem()->setCount(32), "", 1600, 0),
            "8" => new ShopItem("8", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::MAGENTA)->asItem(), "", 50, 0),
            "9" => new ShopItem("9", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::MAGENTA)->asItem()->setCount(32), "", 1600, 0),
            "10" => new ShopItem("10", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::LIGHT_BLUE)->asItem(), "", 50, 0),
            "11" => new ShopItem("11", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::LIGHT_BLUE)->asItem()->setCount(32), "", 1600, 0),
            "12" => new ShopItem("12", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::YELLOW)->asItem(), "", 50, 0),
            "13" => new ShopItem("13", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::YELLOW)->asItem()->setCount(32), "", 1600, 0),
            "14" => new ShopItem("14", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::LIME)->asItem(), "", 50, 0),
            "15" => new ShopItem("15", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::LIME)->asItem()->setCount(32), "", 1600, 0),
            "16" => new ShopItem("16", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::PINK)->asItem(), "", 50, 0),
            "17" => new ShopItem("17", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::PINK)->asItem()->setCount(32), "", 1600, 0),
            "18" => new ShopItem("18", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::GRAY)->asItem(), "", 50, 0),
            "19" => new ShopItem("19", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::GRAY)->asItem()->setCount(32), "", 1600, 0),
            "20" => new ShopItem("20", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::LIGHT_GRAY)->asItem(), "", 50, 0),
            "21" => new ShopItem("21", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::LIGHT_GRAY)->asItem()->setCount(32), "", 1600, 0),
            "22" => new ShopItem("22", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::CYAN)->asItem(), "", 50, 0),
            "23" => new ShopItem("23", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::CYAN)->asItem()->setCount(32), "", 1600, 0),
            "24" => new ShopItem("24", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::PURPLE)->asItem(), "", 50, 0),
            "25" => new ShopItem("25", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::PURPLE)->asItem()->setCount(32), "", 1600, 0),
            "26" => new ShopItem("26", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::BLUE)->asItem(), "", 50, 0),
            "27" => new ShopItem("27", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::BLUE)->asItem()->setCount(32), "", 1600, 0),
            "28" => new ShopItem("28", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::BROWN)->asItem(), "", 50, 0),
            "29" => new ShopItem("29", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::BROWN)->asItem()->setCount(32), "", 1600, 0),
            "30" => new ShopItem("30", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::GREEN)->asItem(), "", 50, 0),
            "31" => new ShopItem("31", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::GREEN)->asItem()->setCount(32), "", 1600, 0),
            "32" => new ShopItem("32", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::RED)->asItem(), "", 50, 0),
            "33" => new ShopItem("33", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::RED)->asItem()->setCount(32), "", 1600, 0),
            "34" => new ShopItem("34", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::BLACK)->asItem(), "", 50, 0),
            "35" => new ShopItem("35", VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::BLACK)->asItem()->setCount(32), "", 1600, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Stone Blocks", ItemSerializeUtils::jsonSerialize(VanillaBlocks::STONE()->asItem()), [
			"0" => new ShopItem("0", VanillaBlocks::STONE()->asItem(), "", 20, 0),
			"1" => new ShopItem("1", VanillaBlocks::STONE()->asItem()->setCount(32), "", 640, 0),
			"2" => new ShopItem("2", VanillaBlocks::STONE_STAIRS()->asItem(), "", 20, 0),
			"3" => new ShopItem("3", VanillaBlocks::STONE_STAIRS()->asItem()->setCount(32), "", 640, 0),
			"4" => new ShopItem("4", VanillaBlocks::STONE_SLAB()->asItem(), "", 20, 0),
			"5" => new ShopItem("5", VanillaBlocks::STONE_SLAB()->asItem()->setCount(32), "", 640, 0),
			"6" => new ShopItem("6", VanillaBlocks::COBBLESTONE()->asItem(), "", 20, 0),
			"7" => new ShopItem("7", VanillaBlocks::COBBLESTONE()->asItem()->setCount(32), "", 640, 0),
			"8" => new ShopItem("8", VanillaBlocks::COBBLESTONE_STAIRS()->asItem(), "", 20, 0),
			"9" => new ShopItem("9", VanillaBlocks::COBBLESTONE_STAIRS()->asItem()->setCount(32), "", 640, 0),
			"10" => new ShopItem("10", VanillaBlocks::COBBLESTONE_SLAB()->asItem(), "", 20, 0),
			"11" => new ShopItem("11", VanillaBlocks::COBBLESTONE_SLAB()->asItem()->setCount(32), "", 640, 0),
			"12" => new ShopItem("12", VanillaBlocks::STONE_BRICKS()->asItem(), "", 40, 0),
			"13" => new ShopItem("13", VanillaBlocks::STONE_BRICKS()->asItem()->setCount(32), "", 1280, 0),
			"14" => new ShopItem("14", VanillaBlocks::MOSSY_STONE_BRICKS()->asItem(), "", 40, 0),
			"15" => new ShopItem("15", VanillaBlocks::MOSSY_STONE_BRICKS()->asItem()->setCount(32), "", 1280, 0),
			"16" => new ShopItem("16", VanillaBlocks::CRACKED_STONE_BRICKS()->asItem(), "", 40, 0),
			"17" => new ShopItem("17", VanillaBlocks::CRACKED_STONE_BRICKS()->asItem()->setCount(32), "", 1280, 0),
			"18" => new ShopItem("18", VanillaBlocks::CHISELED_STONE_BRICKS()->asItem(), "", 40, 0),
			"19" => new ShopItem("19", VanillaBlocks::CHISELED_STONE_BRICKS()->asItem()->setCount(32), "", 1280, 0),
			"20" => new ShopItem("20", VanillaBlocks::STONE_BRICK_STAIRS()->asItem(), "", 40, 0),
			"21" => new ShopItem("21", VanillaBlocks::STONE_BRICK_STAIRS()->asItem()->setCount(32), "", 1280, 0),
			"22" => new ShopItem("22", VanillaBlocks::MOSSY_STONE_BRICK_STAIRS()->asItem(), "", 40, 0),
			"23" => new ShopItem("23", VanillaBlocks::MOSSY_STONE_BRICK_STAIRS()->asItem()->setCount(32), "", 1280, 0),
			"24" => new ShopItem("24", VanillaBlocks::STONE_BRICK_SLAB()->asItem(), "", 40, 0),
			"25" => new ShopItem("25", VanillaBlocks::STONE_BRICK_SLAB()->asItem()->setCount(32), "", 1280, 0),
			"26" => new ShopItem("26", VanillaBlocks::MOSSY_STONE_BRICK_SLAB()->asItem(), "", 40, 0),
			"27" => new ShopItem("27", VanillaBlocks::MOSSY_STONE_BRICK_SLAB()->asItem()->setCount(32), "", 1280, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Wood", ItemSerializeUtils::jsonSerialize(VanillaBlocks::OAK_PLANKS()->asItem()), [
			"0" => new ShopItem("0", VanillaBlocks::OAK_LOG()->asItem(), "", 80, 0),
			"1" => new ShopItem("1", VanillaBlocks::OAK_LOG()->asItem()->setCount(32), "", 2580, 0),
			"2" => new ShopItem("2", VanillaBlocks::SPRUCE_LOG()->asItem(), "", 80, 0),
			"3" => new ShopItem("3", VanillaBlocks::SPRUCE_LOG()->asItem()->setCount(32), "", 2580, 0),
			"4" => new ShopItem("4", VanillaBlocks::BIRCH_LOG()->asItem(), "", 80, 0),
			"5" => new ShopItem("5", VanillaBlocks::BIRCH_LOG()->asItem()->setCount(32), "", 2580, 0),
			"6" => new ShopItem("6", VanillaBlocks::JUNGLE_LOG()->asItem(), "", 80, 0),
			"7" => new ShopItem("7", VanillaBlocks::JUNGLE_LOG()->asItem()->setCount(32), "", 2580, 0),
			"8" => new ShopItem("8", VanillaBlocks::ACACIA_LOG()->asItem(), "", 80, 0),
			"9" => new ShopItem("9", VanillaBlocks::ACACIA_LOG()->asItem()->setCount(32), "", 2580, 0),
			"10" => new ShopItem("10", VanillaBlocks::DARK_OAK_LOG()->asItem(), "", 80, 0),
			"11" => new ShopItem("11", VanillaBlocks::DARK_OAK_LOG()->asItem()->setCount(32), "", 2580, 0),
			// "12" => new ShopItem("12", VanillaBlocks::STRIPPED_OAK_LOG()->asItem(), "", 80, 0),
			// "13" => new ShopItem("13", VanillaBlocks::STRIPPED_OAK_LOG()->asItem()->setCount(32), "", 2580, 0),
			// "14" => new ShopItem("14", VanillaBlocks::STRIPPED_SPRUCE_LOG()->asItem(), "", 80, 0),
			// "15" => new ShopItem("15", VanillaBlocks::STRIPPED_SPRUCE_LOG()->asItem()->setCount(32), "", 2580, 0),
			// "16" => new ShopItem("16", VanillaBlocks::STRIPPED_BIRCH_LOG()->asItem(), "", 80, 0),
			// "17" => new ShopItem("17", VanillaBlocks::STRIPPED_BIRCH_LOG()->asItem()->setCount(32), "", 2580, 0),
			// "18" => new ShopItem("18", VanillaBlocks::STRIPPED_JUNGLE_LOG()->asItem(), "", 80, 0),
			// "19" => new ShopItem("19", VanillaBlocks::STRIPPED_JUNGLE_LOG()->asItem()->setCount(32), "", 2580, 0),
			// "20" => new ShopItem("20", VanillaBlocks::STRIPPED_ACACIA_LOG()->asItem(), "", 80, 0),
			// "21" => new ShopItem("21", VanillaBlocks::STRIPPED_ACACIA_LOG()->asItem()->setCount(32), "", 2580, 0),
			// "22" => new ShopItem("22", VanillaBlocks::STRIPPED_DARK_OAK_LOG()->asItem(), "", 80, 0),
			// "23" => new ShopItem("23", VanillaBlocks::STRIPPED_DARK_OAK_LOG()->asItem()->setCount(32), "", 2580, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Nature", ItemSerializeUtils::jsonSerialize(VanillaBlocks::OAK_LEAVES()->asItem()), [
			"0" => new ShopItem("0", VanillaBlocks::DIRT()->asItem(), "", 2, 0),
			"1" => new ShopItem("1", VanillaBlocks::DIRT()->asItem()->setCount(32), "", 64, 0),
			"2" => new ShopItem("2", VanillaBlocks::GRASS()->asItem(), "", 2, 0),
			"3" => new ShopItem("3", VanillaBlocks::GRASS()->asItem()->setCount(32), "", 64, 0),
			"4" => new ShopItem("4", VanillaBlocks::OAK_LEAVES()->asItem(), "", 4, 0),
			"5" => new ShopItem("5", VanillaBlocks::OAK_LEAVES()->asItem()->setCount(32), "", 128, 0),
			"6" => new ShopItem("6", VanillaBlocks::SPRUCE_LEAVES()->asItem(), "", 4, 0),
			"7" => new ShopItem("7", VanillaBlocks::SPRUCE_LEAVES()->asItem()->setCount(32), "", 128, 0),
			"8" => new ShopItem("8", VanillaBlocks::BIRCH_LEAVES()->asItem(), "", 4, 0),
			"9" => new ShopItem("9", VanillaBlocks::BIRCH_LEAVES()->asItem()->setCount(32), "", 128, 0),
			"10" => new ShopItem("10", VanillaBlocks::JUNGLE_LEAVES()->asItem(), "", 4, 0),
			"11" => new ShopItem("11", VanillaBlocks::JUNGLE_LEAVES()->asItem()->setCount(32), "", 128, 0),
			"12" => new ShopItem("12", VanillaBlocks::ACACIA_LEAVES()->asItem(), "", 4, 0),
			"13" => new ShopItem("13", VanillaBlocks::ACACIA_LEAVES()->asItem()->setCount(32), "", 128, 0),
			"14" => new ShopItem("14", VanillaBlocks::DARK_OAK_LEAVES()->asItem(), "", 4, 0),
			"15" => new ShopItem("15", VanillaBlocks::DARK_OAK_LEAVES()->asItem()->setCount(32), "", 128, 0),
        ]);
        $defaults[] = $category;

        $category = new ShopCategory("Nether", ItemSerializeUtils::jsonSerialize(VanillaItems::NETHER_QUARTZ()), [
			"0" => new ShopItem("0", VanillaBlocks::QUARTZ()->asItem(), "", 50, 0),
			"1" => new ShopItem("1", VanillaBlocks::QUARTZ()->asItem()->setCount(32), "", 1600, 0),
			"2" => new ShopItem("2", VanillaBlocks::QUARTZ_STAIRS()->asItem(), "", 50, 0),
			"3" => new ShopItem("3", VanillaBlocks::QUARTZ_STAIRS()->asItem()->setCount(32), "", 1600, 0),
			"4" => new ShopItem("4", VanillaBlocks::SMOOTH_QUARTZ_STAIRS()->asItem(), "", 50, 0),
			"5" => new ShopItem("5", VanillaBlocks::SMOOTH_QUARTZ_STAIRS()->asItem()->setCount(32), "", 1600, 0),
			"6" => new ShopItem("6", VanillaBlocks::QUARTZ_SLAB()->asItem(), "", 50, 0),
			"7" => new ShopItem("7", VanillaBlocks::QUARTZ_SLAB()->asItem()->setCount(32), "", 1600, 0),
			"8" => new ShopItem("8", VanillaBlocks::SMOOTH_QUARTZ_SLAB()->asItem(), "", 50, 0),
			"9" => new ShopItem("9", VanillaBlocks::SMOOTH_QUARTZ_SLAB()->asItem()->setCount(32), "", 1600, 0),
			"10" => new ShopItem("10", VanillaBlocks::NETHER_QUARTZ_ORE()->asItem(), "", 50, 0),
			"11" => new ShopItem("11", VanillaBlocks::NETHER_QUARTZ_ORE()->asItem()->setCount(32), "", 1600, 0),
			"12" => new ShopItem("12", VanillaBlocks::NETHER_BRICKS()->asItem(), "", 50, 0),
			"13" => new ShopItem("13", VanillaBlocks::NETHER_BRICKS()->asItem()->setCount(32), "", 1600, 0),
			"14" => new ShopItem("14", VanillaBlocks::NETHER_BRICK_FENCE()->asItem(), "", 50, 0),
			"15" => new ShopItem("15", VanillaBlocks::NETHER_BRICK_FENCE()->asItem()->setCount(32), "", 1600, 0),
			"16" => new ShopItem("16", VanillaBlocks::NETHERRACK()->asItem(), "", 50, 0),
			"17" => new ShopItem("17", VanillaBlocks::NETHERRACK()->asItem()->setCount(32), "", 1600, 0),
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