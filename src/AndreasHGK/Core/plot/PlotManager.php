<?php

namespace AndreasHGK\Core\plot;

use AndreasHGK\Core\generator\PlotGenerator;
use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\user\OfflineUser;
use AndreasHGK\Core\utils\FileUtils;
use pocketmine\Server;
use pocketmine\world\World;
use function ceil;
use function floor;

class PlotManager {

    public static $plotworld = "plots";

    private static $instance;

    /**
     * @var array|Plot[]
     */
    private $plots = [];

    public function getWorld() : World {
        return Server::getInstance()->getWorldManager()->getWorldByName(self::$plotworld);
    }

    public function claim(int $plotX, int $plotZ, OfflineUser $claimer) : void {
        $plot = $this->get($plotX, $plotZ);
        $plot->setOwner($claimer->getName());
        $this->plots[$plot->getId()] = $plot;
    }

    public function unclaim(int $plotX, int $plotZ) : void {
        $plot = $this->get($plotX, $plotZ);
        $plot->setOwner("");
        $plot->clear();
        unset($this->plots[$plot->getId()]);
    }

    public function setAll(array $plots) : void {
        $this->plots = $plots;
    }

    public function isClaimed(int $x, int $z) : bool {
        $plot = $this->getPlotAt($x, $z);
        return $plot !== null && $plot->isClaimed();
    }

    /**
     * @return array|Plot[]
     */
    public function getAll() : array {
        return $this->plots;
    }

    public function getPlotAt(int $x, int $z) : ?Plot{
        if(PlotGenerator::getTypeAt($x, $z, Plot::PLOT_SIZE) !== PlotGenerator::PLOT){
            return null;
        }

        if($x < 0){
            $plotX = (int)floor(($x-4)/(Plot::PLOT_SIZE+Plot::ROAD_SIZE))+1;
        }else{
            $plotX = (int)ceil(($x-(Plot::PLOT_SIZE+Plot::ROAD_SIZE)+3)/(Plot::PLOT_SIZE+Plot::ROAD_SIZE))+1;
        }

        if($z < 0){
            $plotZ = (int)floor(($z-4)/(Plot::PLOT_SIZE+Plot::ROAD_SIZE))+1;
        }else{
            $plotZ = (int)ceil(($z-(Plot::PLOT_SIZE+Plot::ROAD_SIZE)+3)/(Plot::PLOT_SIZE+Plot::ROAD_SIZE))+1;
        }

        return $this->get($plotX, $plotZ);
    }

    public function getUnclaimedPlot() : ?Plot{
        $X = $Y = 1000;
        $x = $y = $dx = 0;
        $dy = -1;
        $t = max($X, $Y);
        $maxI = $t * $t;
        for($i = 0; $i < $maxI; ++$i){
            if((-$X / 2 <= $x) and ($x <= $X / 2) and (-$Y / 2 <= $y) and ($y <= $Y / 2)){
                $plot = $this->get($x, $y);
                if(!$plot->isClaimed()){
                    return $plot;
                }
            }

            if(($x === $y) or (($x < 0) and ($x === -$y)) or (($x > 0) and ($x === 1 - $y))){
                $t = $dx;
                $dx = -$dy;
                $dy = $t;
            }

            $x += $dx;
            $y += $dy;
        }

        return null;
    }

    public function get(int $plotX, int $plotZ) : Plot {
        return $this->plots[$plotX.":".$plotZ] ?? new Plot($plotX, $plotZ);
    }

    public function getById(string $id) : ?Plot {
        $expl = explode(":", $id);
        return $this->plots[$id] ?? new Plot($expl[0], $expl[1]);
    }

    public function loadAll() : void {
        $plots = DataManager::getKey(FileUtils::MakeJSON("plots"),  "plots", []);
        foreach($plots as $id => $plot){
            $this->load($id);
        }
    }

    public function load(string $plotId) : ?Plot {
        $tagData = DataManager::getKey(FileUtils::MakeJSON("plots"), "plots")[$plotId] ?? false;
        if($tagData === false) {
            return null;
        }

        $expl = explode(":", $plotId);
        $x = $expl[0];
        $z = $expl[1];

        $plot = new Plot($x, $z, $tagData["name"] ?? "", $tagData["owner"] ?? "", $tagData["members"] ?? [], $tagData["blockedUsers"] ?? []);
        $this->plots[$plot->getId()] = $plot;
        return $plot;
    }

    public function saveAll() : void {
        $plots = [];
        foreach($this->getAll() as $plot){
            $data = [];
            $data["owner"] = $plot->getOwner();
            $data["members"] = $plot->getMembers() ?? [];
            $data["blockedUsers"] = $plot->getBlockedUsers();
            $data["name"] = $plot->getName() ?? "";
            $plots[$plot->getId()] = $data;
        }

        $file = DataManager::get(FileUtils::MakeJSON("plots"));
        $file->set("plots", $plots);
        $file->save();
    }

    public function exist(string $tag) : bool {
        return isset($this->plots[$tag]);
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}