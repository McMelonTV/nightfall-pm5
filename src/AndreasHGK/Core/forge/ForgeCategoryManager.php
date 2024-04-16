<?php

namespace AndreasHGK\Core\forge;

use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\Price;

class ForgeCategoryManager {

    private static $instance;

    /**
     * @var array|ForgeCategory[]
     */
    private $categories = [];

    /**
     * @return array|ForgeCategory[]
     */
    public function getAll() : array {
        return $this->categories;
    }

    public function get(string $name) : ?ForgeCategory {
        return $this->categories[$name] ?? null;
    }

    public function add(ForgeCategory $shopCategory) : void {
        $this->categories[$shopCategory->getName()] = $shopCategory;
    }

    public function exist(string $category) : bool {
        return isset($this->categories[$category]);
    }

    public function registerDefaults() : void {
        $defaults = [
            new ForgeCategory("Weapons", [
                new ForgeItem("basicsword1", CustomItem::BASICSWORD.":1", new Price(10), "§r§0§8T1 §r§bBasic Sword"),
                new ForgeItem("basicsword2", CustomItem::BASICSWORD.":2", new Price(25), "§r§0§8T2 §r§bBasic Sword"),
                new ForgeItem("basicsword3", CustomItem::BASICSWORD.":3", new Price(50), "§r§0§8T3 §r§bBasic Sword"),
                new ForgeItem("basicsword4", CustomItem::BASICSWORD.":4", new Price(100), "§r§0§8T4 §r§bBasic Sword"),
                new ForgeItem("basicsword5", CustomItem::BASICSWORD.":5", new Price(160), "§r§0§8T5 §r§bBasic Sword"),
                new ForgeItem("advancedsword1", CustomItem::ADVANCEDSWORD.":1", new Price(0, 0, 0, 10), "§r§0§8T1 §r§cAdvanced Sword"),
                new ForgeItem("advancedsword2", CustomItem::ADVANCEDSWORD.":2", new Price(0, 0, 0, 25), "§r§0§8T2 §r§cAdvanced Sword"),
                new ForgeItem("advancedsword3", CustomItem::ADVANCEDSWORD.":3", new Price(0, 0, 0, 50), "§r§0§8T3 §r§cAdvanced Sword"),
                new ForgeItem("advancedsword4", CustomItem::ADVANCEDSWORD.":4", new Price(0, 0, 0, 100), "§r§0§8T4 §r§cAdvanced Sword"),
                new ForgeItem("advancedsword5", CustomItem::ADVANCEDSWORD.":5", new Price(0, 0, 0, 160), "§r§0§8T5 §r§cAdvanced Sword"),
            ], "§8Weapons"),
            new ForgeCategory("Pickaxes", [
                new ForgeItem("basicpickaxe1", CustomItem::BASICPICKAXE.":1", new Price(5), "§r§0§8T1 §r§bBasic Pickaxe"), //\n§r8Base Durability: 200"),
                new ForgeItem("basicpickaxe2", CustomItem::BASICPICKAXE.":2", new Price(15), "§r§0§8T2 §r§bBasic Pickaxe"), //\n§r8Base Durability: 400§7, §8Efficiency §b1"),
                new ForgeItem("basicpickaxe3", CustomItem::BASICPICKAXE.":3", new Price(30), "§r§0§8T3 §r§bBasic Pickaxe"), //\n§r8Base Durability: 600§7, §8Efficiency §b2"),
                new ForgeItem("basicpickaxe4", CustomItem::BASICPICKAXE.":4", new Price(50), "§r§0§8T4 §r§bBasic Pickaxe"), //\n§r8Base Durability: 800§7, §8Efficiency §b3"),
                new ForgeItem("basicpickaxe5", CustomItem::BASICPICKAXE.":5", new Price(70), "§r§0§8T5 §r§bBasic Pickaxe"), //\n§r8Base Durability: 1000§7, §8Efficiency §b4"),
                new ForgeItem("basicpickaxe6", CustomItem::BASICPICKAXE.":6", new Price(100), "§r§0§8T6 §r§bBasic Pickaxe"), //\n§r8Base Durability: 1200§7, §8Efficiency §b5"),
                new ForgeItem("basicpickaxe7", CustomItem::BASICPICKAXE.":7", new Price(130), "§r§0§8T7 §r§bBasic Pickaxe"), //\n§r8Base Durability: 1400§7, §8Efficiency §b6"),
                new ForgeItem("basicpickaxe8", CustomItem::BASICPICKAXE.":8", new Price(160), "§r§0§8T8 §r§bBasic Pickaxe"), //§r8Base Durability: 1600§7, §8Efficiency §b7"),
                new ForgeItem("basicpickaxe9", CustomItem::BASICPICKAXE.":9", new Price(200), "§r§0§8T9 §r§bBasic Pickaxe"), //\n§r8Base Durability: 1800§7, §8Efficiency §b8"),
                new ForgeItem("basicpickaxe10", CustomItem::BASICPICKAXE.":10", new Price(250), "§r§0§8T10 §r§bBasic Pickaxe"), //\n§r8Base Durability: §b2000§7, §8Efficiency §b9"),
                new ForgeItem("advancedpickaxe1", CustomItem::ADVANCEDPICKAXE.":1", new Price(0, 0, 0, 25), "§r§0§8T1 §r§cAdvanced Pickaxe"),
                new ForgeItem("advancedpickaxe2", CustomItem::ADVANCEDPICKAXE.":2", new Price(0, 0, 0, 50), "§r§0§8T2 §r§cAdvanced Pickaxe"),
                new ForgeItem("advancedpickaxe3", CustomItem::ADVANCEDPICKAXE.":3", new Price(0, 0, 0, 75), "§r§0§8T3 §r§cAdvanced Pickaxe"),
                new ForgeItem("advancedpickaxe4", CustomItem::ADVANCEDPICKAXE.":4", new Price(0, 0, 0, 100), "§r§0§8T4 §r§cAdvanced Pickaxe"),
                new ForgeItem("advancedpickaxe5", CustomItem::ADVANCEDPICKAXE.":5", new Price(0, 0, 0, 125), "§r§0§8T5 §r§cAdvanced Pickaxe"),
                new ForgeItem("advancedpickaxe6", CustomItem::ADVANCEDPICKAXE.":6", new Price(0, 0, 0, 150), "§r§0§8T6 §r§cAdvanced Pickaxe"),
                new ForgeItem("advancedpickaxe7", CustomItem::ADVANCEDPICKAXE.":7", new Price(0, 0, 0, 175), "§r§0§8T7 §r§cAdvanced Pickaxe"),
                new ForgeItem("advancedpickaxe8", CustomItem::ADVANCEDPICKAXE.":8", new Price(0, 0, 0, 200), "§r§0§8T8 §r§cAdvanced Pickaxe"),
                new ForgeItem("advancedpickaxe9", CustomItem::ADVANCEDPICKAXE.":9", new Price(0, 0, 0, 225), "§r§0§8T9 §r§cAdvanced Pickaxe"),
                new ForgeItem("advancedpickaxe10", CustomItem::ADVANCEDPICKAXE.":10", new Price(0, 0, 0, 250), "§r§0§8T10 §r§cAdvanced Pickaxe"),
            ], "§8Pickaxes"),
            new ForgeCategory("Helmets", [
                new ForgeItem("basichelmet1", CustomItem::BASICHELMET.":1", new Price(10), "§r§0§8T1 §r§bBasic Helmet"),
                new ForgeItem("basichelmet2", CustomItem::BASICHELMET.":2", new Price(25), "§r§0§8T2 §r§bBasic Helmet"),
                new ForgeItem("basichelmet3", CustomItem::BASICHELMET.":3", new Price(50), "§r§0§8T3 §r§bBasic Helmet"),
                new ForgeItem("basichelmet4", CustomItem::BASICHELMET.":4", new Price(100), "§r§0§8T4 §r§bBasic Helmet"),
                new ForgeItem("basichelmet5", CustomItem::BASICHELMET.":5", new Price(160), "§r§0§8T5 §r§bBasic Helmet"),
                new ForgeItem("advancedhelmet1", CustomItem::ADVANCEDHELMET.":1", new Price(0, 0, 0, 10), "§r§0§8T1 §r§cAdvanced Helmet"),
                new ForgeItem("advancedhelmet2", CustomItem::ADVANCEDHELMET.":2", new Price(0, 0, 0, 25), "§r§0§8T2 §r§cAdvanced Helmet"),
                new ForgeItem("advancedhelmet3", CustomItem::ADVANCEDHELMET.":3", new Price(0, 0, 0, 50), "§r§0§8T3 §r§cAdvanced Helmet"),
                new ForgeItem("advancedhelmet4", CustomItem::ADVANCEDHELMET.":4", new Price(0, 0, 0, 100), "§r§0§8T4 §r§cAdvanced Helmet"),
                new ForgeItem("advancedhelmet5", CustomItem::ADVANCEDHELMET.":5", new Price(0, 0, 0, 160), "§r§0§8T5 §r§cAdvanced Helmet"),
            ], "§8Helmets"),
            new ForgeCategory("Chestplates", [
                new ForgeItem("basicchestplate1", CustomItem::BASICCHESTPLATE.":1", new Price(10), "§r§0§8T1 §r§bBasic Chestplate"),
                new ForgeItem("basicchestplate2", CustomItem::BASICCHESTPLATE.":2", new Price(25), "§r§0§8T2 §r§bBasic Chestplate"),
                new ForgeItem("basicchestplate3", CustomItem::BASICCHESTPLATE.":3", new Price(50), "§r§0§8T3 §r§bBasic Chestplate"),
                new ForgeItem("basicchestplate4", CustomItem::BASICCHESTPLATE.":4", new Price(100), "§r§0§8T4 §r§bBasic Chestplate"),
                new ForgeItem("basicchestplate5", CustomItem::BASICCHESTPLATE.":5", new Price(160), "§r§0§8T5 §r§bBasic Chestplate"),
                new ForgeItem("advancedchestplate1", CustomItem::ADVANCEDCHESTPLATE.":1", new Price(0, 0, 0, 10), "§r§0§8T1 §r§cAdvanced Chestplate"),
                new ForgeItem("advancedchestplate2", CustomItem::ADVANCEDCHESTPLATE.":2", new Price(0, 0, 0, 25), "§r§0§8T2 §r§cAdvanced Chestplate"),
                new ForgeItem("advancedchestplate3", CustomItem::ADVANCEDCHESTPLATE.":3", new Price(0, 0, 0, 50), "§r§0§8T3 §r§cAdvanced Chestplate"),
                new ForgeItem("advancedchestplate4", CustomItem::ADVANCEDCHESTPLATE.":4", new Price(0, 0, 0, 100), "§r§0§8T4 §r§cAdvanced Chestplate"),
                new ForgeItem("advancedchestplate5", CustomItem::ADVANCEDCHESTPLATE.":5", new Price(0, 0, 0, 160), "§r§0§8T5 §r§cAdvanced Chestplate"),
            ], "§8Chestplates"),
            new ForgeCategory("Leggings", [
                new ForgeItem("basicleggings1", CustomItem::BASICLEGGINGS.":1", new Price(10), "§r§0§8T1 §r§bBasic Leggings"),
                new ForgeItem("basicleggings2", CustomItem::BASICLEGGINGS.":2", new Price(25), "§r§0§8T2 §r§bBasic Leggings"),
                new ForgeItem("basicleggings3", CustomItem::BASICLEGGINGS.":3", new Price(50), "§r§0§8T3 §r§bBasic Leggings"),
                new ForgeItem("basicleggings4", CustomItem::BASICLEGGINGS.":4", new Price(100), "§r§0§8T4 §r§bBasic Leggings"),
                new ForgeItem("basicleggings5", CustomItem::BASICLEGGINGS.":5", new Price(160), "§r§0§8T5 §r§bBasic Leggings"),
                new ForgeItem("advancedleggings1", CustomItem::ADVANCEDLEGGINGS.":1", new Price(0, 0, 0, 10), "§r§0§8T1 §r§cAdvanced Leggings"),
                new ForgeItem("advancedleggings2", CustomItem::ADVANCEDLEGGINGS.":2", new Price(0, 0, 0, 25), "§r§0§8T2 §r§cAdvanced Leggings"),
                new ForgeItem("advancedleggings3", CustomItem::ADVANCEDLEGGINGS.":3", new Price(0, 0, 0, 50), "§r§0§8T3 §r§cAdvanced Leggings"),
                new ForgeItem("advancedleggings4", CustomItem::ADVANCEDLEGGINGS.":4", new Price(0, 0, 0, 100), "§r§0§8T4 §r§cAdvanced Leggings"),
                new ForgeItem("advancedleggings5", CustomItem::ADVANCEDLEGGINGS.":5", new Price(0, 0, 0, 160), "§r§0§8T5 §r§cAdvanced Leggings"),
            ], "§8Leggings"),
            new ForgeCategory("Boots", [
                new ForgeItem("basicboots1", CustomItem::BASICBOOTS.":1", new Price(10), "§r§0§8T1 §r§bBasic Boots"),
                new ForgeItem("basicboots2", CustomItem::BASICBOOTS.":2", new Price(25), "§r§0§8T2 §r§bBasic Boots"),
                new ForgeItem("basicboots3", CustomItem::BASICBOOTS.":3", new Price(50), "§r§0§8T3 §r§bBasic Boots"),
                new ForgeItem("basicboots4", CustomItem::BASICBOOTS.":4", new Price(100), "§r§0§8T4 §r§bBasic Boots"),
                new ForgeItem("basicboots5", CustomItem::BASICBOOTS.":5", new Price(160), "§r§0§8T5 §r§bBasic Boots"),
                new ForgeItem("advancedboots1", CustomItem::ADVANCEDBOOTS.":1", new Price(0, 0, 0, 10), "§r§0§8T1 §r§cAdvanced Boots"),
                new ForgeItem("advancedboots2", CustomItem::ADVANCEDBOOTS.":2", new Price(0, 0, 0, 25), "§r§0§8T2 §r§cAdvanced Boots"),
                new ForgeItem("advancedboots3", CustomItem::ADVANCEDBOOTS.":3", new Price(0, 0, 0, 50), "§r§0§8T3 §r§cAdvanced Boots"),
                new ForgeItem("advancedboots4", CustomItem::ADVANCEDBOOTS.":4", new Price(0, 0, 0, 100), "§r§0§8T4 §r§cAdvanced Boots"),
                new ForgeItem("advancedboots5", CustomItem::ADVANCEDBOOTS.":5", new Price(0, 0, 0, 160), "§r§0§8T5 §r§cAdvanced Boots"),
            ], "§8Boots"),
            new ForgeCategory("Tools", [
                new ForgeItem("basicaxe1", CustomItem::BASICAXE.":1", new Price(10), "§r§0§8T1 §r§bBasic Axe"),
                new ForgeItem("basicaxe2", CustomItem::BASICAXE.":2", new Price(50), "§r§0§8T2 §r§bBasic Axe"),
                new ForgeItem("basicaxe3", CustomItem::BASICAXE.":3", new Price(160), "§r§0§8T3 §r§bBasic Axe"),

                new ForgeItem("advancedaxe1", CustomItem::ADVANCEDAXE.":1", new Price(0, 0, 0, 10), "§r§0§8T1 §r§cAdvanced Axe"),
                new ForgeItem("advancedaxe2", CustomItem::ADVANCEDAXE.":2", new Price(0, 0, 0, 50), "§r§0§8T2 §r§cAdvanced Axe"),
                new ForgeItem("advancedaxe3", CustomItem::ADVANCEDAXE.":3", new Price(0, 0, 0, 160), "§r§0§8T3 §r§cAdvanced Axe"),

                new ForgeItem("basicshovel1", CustomItem::BASICSHOVEL.":1", new Price(10), "§r§0§8T1 §r§bBasic Shovel"),
                new ForgeItem("basicshovel2", CustomItem::BASICSHOVEL.":2", new Price(50), "§r§0§8T2 §r§bBasic Shovel"),
                new ForgeItem("basicshovel3", CustomItem::BASICSHOVEL.":3", new Price(160), "§r§0§8T3 §r§bBasic Shovel"),

                new ForgeItem("advancedshovel1", CustomItem::ADVANCEDSHOVEL.":1", new Price(0, 0, 0, 10), "§r§0§8T1 §r§cAdvanced Shovel"),
                new ForgeItem("advancedshovel2", CustomItem::ADVANCEDSHOVEL.":2", new Price(0, 0, 0, 50), "§r§0§8T2 §r§cAdvanced Shovel"),
                new ForgeItem("advancedshovel3", CustomItem::ADVANCEDSHOVEL.":3", new Price(0, 0, 0, 160), "§r§0§8T3 §r§cAdvanced Shovel"),
            ], "§8Tools"),
        ];

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