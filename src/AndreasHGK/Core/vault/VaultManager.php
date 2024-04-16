<?php

namespace AndreasHGK\Core\vault;

use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\user\OfflineUser;
use AndreasHGK\Core\utils\FileUtils;
use pocketmine\item\Item;

class VaultManager {

    public const VAULT_FOLDER = "vaults".DIRECTORY_SEPARATOR;

    public static $instance;

    public $vaults = [];

    public function get(OfflineUser $owner, bool $create = true) : ?Vault {
        if($this->isLoaded($owner)) {
            return $this->vaults[$owner->getName()];
        }

        if($this->exists($owner)) {
            return $this->load($owner);
        }

        if($create) {
            return $this->create($owner);
        }

        return null;
    }

    public function load(OfflineUser $owner) : ?Vault {
        $file = DataManager::get(self::VAULT_FOLDER.FileUtils::MakeJSON(strtolower($owner->getName())));
        $maxPages = $file->get("maxPages", 1);
        $pages = $file->get("pages", []);
        $loadedPages = [];
        foreach($pages as $number => $page){
            $loadedPage = [];
            foreach($page as $key => $item){
                $loadedPage[$key] = Item::jsonDeserialize($item);
            }

            $loadedPages[$number] = $loadedPage;
        }

        $vault = new Vault($owner, $loadedPages);
        $vault->setMaxPages($maxPages);
        $this->vaults[$owner->getName()] = $vault;
        return $vault;
    }

    public function unload(OfflineUser $owner) : void {
        unset($this->vaults[$owner->getName()]);
    }

    public function create(OfflineUser $owner) : Vault {
        $vault = new Vault($owner, []);
        $vault->setMaxPages(1);
        $this->vaults[$owner->getName()] = $vault;
        return $vault;
    }

    public function saveAll() : void {
        foreach ($this->vaults as $vault){
            $this->save($vault);
        }
    }

    public function save(Vault $vault) : void {
        $file = DataManager::get(self::VAULT_FOLDER.FileUtils::MakeJSON(strtolower($vault->getOwnerName())));
        $file->set("owner", $vault->getOwnerName());
        $file->set("maxPages", $vault->getMaxPages());
        $pages = [];
        foreach($vault->getPages() as $pageNumber => $items){
            $page = [];
            foreach($items as $key => $item){
                /** @var $item Item */
                $page[$key] = $item->jsonSerialize();
            }

            $pages[$pageNumber] = $page;
        }
        $file->set("pages", $pages);
        $file->save();
    }

    public function isLoaded(OfflineUser $owner) : bool {
        return isset($this->vaults[$owner->getName()]);
    }

    public function exists(OfflineUser $owner) : bool {
        return DataManager::exists(self::VAULT_FOLDER.FileUtils::MakeJSON($owner->getName()));
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}