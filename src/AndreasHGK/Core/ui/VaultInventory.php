<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\user\OfflineUser;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\vault\Vault;
use AndreasHGK\Core\vault\VaultManager;
use muqsit\invmenu\inventory\InvMenuInventory;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class VaultInventory {

    public static function sendTo(Player $sender, int $page = 1, OfflineUser $vaultOwner = null) : void {
        $user = UserManager::getInstance()->get($sender);
        $vault = $user->getVault();
        if(isset($vaultOwner)){
            $vault = $vaultOwner->getVault();
        }

        if($vault->isOpen()){
            $sender->sendMessage("§r§c>§r§7 Someone is already looking in that vault.");
            return;
        }

        $vault->setOpen(true);

        if(!$vault->hasMenu()){
            $page = $vault->getOpenedPage() ?? $page;
            $vault->setOpenedPage($page);
            $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
            $menu->setName("§8Vault");
            $menu->setListener(static function (InvMenuTransaction $ts) use ($menu, $vault){
                $itemClicked = $ts->getOut();
                $action = $ts->getAction();
                if($itemClicked->getNamedTag()->getTag("vaultMenuItem") !== null){
                    if($itemClicked->getNamedTag()->getString("vaultMenuItem") === "previous"){
                        if($vault->getOpenedPage() === 1) {
                            return new InvMenuTransactionResult(true);
                        }

                        self::save($vault, $action->getInventory());
                        $vault->setOpenedPage($vault->getOpenedPage()-1);
                        self::updatePage($vault, $action->getInventory());
                    }elseif($itemClicked->getNamedTag()->getString("vaultMenuItem") === "next"){
                        if($vault->getOpenedPage() === $vault->getEffectiveMaxPages()) {
                            return new InvMenuTransactionResult(true);
                        }

                        self::save($vault, $action->getInventory());
                        $vault->setOpenedPage($vault->getOpenedPage()+1);
                        self::updatePage($vault, $action->getInventory());
                    }
                    return new InvMenuTransactionResult(true);
                }
                self::save($vault, $action->getInventory());
                return new InvMenuTransactionResult(false);
            });

            $menu->setInventoryCloseListener(static function(Player $player, InvMenuInventory $inventory) use ($menu, $vault){
                self::save($vault, $inventory);
                $vault->setOpenedPage(null);
                $vault->setMenu(null);
                $vault->setOpen(false);
                VaultManager::getInstance()->save($vault);
            });

            self::updatePage($vault, $menu->getInventory());
            $vault->setMenu($menu);
        }else{
            $menu = $vault->getMenu();
        }

        $menu->send($sender, isset($vaultOwner) ? "§b".$vaultOwner->getName()."§8's vault" : null);
    }

    public static function updatePage(Vault $vault, Inventory $invMenu) : void {
        $page = $vault->getPage($vault->getOpenedPage());
        for($i = 0; $i < 45; ++$i){
            $invMenu->setItem($i, $page[$i] ?? VanillaItems::AIR());
        }

        for($i = 45; $i < 54; ++$i){
            switch ($i){
                case 48:
                    $item = $vault->getOpenedPage() !== 1 ? VanillaItems::PAPER() : VanillaBlocks::BARRIER()->asItem();
                    $item->setCustomName("§r§bPrevious §7page");
                    $item->setNamedTag($item->getNamedTag()->setString("vaultMenuItem", "previous"));
                    $invMenu->setItem($i, $item);
                    break;
                case 49:
                    $item = VanillaBlocks::CHEST()->asItem();
                    $item->setCustomName("§r§7Page §b".$vault->getOpenedPage()."§8/§b".$vault->getEffectiveMaxPages());
                    $item->setNamedTag($item->getNamedTag()->setString("vaultMenuItem", "page"));
                    $invMenu->setItem($i, $item);
                    break;
                case 50:
                    $item = $vault->getOpenedPage() !== $vault->getEffectiveMaxPages() ? VanillaItems::PAPER() : VanillaBlocks::BARRIER()->asItem();
                    $item->setCustomName("§r§bNext §7page");
                    $item->setNamedTag($item->getNamedTag()->setString("vaultMenuItem", "next"));
                    $invMenu->setItem($i, $item);
                    break;
                default:
                    $item = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED)->asItem();
                    $item->setCustomName("§r§c/");
                    $item->setNamedTag($item->getNamedTag()->setString("vaultMenuItem", "occupied"));
                    $invMenu->setItem($i, $item);
                    break;
            }
        }
    }

    public static function save(Vault $vault, Inventory $invMenu) : void {
        $page = $vault->getOpenedPage() ?? 1;
        $items = [];
        foreach($invMenu->getContents(false) as $key => $item){
            if($key >= 45) {
                break;
            }

            $items[$key] = $item;
        }

        $vault->setPage($page, $items);
    }
}