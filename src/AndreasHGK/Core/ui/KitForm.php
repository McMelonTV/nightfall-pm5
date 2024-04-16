<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\kit\KitManager;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\TimeUtils;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class KitForm {

    public static function sendTo(Player $sender) : void {
        $ui = new SimpleForm(static function (Player $player, $data){
            if($data === null) {
                return;
            }

            $kit = KitManager::getInstance()->get((int)$data);
            if($kit === null){
                $player->sendMessage("§r§c§l> §r§7That kit does not exist.");
                return;
            }

            if(!$player->hasPermission($kit->getPermission())){
                $player->sendMessage("§r§c§l> §r§7You don't have permission to claim that kit.");
                return;
            }

            $user = UserManager::getInstance()->getOnline($player);
            if($kit->isOnCooldown($user)){
                $player->sendMessage("§r§c§l> §r§7That kit is still on cooldown.");
                return;
            }

            if(!$kit->canAdd($player)){
                $player->sendMessage("§r§c§l> §r§7You don't have enough space in your inventory to claim this kit.");
                return;
            }

            $kit->claim(UserManager::getInstance()->getOnline($player));
        });

        $ui->setTitle("§bKits");
        $ui->setContent("§r§fSelect a kit to equip.");

        $user = UserManager::getInstance()->getOnline($sender);
        foreach(KitManager::getInstance()->getAll() as $kit){
            $ui->addButton("§r§b§l§o".$kit->getName()." §r§8§okit§r"."\n".($sender->hasPermission($kit->getPermission()) ? ($kit->isOnCooldown($user) ? "§c".TimeUtils::intToShortTimeString($kit->getCooldownTime($user)) : "§aClaim") : "§cLocked"), -1, "", (string)$kit->getId());
        }

        $sender->sendForm($ui);
    }

}