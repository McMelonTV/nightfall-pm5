<?php

namespace AndreasHGK\Core\user;

use AndreasHGK\Core\auctionhouse\AuctionItem;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\rank\MineRankManager;
use AndreasHGK\Core\utils\FileUtils;
use AndreasHGK\Core\utils\StringSet;
use AndreasHGK\Core\utils\TagUtils;
use AndreasHGK\Core\warning\Warning;
use pocketmine\item\Item;
use pocketmine\player\IPlayer;
use pocketmine\player\Player;
use pocketmine\Server;

class UserManager {

    public const USER_FOLDER = "users".DIRECTORY_SEPARATOR;

    private static $instance;

    /**
     * @var array|User[]
     */
    private $users = [];

    public function matchUser() : ?OfflineUser{
        //todo
        return null;
    }

    /**
     * @return OfflineUser[]|array
     */
    public function getAll() : array {
        $array = [];
        $scan = DataManager::getFilesIn(self::USER_FOLDER);
        foreach($scan as $filename){
            $u = $this->get(Server::getInstance()->getOfflinePlayer(explode(".", $filename)[0]));
            $array[$u->getName()] = $u;
        }

        return $array;
    }

    /**
     * @return array|User[]
     */
    public function getAllOnline() : array {
        return $this->users;
    }

    /**
     * @param IPlayer $player
     * @param bool $create
     * @return OfflineUser|null
     */
    public function get(IPlayer $player, bool $create = false) : ?OfflineUser {
        if($this->isLoaded($player) && $player instanceof Player) {
            /** @var Player $player */
            return $this->getOnline($player);
        }

        if($this->exist($player->getName())) {
            return $this->load($player);
        }

        if($create) {
            $this->create($player);
            return $this->load($player);
        }

        return null;
    }

    /**
     * @param Player $player
     * @return User|null
     */
    public function getOnline(Player $player) : ?User {
        return $this->users[$player->getName()] ?? null;
    }

    public function load(IPlayer $player) : OfflineUser {
        $file = DataManager::get(self::USER_FOLDER.FileUtils::MakeJSON(strtolower($player->getName())));
        if(!$player instanceof Player){
            $user = new OfflineUser($player, $file->getAll());
        }else{
            $user = new User($player, $file->getAll());
        }

        if($player instanceof Player && $user instanceof User){
            $user->setPermissionAttachment();
        }

        $user->setMinedBlocks($file->get("minedBlocks", 0));
        $user->setBalance($file->get("balance", 0));
        $user->setMineRank(MineRankManager::getInstance()->get($file->get("mineRank", 0)));
        $user->setPrestige(max(1, $file->get("prestige", 1)));
        $user->setTags(TagUtils::arrayToTags($file->get("tags", [])));
        $user->setPrestigePoints($file->get("prestigePoints", 0));
        $user->setAppliedTag($file->get("appliedTag", ""));
        $user->setTagColor($file->get("tagColor", ""));
        $user->setMuted($file->get("muted", false));
        $user->setAchievements($file->get("achievements", []));
        $user->setNick($file->get("nick", ""));
        $user->setFly($file->get("fly", false));
        $user->setCooldowns($file->get("cooldowns", []));
        $user->setCommandSpy($file->get("commandSpy", false));
        $user->setIPList(StringSet::fromArray($file->get("iplist", [])));
        $user->setDeviceIdList(StringSet::fromArray($file->get("deviceIdList", [])));
        $user->setClientIdList(StringSet::fromArray($file->get("clientIdList", [])));
        $user->setSelfSignedIdList(StringSet::fromArray($file->get("selfSignedIdList", [])));
        $user->setMaxAuc($file->get("maxAucItems", 1));
        $user->setMaxPlots($file->get("maxPlots", 1));
        $user->setBlockedUsers($file->get("blockedUsers", []));
        $user->setGangId($file->get("gangId", ""));
        $user->setGangRank($this->gangRankFromString($file->get("gangRank", "")));
        $user->setKills($file->get("kills", 0));
        $user->setDeaths($file->get("deaths", 0));
        $user->setTotalEarnedMoney($file->get("totalearned", 0));
        $user->setShopPurchases($file->get("shopPurchases", []));
        $user->setReceivedStartItems($file->get("hasRecievedGuide", false));
        $user->setKitCooldowns($file->get("kitCooldowns", []));
        $user->setVotes($file->get("votes", 0));
        $user->setLastVote($file->get("lastVote", 0));
        $user->setReferree($file->get("referree", ""));
        $user->setReferrals($file->get("referrals", 0));
        $user->setLastPatchNotes($file->get("lastPatchNotes", ""));
        $user->setMuteExpire($file->get("muteExpire", -1));
        $user->setIgnoreAll($file->get("ignoreAll", false));
        $user->setSeenRules($file->get("hasSeenRules", false));
        $user->setSize($file->get("size", 100));
        $user->setVanished($file->get("vanished", false));
        $user->setTotalOnlineTime($file->get("totalOnlineTime", 0));
        $warns = [];
        foreach($file->get("warnings", []) as $warnData) {
            $warns[] = Warning::fromData($user->getPlayer(), $warnData);
        }
        $user->setWarnings($warns);
        foreach($file->get("expiredAuctionItems", []) as $data){
            $id = $data["id"];
            $seller = $data["seller"];
            $sellTime = $data["sellTime"];
            $item = Item::jsonDeserialize($data["item"]);
            $price = $data["price"];
            $user->addExpiredAuctionItem(new AuctionItem($id, $item, $seller, $sellTime, $price));
        }

        if($player instanceof Player){
            $this->users[$player->getName()] = $user;
        }

        return $user;
    }

