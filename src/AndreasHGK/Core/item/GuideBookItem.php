<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\utils\EnchantmentUtils;
use pocketmine\item\VanillaItems;

class GuideBookItem extends CustomItem {

    public function __construct(){
        $item = VanillaItems::WRITTEN_BOOK();
        $item->setCustomName("§r§bNightfall Guide Book");
        EnchantmentUtils::applyGlow($item);
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::GUIDEBOOK));
        $item->setLore(["§r§7A guide to help new players\n§r§7to get familiar with the server."]);
        $item->setTitle("§r§bNightfall Guide Book");
        $item->setAuthor("§bNightfall§r");

        $item->setPageText(0, "§r§0Welcome to the §bNightfall server§0!\n\nThis book will help you learn the basics of the server and get started with mining. If you would still like more information, you can ask our staff and they will help you out. Please also report any issues or hackers to them.");

        $item->setPageText(1,
            "Guide contents:".
            "\n§0Page 1 > §bIntroduction".
            "\n§0Page 2 > §bContents".
            "\n§0Page 3 > §bThe basics".
            "\n§0Page 5 > §bRanking".
            "\n§0Page 6 > §bPrestige Pts".
            "\n§0Page 8 > §bThe forge".
            "\n§0Page 9 > §bEnchanting"
        );

        $item->setPageText(2, "§r§l§bThe basics\n§r§0". "Nightfall is an OP prison server. The main goal of a prison server is to mine materials, get to higher mines and become the richest inmate. Let's start with some of the basic commands. The get your first loadout, type §b/kit§0 and select the starter kit. The starter kit is not that good,");

        $item->setPageText(3, "but it will get you started. If you have purchased a donator rank, you can get yourself a better kit that will get you started faster. To start mining, do §b/mine§0. This will teleport you to your current mine. You will need §b$5000§0 to rank up to mine B. Once you got this, do §b/rankup§0. Now repeat!");

        $item->setPageText(4, "§r§l§bRanking\n§r§0". "The server has a total of 26 mines (not including mine pvp), each one being better than the last. Once you've hit the last mine, Z you can still continue playing. You can §b/prestige§0: it will take away all your money and reset you to mine A but you'll get some prestige points.");

        $item->setPageText(5, "§r§l§bPrestige Points\n§r§0". "Prestige points, or in short §opp§r§0, can be obtained in several different ways. The main way is by prestiging. This gives you by far the most §opp§r§0 but is also the most time consuming way. You can also get it by unlocking achievements or killing players.");

        $item->setPageText(6, "Prestige points are mainly used for buying player upgrades. You can unlock more plots, vaults and auction slots in §b/shop§0. You are also able to buy donator ranks with your prestige points.");

        $item->setPageText(7, "§r§l§bThe forge\n§r§0". "The forge is the main way of acquiring new items in the server. You can use your resources (steeldust/stardust) to create new items. You can also repair your tools. Obsidian shards are the best resources for repairing. Repairing works like anvils.");

        $item->setPageText(8, "§r§l§bEnchanting\n§r§0". "You can create enchantments in §b/eforge§0. Select a rarity and you will get a random enchantment with that rarity. Forging enchants requires magicdust. You can combine enchants with the same level to increase the level by 1.");

        $item->setPageText(9, "You can do this on tools/armor/weapons too. To equip enchants on compatible items, drag the book onto it in your inventory. If you have an enchantment you don't like, you could auction it off.");

        parent::__construct(self::GUIDEBOOK, "guidebook", $item);
    }
}