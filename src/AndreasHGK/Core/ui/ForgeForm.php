<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class ForgeForm {

    public static function sendTo(Player $sender) : void {
        $ui = new SimpleForm(static function (Player $player, ?string $data){
            if($data === null){
                return;
            }

            if($data === "new"){
                ForgeNewForm::sendTo($player);
                return;
            }

            if($data === "repair"){
                ForgeRepairInventory::sendTo($player);
                return;
            }

            if($data === "disenchant"){
                DisenchantForm::sendTo($player);
                return;
            }

            if($data === "enchant"){
                EnchantmentForgeForm::sendTo($player);
                return;
            }
        });

        $ui->setTitle("§bItem Forge");

        $ui->addButton("§8Forge a new item", -1, "", "new");
        $ui->addButton("§8Forge an enchantment", -1, "", "enchant");
        $ui->addButton("§8Remove enchantments", -1, "", "disenchant");
        $ui->addButton("§8Repair an item", -1, "", "repair");

        $sender->sendForm($ui);
    }
}