    public function gangRankFromString(string $name) : ?GangRank{
        switch ($name) {
            case "leader":
                return GangRank::LEADER();
            case "officer":
                return GangRank::OFFICER();
            case "member":
                return GangRank::MEMBER();
            case "recruit":
                return GangRank::RECRUIT();
        }

        return null;
    }

    public function save(OfflineUser $user) : void {
        $file = DataManager::get(self::USER_FOLDER.FileUtils::MakeJSON(strtolower($user->getName())));
        $file->set("rankComponent", $user->getRankComponent()->toData());

        $file->set("name", $user->getName());
        $file->set("balance", $user->getBalance());
        $file->set("minedBlocks", $user->getMinedBlocks());
        $file->set("mineRank", $user->getMineRankId());
        $file->set("prestige", $user->getPrestige());
        $file->set("tags", TagUtils::tagsToArray($user->getTags()));
        $file->set("prestigePoints", $user->getPrestigePoints());
        $file->set("appliedTag", $user->getAppliedTag());
        $file->set("tagColor", $user->getTagColor());
        $file->set("muted", $user->isMuted());
        $file->set("achievements", $user->getAchievements());
        $file->set("nick", $user->getNick());
        $file->set("fly", $user->isFlying());
        $file->set("cooldowns", $user->getCooldowns());
        $file->set("commandSpy", $user->getCommandSpy());
        $file->set("iplist", $user->getIPList()->toArray());
        $file->set("deviceIdList", $user->getDeviceIdList()->toArray());
        $file->set("clientIdList", $user->getClientIdList()->toArray());
        $file->set("selfSignedIdList", $user->getSelfSignedIdList()->toArray());
        $file->set("maxAucItems", $user->getMaxAuc());
        $file->set("maxPlots", $user->getMaxPlots());
        $file->set("blockedUsers", $user->getBlockedUsers());
        $file->set("gangId", $user->getGangId());
        $file->set("gangRank", $user->getGangRankName());
        $file->set("kills", $user->getKills());
        $file->set("deaths", $user->getDeaths());
        $file->set("totalearned", $user->getTotalEarnedMoney());
        $file->set("shopPurchases", $user->getShopPurchases());
        $file->set("hasRecievedGuide", $user->hasReceivedStartItems());
        $file->set("kitCooldowns", $user->getKitCooldowns());
        $file->set("votes", $user->getVotes());
        $file->set("lastVote", $user->getLastVote());
        $file->set("referree", $user->getReferree());
        $file->set("referrals", $user->getReferrals());
        $file->set("lastPatchNotes", $user->getLastPatchNotes());
        $file->set("muteExpire", $user->getMuteExpire());
        $file->set("ignoreAll", $user->getIgnoreAll());
        $file->set("hasSeenRules", $user->hasSeenRules());
        $file->set("size", $user->getSize());
        $file->set("vanished", $user->isVanished());
        $file->set("totalOnlineTime", $user->getTotalOnlineTime());

        $warnData = [];
        foreach($user->getWarnings() as $warning) {
            $warnData[] = $warning->toData();
        }
        $file->set("warnings", $warnData);

        $auc = [];

        foreach($user->getExpiredAuctionItems() as $item => $expiredAuctionItem){
            $data = [];
            $data["id"] = $expiredAuctionItem->getId();
            $data["seller"] = $expiredAuctionItem->getSeller();
            $data["sellTime"] = $expiredAuctionItem->getSellTime();
            $data["item"] = $expiredAuctionItem->getItem()->jsonSerialize();
            $data["price"] = $expiredAuctionItem->getPrice();
            $auc[$expiredAuctionItem->getId()] = $data;
        }

        $file->set("expiredAuctionItems", $auc);
        $file->save();
        //DataManager::save(self::USER_FOLDER.FileUtils::MakeJSON($user->getName()));
    }

    public function unload(User $user) : void {
        unset($this->users[$user->getName()]);
    }

    public function isLoaded(IPlayer $player) : bool {
        return isset($this->users[$player->getName()]);
    }

    public function create(IPlayer $player) : void {
        if(!$player instanceof Player){
            $user = new OfflineUser($player, []);
        }else{
            $user = new User($player, []);
        }

        if($player instanceof Player){
            $user->setPermissionAttachment();
        }

        $user->setMinedBlocks(0);
        $user->setBalanceSilent(0);
        $user->setMineRank(MineRankManager::getInstance()->get(0));
        $user->setPrestige(0);
        $user->setTags([]);
        $user->setPrestigePoints(0);
        $user->setJoinTime(time());
        $user->setTotalOnlineTime(0);
        $this->save($user);
    }

    public function saveAll() : void {
        foreach($this->users as $user){
            $this->save($user);
        }
    }

    public function exist(string $user) : bool {
        return DataManager::exists(self::USER_FOLDER.FileUtils::MakeJSON(strtolower($user)));
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}