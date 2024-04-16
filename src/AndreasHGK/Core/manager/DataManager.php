<?php

declare(strict_types=1);

namespace AndreasHGK\Core\manager;

use AndreasHGK\Core\auctionhouse\AuctionManager;
use AndreasHGK\Core\Core;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\holotext\HolotextManager;
use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\pvp\PVPZoneManager;
use AndreasHGK\Core\rank\MineRankManager;
use AndreasHGK\Core\tag\TagManager;
use AndreasHGK\Core\user\BannedUserManager;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\vault\VaultManager;
use pocketmine\utils\Config;

class DataManager {

    public const CONFIG = "config.yml";
    public const BROADCAST = "broadcast.yml";
    public const AUCTION = "auction.json";
    public const BANS = "bans.json";
    public const PRICES = "prices.yml";

    /**
     * @var DataManager */
    public static $instance = null;

    /**
     * @var Config[]
     */
    public static $memory = [];
    /** @var string */
    public static $dataFolder;

    /**
     * @param string $file
     * @param string $key
     * @param bool $default
     * @return mixed
     */
    public static function getKey(string $file, string $key, $default = false){
        return self::get($file)->get($key, $default);
    }

    public static function get(string $file, bool $keepLoaded = true) : Config {
        if(self::isLoaded($file)) {
            return self::$memory[$file];
        }

        return self::load($file, $keepLoaded);
    }

    public static function load(string $file, bool $keepLoaded = true) : Config {
        $data = self::getFile($file);
        if($keepLoaded){
            self::$memory[$file] = $data;
        }

        return $data;
    }

    public static function reload(string $file, bool $save = false) : bool{
        if(!self::isLoaded($file)) {
            return false;
        }

        if($save) {
            self::get($file)->save();
        }

        self::get($file)->reload();
        return true;
    }

    public static function unload(string $file) : bool {
        if(!self::isLoaded($file)) {
            return false;
        }

        self::save($file);
        unset(self::$memory[$file]);
        return true;
    }

    public static function isLoaded(string $file) : bool{
        return isset(self::$memory[$file]);
    }

    public static function save(string $file) : bool{
        if(!self::isLoaded($file)) {
            return false;
        }
        
        self::$memory[$file]->save();
        return true;
    }

    public static function getFile(string $file) : Config{
        return new Config(self::$dataFolder.$file);
    }

    public static function deleteFile(string $file) : void {
        unlink(self::$dataFolder.$file);
    }

    public static function exists(string $file) : bool {
        return file_exists(self::$dataFolder.$file);
    }

    public static function getFilesIn(string $location) : array {
        return array_diff(scandir(self::$dataFolder.$location), [".", ".."]);
    }

    public static function loadDefault() : void {
        self::$dataFolder = Core::getInstance()->getDataFolder();

        @mkdir(self::$dataFolder.UserManager::USER_FOLDER);
        @mkdir(self::$dataFolder.MineRankManager::RANKS_FOLDER);
        @mkdir(self::$dataFolder.VaultManager::VAULT_FOLDER);
        @mkdir(self::$dataFolder.MineManager::MINES_FOLDER);
        @mkdir(self::$dataFolder.GangManager::GANG_FOLDER);
        if(Core::getInstance()->saveResource(self::CONFIG)) Core::getInstance()->getLogger()->debug("creating ".self::CONFIG);
        if(Core::getInstance()->saveResource(self::BROADCAST)) Core::getInstance()->getLogger()->debug("creating ".self::BROADCAST);
        self::get(self::CONFIG);
        self::get(self::BROADCAST);
        MineRankManager::getInstance()->loadAll();
        TagManager::getInstance()->loadAll();
        PVPZoneManager::getInstance()->loadAll();
        AuctionManager::getInstance()->loadAll();
        BannedUserManager::getInstance()->loadAll();
        GlobalPrices::getInstance()->loadAll();
        HolotextManager::getInstance()->loadAll();
        PlotManager::getInstance()->loadAll();
        GangManager::getInstance()->loadAll();
    }

    private function __construct(){}
}