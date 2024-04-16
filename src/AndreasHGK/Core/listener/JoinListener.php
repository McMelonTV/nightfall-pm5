<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\enchant\CustomEnchantIds;
use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\holotext\HolotextManager;
use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\kit\KitManager;
use AndreasHGK\Core\tag\TagManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\world\sound\TotemUseSound;

class JoinListener implements Listener {

    public function onJoin(PlayerJoinEvent $ev) : void {
        $player = $ev->getPlayer();

        $player->sendMessage("§8§l<--§bNF§8--> ".
            "\n§r§7§b Nightfall§7 useful links§r".
            "\n§r §b§l> §r§7Vote: §bvote.nightfall.xyz §8(get rewards like a free rankup)".
            "\n§r §b§l> §r§7Shop: §bshop.nightfall.xyz".
            "\n§r §b§l> §r§7Discord: §bdiscord.gg/nightfall".
            "\n§r§8§l<--++-->⛏");

        $user = UserManager::getInstance()->getOnline($player);
        foreach(TagManager::getInstance()->getAll() as $tag){
            if($tag->getReceiveOnJoin() && !$user->hasTag($tag)){
                $user->grantTag($tag);
            }
        }

        $player->sendTitle("§r§b<§o§8§lNightfall§r§b>", "", 0, 20, 30);

        if($user->getSize() !== 100){
            $player->setScale($user->getSize()/100);
        }

        $user->playSound(new TotemUseSound());

        $c = count($user->getExpiredAuctionItems());
        if($c > 0){
            $player->sendMessage("§r§b§l> §r§7You have §b".$c."§7 expired auction item(s) that you can reclaim.");
        }

        foreach(HolotextManager::getInstance()->getAll() as $text){
            $text->spawnToAll();
        }

        if(!$user->hasReceivedStartItems()){
            $kit = KitManager::getInstance()->get(10);
            $kit->claim($user);
            $player->getInventory()->addItem(CustomItemManager::getInstance()->get(CustomItem::GUIDEBOOK)->getItem());
            $user->setReceivedStartItems(true);
        }
    }
}