<?php

declare(strict_types=1);

namespace AndreasHGK\Core;

use AndreasHGK\Core\enchant\CustomEnchant;
use AndreasHGK\Core\enchant\CustomEnchantIds;
use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\IntUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;

class ItemInterface{

    public static function fromItem(Item $item) : self {
        $interface = new self($item);
        $interface->recalculateStats();
        return $interface;
    }

    /**
     * @var Item
     */
    protected $item;

    /**
     * @var null|CustomItem
     */
    protected $customItem = null;

    /**
     * @var array|CustomEnchant[]
     */
    protected $customEnchants = [];

    /**
     * @var int
     */
    //protected $masteryXP, $mastery, $maxMastery;

    /**
     * @var int
     */
    protected $maxDamage = -1, $nfDamage = -1;

    protected $type = 0;

    protected $description = "";

    protected $quality = -100;

    protected $signed = "";

    public function getCustomItem() : ?CustomItem {
        return $this->customItem;
    }

    public function isCustomItem() : bool {
        return $this->customItem !== null;
    }

    public function isSigned() : bool {
        return $this->signed !== "";
    }

    public function getSigner() : string {
        return $this->signed;
    }

    public function setSigner(string $sign) : void {
        $this->signed = $sign;
    }

    public function getQuality() : int {
        return $this->quality;
    }

    public function setQuality(int $quality) : void {
        $this->quality = $quality;
    }

    public function hasQuality() : bool {
        return $this->quality !== -100;
    }

    public function getDescription() : string {
        return $this->description;
    }

    public function setDescription(string $description) : void {
        $this->description = $description;
    }

    public function hasDescription() : bool {
        return $this->description !== "";
    }

    public function getItem() : Item {
        return $this->item;
    }

    public function setDamage(int $damage) : void {
        $this->nfDamage = $damage;
    }

    public function getDamage() : int {
        return $this->nfDamage === -1 ? $this->item->getMeta() : $this->nfDamage;
    }

    public function applyDamage(int $damage) : void {
        $this->nfDamage += $damage;
    }

    public function setMaxDamage(int $maxDamage) : void {
        $this->maxDamage = $maxDamage;
    }

    public function getMaxDamage() : int {
        $item = $this->item;
        if($item instanceof Durable){
            return $this->maxDamage === -1 ? $item->getMaxDurability() : $this->maxDamage;
        }else{
            return -1;
        }
    }

    /**
     * @return array|CustomEnchant[]
     */
    public function getCustomEnchants() : array {
        return $this->customEnchants;
    }

    public function setCustomEnchants(array $enchants) : void {
        $this->customEnchants = $enchants;
    }

    public function enchant(CustomEnchant $enchant) : void {
        $this->customEnchants[$enchant->getId()] = $enchant;
    }

    public function hasEnchantment(CustomEnchant $enchant) : bool {
        return isset($this->customEnchants[$enchant->getId()]);
    }

    public function isDurable() : bool {
        return $this->item instanceof Durable;
    }

    public function doUseDurability() : bool {
        if($this->hasEnchantment(CustomEnchantsManager::getInstance()->get(CustomEnchantIds::UNBREAKING))){
            $e = $this->customEnchants[CustomEnchantIds::UNBREAKING];
            return mt_rand(0, 100) < $e->getLevel()*10;
        }

        return true;
    }

    public function saveStats() : void {
        $nbt = $this->item->getNamedTag();
/*        $nbt->setInt("masteryXP", $this->masteryXP);
        $nbt->setInt("mastery", $this->mastery);
        $nbt->setInt("maxMastery", $this->maxMastery);*/
        if($this->isDurable()){
            $nbt->setInt("maxDamage", $this->maxDamage);
            $nbt->setInt("nfDamage", $this->nfDamage);
        }

        $nbt->setInt("type", $this->type);
        $nbt->setString("description", $this->description);
        $nbt->setInt("quality", $this->quality);

        if($this->isSigned()){
            $nbt->setString("signed", $this->getSigner());
        }

        if(!empty($this->customEnchants)){
            $array = [];
            foreach($this->getCustomEnchants() as $customEnchant){
                $c = new CompoundTag();
                $c->setShort("id", $customEnchant->getId());
                $c->setShort("lvl", $customEnchant->getLevel());
                $array[] = $c;
            }

            $tag = new ListTag($array, NBT::TAG_Compound);
            $nbt->setTag("customenchants", $tag);
        }else{
            $nbt->removeTag("customenchants");
        }
    }

