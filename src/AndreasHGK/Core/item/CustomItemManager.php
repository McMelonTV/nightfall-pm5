<?php

namespace AndreasHGK\Core\item;

class CustomItemManager {

    private static $instance;

    /**
     * @var array|CustomItem[]
     */
    private array $customItems = [];

    public function getFromName(string $name) : ?CustomItem{
        foreach($this->customItems as $customItem){
            if(strtolower($customItem->getName()) === strtolower($name)) {
                return clone $customItem;
            }
        }

        return null;
    }

    /**
     * @return array|CustomItem[]
     */
    public function getAll() : array {
        return $this->customItems;
    }

    public function get(int $id) : ?CustomItem {
        return isset($this->customItems[$id]) ? (clone $this->customItems[$id]) : null;
    }

    public function exist(int $id) : bool {
        return isset($this->customItems[$id]);
    }

    public function register(CustomItem $customItem) : void {
        $this->customItems[$customItem->getId()] = $customItem;
    }

    public function registerDefaults() : void {
        $this->register(new Stardust());
        $this->register(new MagicDust());
        $this->register(new ObsidianShard());
        $this->register(new Steeldust());
        $this->register(new TestPickaxe());
        $this->register(new BasicPickaxe());
        $this->register(new AdvancedPickaxe());
        $this->register(new EnchantmentBook());
        $this->register(new BasicBoots());
        $this->register(new BasicLeggings());
        $this->register(new BasicHelmet());
        $this->register(new BasicChestplate());
        $this->register(new BasicSword());
        $this->register(new GuideBookItem());
        $this->register(new AdvancedSword());
        $this->register(new AdvancedBoots());
        $this->register(new AdvancedLeggings());
        $this->register(new AdvancedChestplate());
        $this->register(new AdvancedHelmet());
        $this->register(new HealApple());
        $this->register(new EnchantedHealApple());
        $this->register(new BasicAxe());
        $this->register(new AdvancedAxe());
        $this->register(new BasicShovel());
        $this->register(new AdvancedShovel());
        $this->register(new ElitePickaxe());
        $this->register(new KothChestplate());
        $this->register(new KothPickaxe());
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}