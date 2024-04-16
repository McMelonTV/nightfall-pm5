<?php

declare(strict_types=1);

namespace AndreasHGK\Core\kit;

use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\item\TieredItem;
use AndreasHGK\Core\item\VariantItem;
use AndreasHGK\Core\user\User;
use pocketmine\item\Item;
use pocketmine\player\Player;

class Kit {

    public const SLOT_BOOTS = "boots";
    public const SLOT_LEGGINGS = "leggings";
    public const SLOT_CHESTPLATE = "chestplate";
    public const SLOT_HELMET = "helmet";

    public static function returnItem($data) : Item {
        if(is_string($data)){
            $expl = explode(":", $data);
            $id = $expl[0];
            $cItem = CustomItemManager::getInstance()->get((int)$id);
            $item = $cItem->getItem();
            if(isset($expl[1])){
                if($cItem instanceof TieredItem){
                    $item = $cItem->getTier((int)$expl[1]);
                }elseif($cItem instanceof VariantItem){
                    $item = $cItem->getVariant((int)$expl[1]);
                }
            }
        }elseif($data instanceof Item){
            $item = $data;
        }

        return $item;
    }

    public static function toSlotNumber(string $armorSlot) : int {
        switch ($armorSlot){
            case self::SLOT_HELMET:
                return 0;
            case self::SLOT_CHESTPLATE:
                return 1;
            case self::SLOT_LEGGINGS:
                return 2;
            case self::SLOT_BOOTS:
                return 3;
        }
        return -1;
    }

    private $name;

    private $id;

    /** @var Item[]|string[] */
    private $contents = [];

    /** @var Item[]|string[] */
    private $armor;

    private $permission;

    private $cooldown;

    public function claim(User $user) : void {
        $player = $user->getPlayer();
        $items = [];
        foreach($this->contents as $content){
            $items[] = self::returnItem($content);
        }

        $armor = [];
        foreach($this->armor as $slotName => $content){
            $slot = self::toSlotNumber($slotName);
            if($slot === -1){
                $items[] = self::returnItem($content);
                continue;
            }

            if(!$player->getArmorInventory()->isSlotEmpty($slot)){
                $items[] = self::returnItem($content);
                continue;
            }
            $armor[$slot] = self::returnItem($content);
        }

        $player->getInventory()->addItem(...$items);
        foreach($armor as $slot => $armorPiece){
            $player->getArmorInventory()->setItem($slot, $armorPiece);
        }

        $cds = $user->getKitCooldowns();
        $cds[$this->id] = time();
        $user->setKitCooldowns($cds);
    }

    public function canAdd(Player $player) : bool {
        return $player->getInventory()->getSize() - $this->getActualItemCount($player) >= 0;
    }

    public function getActualItemCount(Player $player) : int {
        $items = [];
        foreach($this->contents as $content){
            $items[] = self::returnItem($content);
        }
        foreach($this->armor as $slotName => $content){
            $slot = self::toSlotNumber($slotName);
            if($slot === -1){
                $items[] = $content;
                continue;
            }

            if(!$player->getArmorInventory()->isSlotEmpty($slot)){
                $items[] = $content;
                continue;
            }
        }
        return count($items);
    }

    public function isOnCooldown(User $user) : bool {
        return isset($user->getKitCooldowns()[$this->getId()]) && $user->getKitCooldowns()[$this->getId()] + $this->getCooldown() > time();
    }

    public function getCooldownTime(User $user) : int {
        if(!isset($user->getKitCooldowns()[$this->getId()])) return 0;
        return $user->getKitCooldowns()[$this->getId()] + $this->getCooldown() - time();
    }

    public function getCooldown() : int {
        return $this->cooldown;
    }

    public function setCooldown(int $cooldown) : void {
        $this->cooldown = $cooldown;
    }

    public function getPermission() : string {
        return $this->permission;
    }

    /**
     * @return array|Item[]|string[]
     */
    public function getArmor() : array {
        return $this->armor;
    }

    public function setArmor(array $armor) : void {
        $this->armor = $armor;
    }

    /**
     * @return array|Item[]|string[]
     */
    public function getItems() : array {
        return $this->contents;
    }

    public function setItems(array $items) : void {
        $this->contents = $items;
    }

    public function getName() : string {
        return $this->name;
    }

    public function setName(string $name) : void {
        $this->name = $name;
    }

    public function getId() : int {
        return $this->id;
    }

    public function __construct(int $id, string $name, string $permission, int $cooldown, array $contents = [], array $armor = []){
        $this->id = $id;
        $this->name = $name;
        $this->cooldown = $cooldown;
        $this->armor = $armor;
        $this->permission = $permission;
        $this->contents = $contents;
    }

}