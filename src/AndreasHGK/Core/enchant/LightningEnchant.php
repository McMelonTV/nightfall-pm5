<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\EnchantmentUtils;
use pocketmine\item\Item;
use pocketmine\player\Player;
use function mt_rand;

class LightningEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_SWORD];
    }

    public function getDescription() : string {
        return "Has a chance to strike someone with lightning, dealing 3 hearts of damage + fire damage";
    }

    public function getName() : string {
        return "Lightning";
    }

    public function getId() : int {
        return CustomEnchantIds::LIGHTNING;
    }

    public function getRarity() : int {
        return self::RARITY_LEGENDARY;
    }

    public function getMaxLevel() : int {
        return 3;
    }

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        if($isAttacker && mt_rand(0, 100) < 1*$this->getLevel()){
            $player = $ev->getEvent()->getEntity();
            if(!$player instanceof Player){
                return;
            }

            $player->setOnFire(6);

            $damage = 6;
            $helm = $player->getArmorInventory()->getHelmet();
            if(!$helm->isNull()){
                $helmInterface = ItemInterface::fromItem($helm);
                $ench = CustomEnchantsManager::getInstance()->get(CustomEnchantIds::INSULATOR);
                if($helmInterface->hasEnchantment($ench)){
                    $ench = $helmInterface->getCustomEnchants()[$ench->getId()];
                    switch($ench->getLevel()){
                        case 1:
                            $damage = 4;
                            break;
                        case 2:
                            $damage = 3;
                            break;
                        case 3:
                            $damage = 2;
                            break;
                    }
                }
            }

            if(($player->getHealth() - $damage) <= 0){
                $user = UserManager::getInstance()->getOnline($player);
                $user->addDeath();
            }

            $player->setHealth($player->getHealth() - $damage);
            EnchantmentUtils::lightning($player);
        }
    }
}