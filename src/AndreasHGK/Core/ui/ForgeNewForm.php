<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\forge\ForgeCategoryManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class ForgeNewForm {

    public static function sendTo(Player $sender) : void {
        $ui = new SimpleForm(static function (Player $player, ?string $data){
            if($data === null){
                return;
            }

            $category = ForgeCategoryManager::getInstance()->get($data);
            if($category === null){
                $player->sendMessage("§c§l> §r§7The selected category could not be found.");
                return;
            }

            ForgeCategoryForm::sendTo($player, $category);
        });

        $ui->setTitle("§bItem Forge");
        foreach(ForgeCategoryManager::getInstance()->getAll() as $forgeCategory){
            $ui->addButton($forgeCategory->getDisplayTag(), -1, "", $forgeCategory->getName());
        }

        $sender->sendForm($ui);
    }

}