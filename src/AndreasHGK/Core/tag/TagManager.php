<?php

namespace AndreasHGK\Core\tag;

use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\utils\FileUtils;

class TagManager {

    private static $instance;

    /**
     * @var array|Tag[]
     */
    private $tags = [];

    public function getAllNames() : array {
        $str = [];
        foreach($this->tags as $tag){
            $str[] = strtolower($tag->getTag());
        }

        return $str;
    }

    /**
     * @return array|Tag[]
     */
    public function getAll() : array {
        return $this->tags;
    }

    public function get(string $id) : ?Tag {
        return $this->tags[$id] ?? null;
    }

    public function loadAll() : void {
        $tags = DataManager::getKey(FileUtils::MakeYAML("tags"),  "tags", []);
        foreach($tags as $id => $tag){
            $this->load($id);
        }
    }

    public function load(string $tagId) : ?Tag {
        //if($file === "." || $file === "..") return null;
        $tagData = DataManager::getKey(FileUtils::MakeYAML("tags"), "tags")[$tagId];
        $id = $tagData["id"] ?? $tagId;
        $tag = new Tag($id, $tagData["tag"] ?? $id, $tagData["rarity"] ?? Tag::COMMON);
        $tag->setPublic($tagData["isPublic"] ?? false);
        $tag->setCrateDrop($tagData["isCrateDrop"] ?? true);
        $tag->setReceiveOnJoin($tagData["recieveOnJoin"] ?? false);
        $this->tags[$id] = $tag;
        return $tag;
    }

    public function exist(string $tag) : bool {
        return isset($this->tags[$tag]);
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}