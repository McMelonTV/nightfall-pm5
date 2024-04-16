<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\achievement\Achievement;
use AndreasHGK\Core\achievement\AchievementManager;
use AndreasHGK\Core\forge\ForgeCategory;
use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\user\UserManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\world\sound\AnvilUseSound;

class ForgeCategoryForm {

    public static function sendTo(Player $sender, ForgeCategory $category) : void {
        if(empty($category->getItems())){
            $sender->sendMessage("§c§l> §r§7This category is not available at the moment.");
            return;
        }

        $ui = new SimpleForm(static function (Player $player, ?string $data) use($category){
            if($data === null){
                ForgeNewForm::sendTo($player);
                return;
            }

            $forgeItems = $category->getItems();
            $forgeItem = null;
            foreach($forgeItems as $i){
                if($i->getName() === $data){
                    $forgeItem = $i;
                    break;
                }
            }

            if($forgeItem === null){
                $player->sendMessage("§c§l> §r§7The selected item could not be found.");
                return;
            }

            if(!$player->getInventory()->canAddItem(clone $forgeItem->getItem())){
                $player->sendMessage("§c§l> §r§7You don't have enough space in your inventory to forge this item.");
                return;
            }

            if(!$forgeItem->getPrice()->canAfford($player)){
                $player->sendMessage("§c§l> §r§7You cannot afford to forge this.");
                return;
            }

            $user = UserManager::getInstance()->getOnline($player);
            $forgeItem->getPrice()->pay($player);
            if($category->getName() === "Pickaxes"){
                AchievementManager::getInstance()->tryAchieve($user, Achievement::GETTING_AN_UPGRADE);
            }

            $interface = ItemInterface::fromItem(clone $forgeItem->getItem());
            if($interface->getQuality() >= 27){
                AchievementManager::getInstance()->tryAchieve($user, Achievement::QUALITY_MARKSMANSHIP);
            }

            $player->getInventory()->addItem(clone $forgeItem->getItem());
            $player->sendMessage("§r§b§l> §r§7You forged a ".$forgeItem->getDisplayTag()."§r§7.");

            $user->playSound(new AnvilUseSound());
        });
        $ui->setTitle("§bItem Forge - ".$category->getDisplayTag());

        foreach($category->getItems() as $forgeItem){
            $ui->addButton($forgeItem->getDisplayTag()."\n§r".$forgeItem->getPrice()->toString(), -1, "", $forgeItem->getName());
        }

        $sender->sendForm($ui);
    }
}