<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\ColorUtils;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class TagColorForm{

    public static function sendTo(Player $sender) : void {
        $ui = new SimpleForm(static function (Player $player, $data){
            if($data === null){
                return;
            }

            $user = UserManager::getInstance()->getOnline($player);

            if(!$player->hasPermission("nightfall.tag.colored")){
                $player->sendMessage("§r§c§l>§r§7 You don't have permission to a custom color.");
                return;
            }

            if($data === "__clear"){
                $user->setTagColor("");
                $player->sendMessage("§r§b§l>§r§7 You removed your tag color.");
                return;
            }

            if(ColorUtils::getColorCodeFor($data) === ""){
                $player->sendMessage("§r§c§l>§r§7 You have selected an invalid color.");
                return;
            }

            $user->setTagColor(ColorUtils::getColorCodeFor($data));
            $player->sendMessage("§r§b§l>§r§7 You changed your tag color to §r".ColorUtils::getFullColor($data)."§r§7.");
        });
        $ui->setTitle("§bTag color selector");
        $ui->setContent("§7Select a color to apply to your tag. Your tag will be in this color instead of the default color.");

        $ui->addButton("§0> §8Clear color §0<", -1, "", "__clear");

        foreach(ColorUtils::COLORS as $name => $colorCode){
            $ui->addButton(ColorUtils::getFullColor($name), -1, "", $name);
        }

        $sender->sendForm($ui);
    }
}