    public function recalculateDamage() : void {
        if($this->item instanceof Durable){
            $item = $this->item;

            $maxVanillaDamage = $item->getMaxDurability();
            $maxDamage = $this->getMaxDamage() !== -1 ? $this->getMaxDamage() : $maxVanillaDamage;
            $damage = $this->getDamage() !== -1 ? $this->getDamage() : $item->getDamage();

            if($damage === $item->getDamage()) {
                return;
            }

            $damage = $damage / $maxDamage;
            $fakeDamage = $maxVanillaDamage * $damage;
            $item->setDamage((int) min(max((int) $fakeDamage, 0), $maxVanillaDamage - 10));
        }
    }

    public function recalculateStats() : void {
        $nbt = $this->item->getNamedTag();
/*        $this->mastery = $nbt->getInt("mastery", 0);
        $this->masteryXP = $nbt->getInt("masteryXP", 0);
        $this->maxMastery = $nbt->getInt("maxMastery", 0);*/
        if($this->isDurable()){
            $this->maxDamage = $nbt->getInt("maxDamage", -1);
            $this->nfDamage = $nbt->getInt("nfDamage", -1);
        }

        if($nbt->getTag("customitem") !== null){
            $this->customItem = CustomItemManager::getInstance()->get($nbt->getInt("customitem", 0));
        }

        $this->description = $nbt->getString("description", "");
        $this->signed = $nbt->getString("signed", "");
        $this->quality = $nbt->getInt("quality", -100);

        $this->type = $nbt->getInt("type", 0);
        if($nbt->getTag("customenchants") !== null){
            $array = [];
            $customEnchantsManager = CustomEnchantsManager::getInstance();
            foreach($nbt->getListTag("customenchants") as $entry){
                /** @var $entry CompoundTag */
                $id = $entry->getShort("id");

                $ench = $customEnchantsManager->get($id);
                if($ench === null) {
                    continue;
                }

                $ench->setLevel($entry->getShort("lvl"));
                $array[$id] = $ench;
            }

            $this->setCustomEnchants($array);
        }
    }

    //make sure to recalculate stats before calling this
    public function recalculateLore() : void {
        $item = $this->item;
        $item->setLore([]);
        $lore = [];
        if($this->isSigned()){
            $lore[] = "§r§7Signed by: §b".$this->getSigner();
        }

        if($this->hasDescription()){
            $lore[] = $this->getDescription();
        }

        if($this->hasQuality()){
            $lore[] = "§r§7Quality: ".ItemUtils::qualityName($this->getQuality())."§r§8 (".($this->quality >= 0 ? "+" : "").$this->getQuality()."§8%)";
        }

        if($this->getMaxDamage() !== -1){
            $lore[] = "§r§7Durability: §b".($this->getMaxDamage()-$this->getDamage())."§8/§b".$this->getMaxDamage();
        }

        if(!empty($this->customEnchants)){
            $lore[] = "";
        }

        foreach($this->customEnchants as $customEnchant){
            $lore[] = "§r".EnchantmentUtils::rarityColor($customEnchant->getRarity()).$customEnchant->getName().($customEnchant->getMaxLevel() > 1 ? " ".IntUtils::toRomanNumerals($customEnchant->getLevel()) : "");
        }

        $item->setLore($lore);
    }

    private function __construct(Item $item){
        $this->item = $item;
    }
}