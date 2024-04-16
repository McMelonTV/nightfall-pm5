<?php

namespace AndreasHGK\Core\rank;

use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\utils\FileUtils;

class MineRankManager {

    public const RANKS_FOLDER = "mineranks".DIRECTORY_SEPARATOR;

    private static $instance;

    /**
     * @var array|MineRank[]
     */
    private $mineRanks = [];

    public function getAllNames() : array {
        $str = [];
        foreach($this->mineRanks as $rank){
            $str[] = $rank->getName();
        }

        return $str;
    }

    public function getFromName(string $name) : ?MineRank{
        foreach($this->mineRanks as $mr){
            if(strtolower($mr->getName()) === strtolower($name)) {
                return $mr;
            }
        }

        return null;
    }

    /**
     * @return array|MineRank[]
     */
    public function getAll() : array {
        return $this->mineRanks;
    }

    public function get(int $id) : ?MineRank {
        return $this->mineRanks[$id] ?? null;
    }

    public function loadAll() : void {
        $scan = DataManager::getFilesIn(self::RANKS_FOLDER);
        foreach($scan as $filename){
            $this->load($filename);
        }
    }

    public function load(string $file) : ?MineRank {
        //if($file === "." || $file === "..") return null;
        $file = DataManager::get(self::RANKS_FOLDER.$file, false);
        $name = $file->get("name");
        $id = $file->get("id");
        $tag = $file->get("tag");
        $price = (int)$file->get("price", 100);
        $mr = new MineRank($id, $name);
        $mr->setTag($tag);
        $mr->setPrice($price);
        $this->mineRanks[$id] = $mr;
        return $mr;
    }

    public function exist(string $rank) : bool {
        return DataManager::exists(self::RANKS_FOLDER.FileUtils::MakeYAML($rank));
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}