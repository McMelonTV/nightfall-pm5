<?php

declare(strict_types=1);

namespace AndreasHGK\Core\utils;

use AndreasHGK\Core\tag\Tag;
use AndreasHGK\Core\tag\TagManager;

final class TagUtils{

    private function __construct(){
        //NOOP
    }

    public static function rarityColor(int $rarity) : string {
        switch ($rarity){
            case Tag::COMMON:
                return "§7";
            case Tag::UNCOMMON:
                return "§a";
            case Tag::RARE:
                return "§9";
            case Tag::VERY_RARE:
                return "§5";
            case Tag::LEGENDARY:
                return "§b";
            default:
                return "§7";
        }

    }

    public static function rarityName(int $rarity) : string {
        switch ($rarity){
            case Tag::COMMON:
                return "common";
            case Tag::UNCOMMON:
                return "uncommon";
            case Tag::RARE:
                return "rare";
            case Tag::VERY_RARE:
                return "very rare";
            case Tag::LEGENDARY:
                return "legendary";
            default:
                return "common";
        }

    }

    /**
     * @param array|Tag[] $tags
     * @return array|string[]
     */
    public static function tagsToArray(array $tags) : array {
        $newArray = [];
        foreach($tags as $tag){
            $newArray[$tag->getId()] = $tag->getTag();
        }
        return $newArray;
    }

    public static function arrayToTags(array $strings) : array {
        $tags = [];
        foreach($strings as $id => $string){
            $tag = TagManager::getInstance()->get($id);
            if($tag === null) continue;
            $tags[$id] = $tag;
        }
        return $tags;
    }

}