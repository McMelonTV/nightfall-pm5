<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use AndreasHGK\Core\Price;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;

abstract class CustomEnchant{

    public const RARITY_COMMON = 8;
    public const RARITY_UNCOMMON = 7;
    public const RARITY_RARE = 5;
    public const RARITY_VERY_RARE = 4;
    public const RARITY_MYTHIC = 2;
    public const RARITY_LEGENDARY = 1;

    public const TYPE_SWORD = "sword";
    public const TYPE_PICKAXE = "pickaxe";
    public const TYPE_AXE = "axe";
    public const TYPE_SHOVEL = "shovel";
    public const TYPE_BOW = "bow";
    public const TYPE_HELMET = "helmet";
    public const TYPE_CHESTPLATE = "chestplate";
    public const TYPE_LEGGINGS = "leggings";
    public const TYPE_BOOTS = "boots";

    public const GROUP_TOOLS = "tools";
    public const GROUP_ARMOR = "armor";
    public const GROUP_ALL = "all";

    abstract public function getCompatible() : array;

    abstract public function getName() : string;

    abstract public function getDescription() : string;

    abstract public function getId() : int;

    //abstract public function getCompatibleSlots() : int;

    abstract public function getRarity() : int;

    abstract public function getMaxLevel() : int;

    protected $level = 1;

    public function getApplyPrice() : Price {
        $price = new Price();

        switch ($this->getRarity()){
            case CustomEnchant::RARITY_COMMON:
                $xplevel = 3*$this->getLevel();
                break;
            case CustomEnchant::RARITY_UNCOMMON:
                $xplevel = 6*$this->getLevel();
                break;
            case CustomEnchant::RARITY_RARE:
                $xplevel = 8*$this->getLevel();
                break;
            case CustomEnchant::RARITY_VERY_RARE:
                $xplevel = 12*$this->getLevel();
                break;
            case CustomEnchant::RARITY_MYTHIC:
                $xplevel = 15*$this->getLevel();
                break;
            case CustomEnchant::RARITY_LEGENDARY:
                $xplevel = 20*$this->getLevel();
                break;
            default:
                break;
        }

        $price->setXPLevels($xplevel);
        return $price;
    }

    public function getLevel() : int {
        return $this->level;
    }

    public function setLevel(int $level) : void {
        $this->level = $level;
    }

    //events

    public function onMine(CEMineEvent $ev) : void{}

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{}

    public function onHit2(CEAttackEvent $ev) : void{}

    public function onGetDamage(EntityDamageEvent $ev) : void{}

    public function onKill(EntityDamageEvent $ev, Player $attacker, Item $item, bool $isAttacker) : void{}
}