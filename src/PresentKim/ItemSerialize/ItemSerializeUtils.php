<?php

/**
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 *
 * @author       PresentKim (debe3721@gmail.com)
 * @link         https://github.com/PresentKim
 * @license      https://opensource.org/licenses/MIT MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace PresentKim\ItemSerialize;

use PresentKim\ItemSerialize\NbtSerializer;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\data\bedrock\item\SavedItemStackData;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;

use function get_class;
use function json_decode;
use function json_encode;

final class ItemSerializeUtils{

    /**
     * Serialize the item to the binary contents
     *
     * @param Item $item
     *
     * @return string
     */
    public static function binarySerialize(Item $item) : string{
        return self::encodeToUTF8(NbtSerializer::toBinary($item->nbtSerialize()));
    }

    /**
     * Deserialize the item from the binary contents
     *
     * @param string $contents
     *
     * @return Item
     */
    public static function binaryDeserialize(string $contents) : Item{
        return self::deserializeItemTag(NbtSerializer::fromBinary($contents));
    }

    /**
     * Serialize the item to the base64 contents
     *
     * @param Item $item
     *
     * @return string
     */
    public static function base64Serialize(Item $item) : string{
        return self::encodeToUTF8(NbtSerializer::toBase64($item->nbtSerialize()));
    }

    /**
     * Deserialize the item from the base64 contents
     *
     * @param string $contents
     *
     * @return Item
     */
    public static function base64Deserialize(string $contents) : Item{
        return self::deserializeItemTag(NbtSerializer::fromBase64($contents));
    }

    /**
     * Serialize the item to the hex string contents
     *
     * @param Item $item
     *
     * @return string
     */
    public static function hexSerialize(Item $item) : string{
        return self::encodeToUTF8(NbtSerializer::toHex($item->nbtSerialize()));
    }


    /**
     * Deserialize the item from the hex string contents
     *
     * @param string $contents
     *
     * @return Item
     */
    public static function hexDeserialize(string $contents) : Item{
        return self::deserializeItemTag(NbtSerializer::fromHex($contents));
    }

    /**
     * Serialize the item to the SNBT contents
     *
     * @param Item $item
     *
     * @return string
     */
    public static function snbtSerialize(Item $item) : string{
        return self::encodeToUTF8(NbtSerializer::toSNBT($item->nbtSerialize()));
    }

    /**
     * Deserialize the item from the SNBT contents
     *
     * @param string $contents
     *
     * @return Item
     */
    public static function snbtDeserialize(string $contents) : Item{
        return self::deserializeItemTag(NbtSerializer::fromSNBT($contents));
    }

    /**
     * Serialize the item to the JSON contents
     *  It's not same as {@link Item::legacyJsonDeserialize()}
     *
     * @param Item $item
     *
     * @return string
     */
    public static function jsonSerialize(Item $item) : string{
        $tag = $item->nbtSerialize();
        $json = [
            SavedItemStackData::TAG_COUNT => $tag->getByte(SavedItemStackData::TAG_COUNT),
            SavedItemData::TAG_NAME => $tag->getString(SavedItemData::TAG_NAME),
            SavedItemData::TAG_DAMAGE => $tag->getShort(SavedItemData::TAG_DAMAGE)
        ];

        $block = $tag->getCompoundTag(SavedItemData::TAG_BLOCK);
        if($block !== null){
            $json[SavedItemData::TAG_BLOCK] = NbtSerializer::toSNBT($block);
        }

        $namedTag = $tag->getCompoundTag(SavedItemData::TAG_TAG);
        if($namedTag !== null){
            $json[SavedItemData::TAG_TAG] = NbtSerializer::toSNBT($namedTag);
        }
        return self::encodeToUTF8(json_encode($json));
    }

    /**
     * Deserialize the item from the JSON contents
     *   It's not same as {@link Item::legacyJsonDeserialize()}
     *
     * @param string $contents
     *
     * @return Item
     */
    public static function jsonDeserialize(string $contents) : Item{
        $json = json_decode($contents, true);

        $tag = new CompoundTag();
        $tag->setByte(SavedItemStackData::TAG_COUNT, $json[SavedItemStackData::TAG_COUNT]);
        $tag->setString(SavedItemData::TAG_NAME, $json[SavedItemData::TAG_NAME]);
        $tag->setShort(SavedItemData::TAG_DAMAGE, $json[SavedItemData::TAG_DAMAGE]);

        if(isset($json[SavedItemData::TAG_BLOCK])){
            $tag->setTag(SavedItemData::TAG_BLOCK, NbtSerializer::fromSNBT($json[SavedItemData::TAG_BLOCK]));
        }
        if(isset($json[SavedItemData::TAG_TAG])){
            $tag->setTag(SavedItemData::TAG_TAG, NbtSerializer::fromSNBT($json[SavedItemData::TAG_TAG]));
        }

        return self::deserializeItemTag($tag);
    }

    /** Convert the contents to UTF-8 encoding */
    private static function encodeToUTF8(string $contents) : string{
        return mb_convert_encoding($contents, "UTF-8", mb_detect_encoding($contents));
    }


    /** Deserialize the item from the compound tag */
    private static function deserializeItemTag(Tag $tag) : Item{
        if(!($tag instanceof CompoundTag)){
            throw new \InvalidArgumentException("Invalid tag type : " . get_class($tag));
        }
        return Item::nbtDeserialize($tag);
    }
}
