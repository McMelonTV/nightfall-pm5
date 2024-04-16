<?php

namespace AndreasHGK\Core\mine;

use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\utils\FileUtils;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;

class MineManager {

    public const MINES_FOLDER = "mines".DIRECTORY_SEPARATOR;

    private static $instance;

    /**
     * @var array|Mine[]
     */
    private array $mines = [];

    private array $worlds = [];

    public function getAllNames() : array {
        $str = [];
        foreach($this->mines as $mine){
            $str[] = strtolower($mine->getName());
        }
        return $str;
    }

    public function isMineWorld(World $world) : bool{
        return isset($this->worlds[$world->getDisplayName()]);
    }

    public function getMineAt($x, $y, $z, World $world) : ?Mine {
        foreach($this->mines as $mine){
            if($mine->isInMine($x, $y, $z, $world)) {
                return $mine;
            }
        }

        return null;
    }

    public function regenAll() : void {
        $count = 0;
        foreach($this->getAll() as $mine){
            if($mine->isDisabled()) {
                continue;
            }

            $mine->regenerate();
            ++$count;
        }

        RegenerationObserver::getInstance()->setGlobalObserve(true);
        RegenerationObserver::getInstance()->setGlobalReset($count);

        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7All mines are now regenerating...");
    }

    public function getFromName(string $name) : ?Mine{
        foreach($this->mines as $m){
            if(strtolower($m->getName()) === strtolower($name)) {
                return $m;
            }
        }
        return null;
    }

    /**
     * @return array|Mine[]
     */
    public function getAll() : array {
        return $this->mines;
    }

    public function get(int $id) : ?Mine {
        return $this->mines[$id] ?? null;
    }

    public function loadAll() : void {
        $scan = DataManager::getFilesIn(self::MINES_FOLDER);
        foreach($scan as $filename){
            $this->load($filename);
        }
        foreach($this->mines as $mine){
            $displayName = $mine->getWorld()->getDisplayName();
            if(!isset($this->worlds[$displayName])){
                $this->worlds[$displayName][] = $mine->getId();
            }
        }
    }

    public function load(string $file) : ?Mine {
        //if($file === "." || $file === "..") return null;
        $file = DataManager::get(self::MINES_FOLDER.$file, false);
        $name = $file->get("name");
        $id = $file->get("id");

        $worldManager = Server::getInstance()->getWorldManager();
        $worldManager->loadWorld($file->get("world"), true);
        $world = $worldManager->getWorldByName($file->get("world"));

        $pos1 = new Vector3($file->get("x1"), $file->get("y1"), $file->get("z1"));
        $pos2 = new Vector3($file->get("x2"), $file->get("y2"), $file->get("z2"));

        $m = new Mine($id, $name, $world, $pos1, $pos2);
        $m->setPrices($file->get("prices", []));
        $m->setBlocks($file->get("blocks", []));
        $m->setDisabled($file->get("isDisabled", false));
        $m->setSpawnPosition(new Position($file->get("spawnX", null) ?? $file->get("x1"),
            $file->get("spawnY", null) ?? $file->get("y1"),
            $file->get("spawnZ", null) ?? $file->get("z1"),
            $m->getWorld()));
        $this->mines[$id] = $m;
        return $m;
    }

    public function saveAll() : void {
        foreach($this->mines as $mine){
            $this->save($mine);
        }
    }

    public function save(Mine $mine) : void {
        $file = DataManager::get(self::MINES_FOLDER.FileUtils::MakeYAML($mine->getName()), false);
        $file->set("name", $mine->getName());
        $file->set("id", $mine->getId());
        $file->set("world", $mine->getWorld());
        $file->set("x1", $mine->getPos1()->getX());
        $file->set("y1", $mine->getPos1()->getY());
        $file->set("z1", $mine->getPos1()->getZ());
        $file->set("x2", $mine->getPos2()->getX());
        $file->set("y2", $mine->getPos2()->getY());
        $file->set("z2", $mine->getPos2()->getZ());
        $file->set("prices", $mine->getPrices());
        $file->set("blocks", $mine->getBlocks());
        $file->set("isDisabled", $mine->isDisabled());
        $file->set("spawnX", $mine->getSpawnPosition()->getX());
        $file->set("spawnY", $mine->getSpawnPosition()->getY());
        $file->set("spawnZ", $mine->getSpawnPosition()->getZ());
    }

    public function exist(string $rank) : bool {
        return DataManager::exists(self::MINES_FOLDER.FileUtils::MakeYAML($rank));
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}