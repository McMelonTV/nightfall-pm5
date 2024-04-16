<?php

namespace AndreasHGK\Core\vault;

use AndreasHGK\Core\user\OfflineUser;
use AndreasHGK\Core\user\UserManager;
use muqsit\invmenu\InvMenu;
use pocketmine\item\Item;
use pocketmine\Server;

class Vault {

    public const MAX_ITEMS = 45;

    private $owner;

    /** @var Item[] */
    private array $pages;

    private $maxPages = 1;

    private $isOpened = false;

    private ?InvMenu $menu;

    private $openedPage = null;

    public function isOpen() : bool {
        return $this->isOpened;
    }

    public function setOpen(bool $open) : void {
        $this->isOpened = $open;
    }

    public function addItem(Item $item) : void {
        $maxPages = $this->getEffectiveMaxPages();
        for($i = 1; $i <= $maxPages; ++$i){
            if($this->canAddItem($i, $item)){
                $this->addItemToPage($i, $item);
                return;
            }
        }
    }

    public function addItemToPage(int $pageId, Item $item) : void {
        $page = $this->getPage($pageId);
        //$page[0] = ItemFactory::getInstance()->get(ItemIds::AIR);
        if($item->getCount() > 1){
            while($item->getCount() > $item->getMaxStackSize()){
                $page[] = $item->pop($item->getMaxStackSize());
            }
        }
        $page[] = $item;
        $this->setPage($pageId, $page);
    }

    public function getOpenedPage() : ?int {
        return $this->openedPage;
    }

    public function setOpenedPage(?int $page) : void {
        $this->openedPage = $page;
    }

    public function hasMenu() : bool {
        return isset($this->menu);
    }

    public function getMenu() : ?InvMenu {
        return $this->menu;
    }

    public function setMenu(?InvMenu $menu) : void {
        $this->menu = $menu;
    }

    public function canAddItem(int $page, Item $item) : bool {
        $item = clone $item;
        $page = $this->getPage($page);
        if(empty($page)) {
            return true;
        }

        $count = $item->getCount();
        $maxStackSize = $item->getMaxStackSize();
        /** @var Item $slot */
        foreach($page as $slot){
            if($item->canStackWith($slot)){
                if(($diff = $slot->getMaxStackSize() - $slot->getCount()) > 0){
                    $item->setCount($count - $diff);
                }
            }elseif($slot->isNull()){
                //$item->setCount($item->getCount() - Inventory::MAX_STACK);
                $item->setCount($count - $maxStackSize);
            }

            if($count <= 0){
                return true;
            }
        }

        $stacks = intdiv($count, $maxStackSize);
        if(self::MAX_ITEMS - count($page) >= $stacks) {
            return true;
        }

        return false;
    }

    public function getPage(int $page) : array {
        return $this->pages[$page] ?? [];
    }

    public function setPage(int $page, array $data) : void {
        $this->pages[$page] = $data;
    }

    public function getEffectiveMaxPages() : int {
        if($this->getOwner()->getRankComponent()->isDonator()) return $this->getMaxPages() + $this->getOwner()->getRankComponent()->getDonatorRank()->getRank()->getVaults();
        return $this->getMaxPages();
    }

    public function getMaxPages() : int {
        return $this->maxPages;
    }

    public function setMaxPages(int $pages) : void {
        $this->maxPages = $pages;
    }

    public function getOwner() : OfflineUser {
        return UserManager::getInstance()->get(Server::getInstance()->getOfflinePlayer($this->owner));
    }

    public function getOwnerName() : string {
        return $this->owner;
    }

    public function setOwner(OfflineUser $user) : void {
        $this->owner = $user->getName();
    }

    public function getPages() : array{
        return $this->pages;
    }

    public function setPages(array $pages) : void {
        $this->pages = $pages;
    }

    public function __construct(OfflineUser $owner, array $pages){
        $this->owner = $owner->getName();
        $this->pages = $pages;
    }
}