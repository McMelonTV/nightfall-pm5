<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\achievement\AchievementManager;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use muqsit\invmenu\InvMenu;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;

class AchievementsInventory {

    public static function sendTo(Player $sender) : void {
        $user = UserManager::getInstance()->get($sender);

        if(!$user instanceof User) {
            return;
        }

        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName("§8Achievements");
        $menu->setListener($menu->readonly());

        $achievements = AchievementManager::getInstance()->getAll();

        $key = 0;
        foreach ($achievements as $achievement){
            $item = VanillaBlocks::STAINED_CLAY()->setColor(($achievement->isAchievedBy($user) ? DyeColor::LIME() : DyeColor::RED()))->asItem();
            $item->setCustomName("§r§b".$achievement->getName());
            $item->setLore([$achievement->isAchievedBy($user) ? "§r§7".$achievement->getDesc() : "§r§8[§cLocked§8]"]);
            $item->setNamedTag($item->getNamedTag()->setString("achievement", $achievement->getName()));
            $menu->getInventory()->setItem($key, $item);
            ++$key;
        }

        $menu->send($sender);
    }
}