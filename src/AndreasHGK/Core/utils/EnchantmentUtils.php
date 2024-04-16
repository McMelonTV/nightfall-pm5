<?php

declare(strict_types=1);

namespace AndreasHGK\Core\utils;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\enchant\CustomEnchant;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

final class EnchantmentUtils{

    private function __construct(){
        //NOOP
    }

    public static function applyGlow(Item &$item) : Item {
        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(Core::GLOW_ID), 1));
        return $item;
    }

    public static function applyProt(Item &$item) : Item {
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
        return $item;
    }

    public static function applyGlowLevel(Item &$item, int $level) : Item {
        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(Core::GLOW_ID), $level));
        return $item;
    }

    public static function rarityColor(int $rarity) : string {
        switch ($rarity){
            case CustomEnchant::RARITY_COMMON:
                $str = "§7";
                break;
            case CustomEnchant::RARITY_UNCOMMON:
                $str = "§a";
                break;
            case CustomEnchant::RARITY_RARE:
                $str = "§c";
                break;
            case CustomEnchant::RARITY_VERY_RARE:
                $str = "§4";
                break;
            case CustomEnchant::RARITY_MYTHIC:
                $str = "§5";
                break;
            case CustomEnchant::RARITY_LEGENDARY:
                $str = "§l§6";
                break;
            default:
                $str = "§7";
        }
        return $str;
    }

    public static function rarityName(int $rarity) : string {
        switch ($rarity){
            case CustomEnchant::RARITY_COMMON:
                $str = "Common";
                break;
            case CustomEnchant::RARITY_UNCOMMON:
                $str = "Uncommon";
                break;
            case CustomEnchant::RARITY_RARE:
                $str = "Rare";
                break;
            case CustomEnchant::RARITY_VERY_RARE:
                $str = "Very Rare";
                break;
            case CustomEnchant::RARITY_MYTHIC:
                $str = "Mythic";
                break;
            case CustomEnchant::RARITY_LEGENDARY:
                $str = "Legendary";
                break;
            default:
                $str = "Common";
        }
        return $str;
    }

    public static function lightning(Entity $player) : void {
        $strike = new AddActorPacket();
        $strike->type = "minecraft:lightning_bolt";
        $strike->entityRuntimeId = mt_rand(99999, 9999999);
        $strike->metadata = [];
        $strike->motion = null;
        $strike->position = $player->getPosition();
        $player->getWorld()->broadcastPacketToViewers($player->getPosition(), $strike);
        $sound = LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_THUNDER, $player->getPosition());
        $sound->extraData = 1;
        $sound->entityType = "minecraft:lightning_bolt";
        $player->getWorld()->broadcastPacketToViewers($player->getPosition(), $sound);
    }

}