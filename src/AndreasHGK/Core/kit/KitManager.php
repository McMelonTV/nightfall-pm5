<?php

namespace AndreasHGK\Core\kit;

use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;

class KitManager {

    private static $instance;

    /**
     * @var array|Kit[]
     */
    private $kits = [];

    /**
     * @return array|Kit[]
     */
    public function getAll() : array {
        return $this->kits;
    }

    public function get(int $id) : ?Kit {
        return $this->kits[$id] ?? null;
    }

    public function add(Kit $kit) : void {
        $this->kits[$kit->getId()] = $kit;
        PermissionManager::getInstance()->addPermission(new Permission($kit->getPermission(), ""));
    }

    public function exist(string $kit) : bool {
        return isset($this->kits[$kit]);
    }

    public function registerDefaults() : void {
        $this->add(new Kit(10, "Starter", "nightfall.kit.starter", 3600, [ "13:1", "6:1" ], [ Kit::SLOT_BOOTS => "12:1", Kit::SLOT_LEGGINGS => "11:1", Kit::SLOT_CHESTPLATE => "10:1", Kit::SLOT_HELMET => "9:1" ]));
        $this->add(new Kit(20, "Mercenary", "nightfall.kit.mercenary", 21600, [ "13:2", "6:2" ], [ Kit::SLOT_BOOTS => "12:2", Kit::SLOT_LEGGINGS => "11:2", Kit::SLOT_CHESTPLATE => "10:2", Kit::SLOT_HELMET => "9:2" ]));
        $this->add(new Kit(30, "Warrior", "nightfall.kit.warrior", 21600, [ "13:2", "6:3" ], [ Kit::SLOT_BOOTS => "12:2", Kit::SLOT_LEGGINGS => "11:2", Kit::SLOT_CHESTPLATE => "10:2", Kit::SLOT_HELMET => "9:2" ]));
        $this->add(new Kit(40, "Knight", "nightfall.kit.knight", 21600, [ "13:3", "6:4" ], [ Kit::SLOT_BOOTS => "12:3", Kit::SLOT_LEGGINGS => "11:3", Kit::SLOT_CHESTPLATE => "10:3", Kit::SLOT_HELMET => "9:3" ]));
        $this->add(new Kit(50, "Lord", "nightfall.kit.lord", 21600, [ "13:3", "6:5" ], [ Kit::SLOT_BOOTS => "12:3", Kit::SLOT_LEGGINGS => "11:3", Kit::SLOT_CHESTPLATE => "10:3", Kit::SLOT_HELMET => "9:3" ]));
        $this->add(new Kit(60, "Titan", "nightfall.kit.titan", 21600, [ "13:4", "6:5" ], [ Kit::SLOT_BOOTS => "12:4", Kit::SLOT_LEGGINGS => "11:4", Kit::SLOT_CHESTPLATE => "10:4", Kit::SLOT_HELMET => "9:4" ]));
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}