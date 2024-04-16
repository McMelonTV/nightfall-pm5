<?php

namespace AndreasHGK\Core\pvp;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\utils\FileUtils;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\World;

class PVPZoneManager {

    private static $instance;

    /**
     * @var array|PVPZone[]
     */
    private $pvpzones = [];

    public function canPvPHappen(?User $damager, ?User $target) : bool {
        if($damager === null) {
            return false;
        }

        if($target === null) {
            return false;
        }

        if($damager->getAdminMode()) {
            return true;
        }

        $location = $target->getPlayer()->getLocation();
        if($this->isSafe($location->x, $location->y, $location->z, $location->world)) {
            return false;
        }

        $player = $damager->getPlayer();
        if($player->getWorld()->getDisplayName() === Core::PVPMINEWORLD) {
            return true;
        }

        $damagerLocation = $player->getLocation();
        return $this->isPVPZone($damagerLocation->x, $damagerLocation->y, $damagerLocation->z, $damagerLocation->world)
            && $this->isPVPZone($location->x, $location->y, $location->z, $location->world);
    }

    public function isSafe($x, $y, $z, World $world = null) : bool {
        $zone = $this->getZoneAt($x, $y, $z, $world);
        return $zone !== null && $zone->isSafe();
    }

    public function canFlyAt($x, $y, $z, World $world = null) : bool {
        if($world !== null && $world->getDisplayName() === Core::PVPMINEWORLD){
            return false;
        }

        foreach($this->pvpzones as $pvpzone){
            if($pvpzone->isInZone($x, $y, $z, $world)) {
                return false;
            }
        }

        return true;
    }

    public function isPVPZone($x, $y, $z, World $world = null) : bool {
        if($world !== null && $world->getDisplayName() === Core::PVPMINEWORLD) {
            return true;
        }

        foreach($this->pvpzones as $pvpzone){
            if($pvpzone->isInZone($x, $y, $z, $world)) {
                return true;
            }
        }

        return false;
    }

    public function getZoneAt($x, $y, $z, World $world = null) : ?PVPZone {
        foreach($this->pvpzones as $pvpzone){
            if($pvpzone->isInZone($x, $y, $z, $world)) {
                return $pvpzone;
            }
        }

        return null;
    }

    /**
     * @return array|PVPZone[]
     */
    public function getAll() : array {
        return $this->pvpzones;
    }

    public function get(string $id) : ?PVPZone {
        return $this->pvpzones[$id] ?? null;
    }

    public function loadAll() : void {
        $zones = DataManager::getKey(FileUtils::MakeYAML("pvpzones"),  "pvpzones", []);
        foreach($zones as $id => $zone){
            $this->load($id);
        }
    }

    public function load(string $zoneId) : ?PVPZone {
        //if($file === "." || $file === "..") return null;
        $zoneData = DataManager::getKey(FileUtils::MakeYAML("pvpzones"), "pvpzones")[$zoneId];
        $id = $zoneData["id"] ?? $zoneId;
        $worldManager = Server::getInstance()->getWorldManager();
        $worldManager->loadWorld($zoneData["world"], true);
        $world = $worldManager->getWorldByName($zoneData["world"]);
        $safe = false;
        if(isset($zoneData["safe"])) {
            $safe = $zoneData["safe"];
        }

        $zone = new PVPZone($id, $world, new Vector3($zoneData["x1"], $zoneData["y1"], $zoneData["z1"]), new Vector3($zoneData["x2"], $zoneData["y2"], $zoneData["z2"]), $safe);
        $this->pvpzones[$id] = $zone;
        return $zone;
    }

    public function exist(string $tag) : bool {
        return isset($this->pvpzones[$tag]);
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}