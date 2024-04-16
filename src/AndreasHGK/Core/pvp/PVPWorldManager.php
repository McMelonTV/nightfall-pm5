<?php

namespace AndreasHGK\Core\pvp;

use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\utils\FileUtils;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\World;

class PVPWorldManager {

    private static $instance;

    /**
     * @var array|string[]
     */
    private $pvpWorlds = [];

    public function canPvPHappen(User $damager, User $target) : bool {
        if($damager->getAdminMode()) {
            return true;
        }

        $damager->getPlayer()->getWorld();
        return false;
    }

    public function isPVPWorld(World $world) : bool {
        foreach($this->pvpWorlds as $pvpzone){
            if($world === $pvpzone) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array|string[]
     */
    public function getAll() : array {
        return $this->pvpWorlds;
    }

    public function get(string $id) : ?string {
        return $this->pvpWorlds[$id] ?? null;
    }

    public function loadAll() : void {
        $zones = DataManager::getKey(FileUtils::MakeYAML("pvpworlds"),  "pvpworlds", []);
        foreach($zones as $id => $zone){
            $this->load($id);
        }
    }

    public function load(string $zoneId) : ?string {
        //if($file === "." || $file === "..") return null;
        $zoneData = DataManager::getKey(FileUtils::MakeYAML("pvpworlds"), "pvpworlds")[$zoneId];
        $id = $zoneData["id"] ?? $zoneId;
        $worldManager = Server::getInstance()->getWorldManager();
        $worldManager->loadWorld($zoneData["world"]);
        $world = $worldManager->getWorldByName($zoneData["world"]);
        $zone = new PVPZone($id, $world, new Vector3($zoneData["x1"], $zoneData["y1"], $zoneData["z1"]), new Vector3($zoneData["x2"], $zoneData["y2"], $zoneData["z2"]));
        $this->pvpWorlds[$id] = $zone;
        return $zone;
    }

    public function exist(string $tag) : bool {
        return isset($this->pvpWorlds[$tag]);
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}