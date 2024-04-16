<?php

namespace AndreasHGK\Core\holotext;

use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\utils\FileUtils;
use pocketmine\entity\Location;
use pocketmine\Server;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;

class HolotextManager {

    private static $instance;

    /**
     * @var array|Holotext[]
     */
    private $holotexts = [];

    /**
     * @return array|Holotext[]
     */
    public function getAll() : array {
        return $this->holotexts;
    }

    public function get(string $id) : ?Holotext {
        return $this->holotexts[$id] ?? null;
    }

    public function loadAll() : void {
        $texts = DataManager::getKey(FileUtils::MakeYAML("holotext"),  "holotext", []);
        foreach($texts as $id => $text){
            $this->load($id);
        }
    }

    public function getNextId() : string {
        return (string) (count($this->holotexts)+1);
    }

    public function add(Holotext $text) : void {
        $this->holotexts[$text->hologramId] = $text;
    }

    public function load(string $textId) : ?Holotext {
        $textData = DataManager::getKey(FileUtils::MakeYAML("holotext"), "holotext")[$textId];
        $worldManager = Server::getInstance()->getWorldManager();
        $worldManager->loadWorld($textData["world"]);
        $world = $worldManager->getWorldByName($textData["world"]);
        $holotext = new Holotext($textId, new Location($textData["x"], $textData["y"], $textData["z"], 0, 0, $world), $textData["text"], $textData["title"] ?? "");

        $holotext->spawnToAll();

        $this->holotexts[$textId] = $holotext;
        return $holotext;
    }

    public function exist(string $text) : bool {
        return isset($this->holotexts[$text]);
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}