<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\crate\CrateManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class CratesForm {

    public static function sendTo(Player $sender) : void {
        $ui = new SimpleForm(static function (Player $player, ?string $data){
            if($data === null) {
                return;
            }

            $crate = CrateManager::getInstance()->get((int)$data);
            if($crate === null){
                $player->sendMessage("§r§c§l>§r§7 That crate does not exist.");
                return;
            }

            CrateItemsInventory::sendTo($player, $crate);
        });

        $ui->setTitle("§bCrates");

        $ui->setContent("§r§7Click on a crate to view its drop chances");
        foreach(CrateManager::getInstance()->getAll() as $crate){
            $ui->addButton("§r§b§l".$crate->getName()."§r§8 crate", -1, "", (string)$crate->getId());
        }

        $sender->sendForm($ui);
    }

}