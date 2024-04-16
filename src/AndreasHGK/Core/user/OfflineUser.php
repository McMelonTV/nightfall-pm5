<?php

declare(strict_types=1);

namespace AndreasHGK\Core\user;

use AndreasHGK\Core\auctionhouse\AuctionItem;
use AndreasHGK\Core\gang\Gang;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\item\CrateKey;
use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\plot\Plot;
use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\rank\MineRank;
use AndreasHGK\Core\rank\MineRankManager;
use AndreasHGK\Core\shop\ShopItem;
use AndreasHGK\Core\tag\Tag;
use AndreasHGK\Core\utils\StringSet;
use AndreasHGK\Core\vault\Vault;
use AndreasHGK\Core\vault\VaultManager;
use AndreasHGK\Core\vote\VoteParty;
use AndreasHGK\Core\warning\Warning;
use AndreasHGK\RankSystem\rank\RankInstance;
use AndreasHGK\RankSystem\session\OnlineRankComponent;
use AndreasHGK\RankSystem\session\RankComponent;
use pocketmine\player\IPlayer;
use pocketmine\player\Player;
use pocketmine\Server;
use function time;

class OfflineUser {

    protected string $name;

    protected $balance = 0;

    protected $totalEarned = 0;

    protected $minedBlocks;

    protected $mineRank;

    protected $rank;

    protected $donatorRank;

    protected $prestige = 1;

    protected $prestigePoints = 0;

    protected $tags = [];

    protected $appliedTag = "";

    protected $tagColor = "";

    protected bool $muted = false;

    protected $achievements = [];

    protected $nick = "";

    protected bool $fly = false;

    protected $cooldowns = [];

    protected bool $commandSpy = false;

    protected $boosters = [];

    protected StringSet $IPList;

    protected StringSet $deviceIdList;

    protected StringSet $clientIdList;

    protected StringSet $selfSignedIdList;

    protected $muteExpire = -1;

    //protected $isBanned = false;

    //protected $banExpire = -1;

    protected $expiredAuctionItems = [];

    protected $maxAuc = 1;

    protected $maxPlots = 1;

    protected $blockedUsers = [];

    protected $gangId = "";

    protected ?GangRank $gangRank = null;

    protected $kills = 0;

    protected $deaths = 0;

    protected $shopPurchases = [];

    protected bool $hasReceivedStartItems = false;

    protected $kitCooldowns = [];

    protected $votes = 0;

    protected $lastVote = 0;

    protected $referree = "";

    protected $referrals = 0;

    protected $lastPatchNotes = "";

    protected bool $ignoreAll = false;

    protected bool $hasSeenRules = false;

    protected $size = 100;

    /** @var Warning[] */
    private array $warnings = [];

    private bool $vanish = false;

    protected int $totalOnlineTime;

    private RankComponent $rankComponent;

    public function __construct(IPlayer $player, array $data){
        $this->name = $player->getName();
        $this->rankComponent = OnlineRankComponent::fromData($data["rankComponent"] ?? [], $player);
        $this->IPList = new StringSet();
        $this->deviceIdList = new StringSet();
        $this->clientIdList = new StringSet();
        $this->selfSignedIdList = new StringSet();
    }

    public function getTotalOnlineTime() : int{
        if($this->isVanished()){
            return $this->totalOnlineTime;
        }

        return $this->getSessionTime() + $this->totalOnlineTime;
    }

    public function setTotalOnlineTime(int $totalOnlineTime) : void{
        $this->totalOnlineTime = $totalOnlineTime;
    }

    public function getSessionTime() : int{
        return 0;
    }

    /**
     * Check if the player is invisible and unable to be seen on the list
     *
     * @return bool
     */
    public function isVanished() : bool {
        return $this->vanish;
    }

    /**
     * Change the vanished state
     *
     * @param bool $vanished
     */
    public function setVanished(bool $vanished) : void {
        $this->vanish = $vanished;
    }

    /**
     * Get the amount of warns the player has that have not yet expired
     *
     * @return int
     */
    public function countValidWarns() : int {
        $i = 0;
        foreach($this->warnings as $warning) {
            if($warning->isExpired()) continue;
            $i++;
        }
        return $i;
    }

    /**
     * Get all the warnings the user has
     *
     * @return Warning[]
     */
    public function getWarnings() : array {
        return $this->warnings;
    }

    /**
     * Add a warning to the player
     *
     * @param Warning $warning
     */
    public function warn(Warning $warning) : void {
        $this->warnings[] = $warning;
    }

    /**
     * Set the warnings for a player
     *
     * @param Warning[] $warnings
     */
    public function setWarnings(array $warnings) : void {
        $this->warnings = $warnings;
    }

    public function getSize() : int {
        return $this->size;
    }

    public function setSize(int $size) : void {
        $this->size = $size;
    }

    public function hasSeenRules() : bool {
        return $this->hasSeenRules;
    }

    public function setSeenRules(bool $bool) : void {
        $this->hasSeenRules = $bool;
    }

    public function getIgnoreAll() : bool {
        return $this->ignoreAll;
    }

    public function setIgnoreAll(bool $ignore) : void {
        $this->ignoreAll = $ignore;
    }

    public function getLastPatchNotes() : string {
        return $this->lastPatchNotes;
    }

    public function setLastPatchNotes(string $ver) : void {
        $this->lastPatchNotes = $ver;
    }

    public function getReferree() : string {
        return $this->referree;
    }

    public function setReferree(string $referree) : void {
        $this->referree = $referree;
    }

    public function getReferrals() : int {
        return $this->referrals;
    }

    public function setReferrals(int $refer) : void {
        $this->referrals = $refer;
    }

    /**
     * Get the rank component for a player
     *
     * @return RankComponent
     */
    public function getRankComponent() : RankComponent {
        return $this->rankComponent;
    }

    public function castVote() : void {
        $this->setLastVote(time());
        $this->addVote();
        VoteParty::getInstance()->addVote();
        if(VoteParty::getInstance()->getVotes() >= VoteParty::PARTY){
            VoteParty::getInstance()->setVotes(0);
            Server::getInstance()->broadcastMessage("§r§8[§bNF§8] §r§7It's vote party! Everyone gets a §bvote key§r§7!");

            /** @var CrateKey $cItem */
            $cItem = CustomItemManager::getInstance()->get(CustomItem::CRATEKEY);
            $item = $cItem->getVariant(99);
            $item->setCount(4);
            foreach(Server::getInstance()->getOnlinePlayers() as $player){
                $user = UserManager::getInstance()->getOnline($player);
                if($user === null) {
                    continue;
                }

                $user->safeGive($item);
            }
        }else{
            Server::getInstance()->broadcastMessage("§r§8[§bNF§8] §r§7Vote party will start in §b".(VoteParty::PARTY-VoteParty::getInstance()->getVotes())." votes§r§7.");
        }
    }

    public function getLastVote() : int{
        return $this->lastVote;
    }

    public function setLastVote($lastVote) : void {
        $this->lastVote = $lastVote;
    }

    public function getVotes() : int {
        return $this->votes;
    }

    public function setVotes(int $votes) : void {
        $this->votes = $votes;
    }

    public function addVote() : void {
        ++$this->votes;
    }

    public function getKitCooldowns() : array {
        return $this->kitCooldowns;
    }

    public function setKitCooldowns(array $cooldowns) : void {
        $this->kitCooldowns = $cooldowns;
    }

    public function hasReceivedStartItems() : bool {
        return $this->hasReceivedStartItems;
    }

    public function setReceivedStartItems(bool $bool) : void {
        $this->hasReceivedStartItems = $bool;
    }

    public function getShopPurchases() : array {
        return $this->shopPurchases;
    }

    public function setShopPurchases(array $array) : void {
        $this->shopPurchases = $array;
    }

    public function addShopPurchase(ShopItem $item) : void {
        if(!isset($this->shopPurchases[$item->fullId()])) {
            $this->shopPurchases[$item->fullId()] = 0;
        }

        ++$this->shopPurchases[$item->fullId()];
    }

    public function hasShopPurchased(ShopItem $item) : bool {
        return isset($this->shopPurchases[$item->fullId()]);
    }

    public function getKDR() : float {
        if($this->deaths === 0){
            return $this->kills;
        }
        return $this->kills / $this->deaths;
    }

    public function getKills() : int {
        return $this->kills;
    }

    public function setKills(int $kills) : void {
        $this->kills = $kills;
    }

    public function addKill() : void {
        ++$this->kills;
    }

    public function getDeaths() : int {
        return $this->deaths;
    }

    public function setDeaths(int $deaths) : void {
        $this->deaths = $deaths;
    }

    public function addDeath() : void {
        ++$this->deaths;
    }

    public function getGangId() : string {
        return $this->gangId;
    }

    public function setGangId(string $id) : void {
        $this->gangId = $id;
    }

    public function getGang() : ?Gang {
        return GangManager::getInstance()->get($this->gangId);
    }

    public function isInGang() : bool {
        return $this->getGang() !== null;
    }

    public function setGang(?Gang $gang) : void {
        if($gang === null){
            $this->gangId = "";
            return;
        }
        $this->gangId = $gang->getId();
    }

    public function getGangRank() : ?GangRank{
        return $this->gangRank;
    }

    public function getGangRankName() : string{
        if($this->gangRank === null){
            return "";
        }
        return $this->gangRank->name();
    }

    public function setGangRank(?GangRank $rank) : void{
        $this->gangRank = $rank;
    }

    public function hasBlocked(string $user) : bool {
        return in_array(strtolower($user), $this->blockedUsers);
    }

    public function addBlockedUser(string $name) : void {
        if(!$this->hasBlocked($name)){
            $this->blockedUsers[] = strtolower($name);
        }
    }

    public function removeBlockedUser(string $name) : void {
        if(!$this->hasBlocked($name)) {
            return;
        }

        unset($this->blockedUsers[array_search(strtolower($name), $this->blockedUsers)]);
    }

    public function getBlockedUsers() : array {
        return $this->blockedUsers;
    }

    public function setBlockedUsers(array $users) : void {
        $this->blockedUsers = $users;
    }

    public function countPlots() : int {
        $c = 0;
        foreach(PlotManager::getInstance()->getAll() as $plot){
            if(strtolower($plot->getOwner()) === strtolower($this->getName())) {
                ++$c;
            }
        }

        return $c;
    }

    /**
     * @return array|Plot[]
     */
    public function getOwnedPlots() : array {
        $return = [];
        foreach(PlotManager::getInstance()->getAll() as $plot){
            if(strtolower($plot->getOwner()) === strtolower($this->getName())) {
                $return[] = $plot;
            }
        }

        return $return;
    }

    public function getEffectiveMaxPlots() : int {
        return $this->getMaxPlots() + ($this->getRankComponent()->isDonator() ? $this->getRankComponent()->getDonatorRank()->getRank()->getPlots() : 0);
    }

    public function getMaxPlots() : int {
        return $this->maxPlots;
    }

    public function setMaxPlots(int $max) : void {
        $this->maxPlots = $max;
    }

    public function getMaxAuc() : int {
        return $this->maxAuc;
    }

    public function setMaxAuc(int $max) : void {
        $this->maxAuc = $max;
    }

    public function addExpiredAuctionItem(AuctionItem $item) : void {
        $this->expiredAuctionItems[] = $item;
    }

    /**
     * @return array|AuctionItem[]
     */
    public function getExpiredAuctionItems() : array {
        return $this->expiredAuctionItems;
    }

    public function hasExpiredAuctionItems() : bool{
        return count($this->expiredAuctionItems) !== 0;
    }

    public function setExpiredAuctionItems(array $aucItems) : void {
        $this->expiredAuctionItems = $aucItems;
    }

    public function hasTag(Tag $tag) : bool {
        return array_key_exists($tag->getId(), $this->tags);
    }

    public function grantTagSilent(Tag $tag) : void {
        $this->tags[$tag->getId()] = $tag;
    }

    public function getTagColor() : string {
        return $this->tagColor;
    }

    public function setTagColor(string $color) : void {
        $this->tagColor = $color;
    }

    //this is handled by BannedUserManager
/*    public function getBanExpiry() : int {
        return $this->banExpire;
    }

    public function setBanExpiry(int $expire) : void {
        $this->banExpire = $expire;
    }

    public function isBanned() : bool {
        return $this->isBanned;
    }

    public function setBanned(bool $banned = true) : void {
        $this->isBanned = $banned;
    }*/

    public function getMuteExpire() : int {
        return $this->muteExpire;
    }

    public function setMuteExpire(int $expire) : void {
        $this->muteExpire = $expire;
    }

    public function getIPList() : StringSet {
        return $this->IPList;
    }

    public function setIPList(StringSet $list) : void {
        $this->IPList = $list;
    }

    public function registerIP(string $ip) : void {
        $this->IPList->add($ip);
    }

    public function getDeviceIdList() : StringSet {
        return $this->deviceIdList;
    }

    public function setDeviceIdList(StringSet $list) : void {
        $this->deviceIdList = $list;
    }

    public function registerDeviceId(string $deviceId) : void {
        $this->deviceIdList->add($deviceId);
    }

    public function getClientIdList() : StringSet {
        return $this->clientIdList;
    }

    public function setClientIdList(StringSet $list) : void {
        $this->clientIdList = $list;
    }

    public function registerClientId(string $clientId) : void {
        $this->clientIdList->add($clientId);
    }

    public function getSelfSignedIdList() : StringSet {
        return $this->selfSignedIdList;
    }

    public function setSelfSignedIdList(StringSet $list) : void {
        $this->selfSignedIdList = $list;
    }

    public function registerSelfSignedId(string $selfSignedId) : void {
        $this->selfSignedIdList->add($selfSignedId);
    }

    public function addBooster(Booster $booster) : void {
        //todo
    }

    public function removeBooster(Booster $booster) : void {
        //todo
    }

    public function getBoosters() : array {
        return $this->boosters;
    }

    public function getCommandSpy() : bool {
        return $this->commandSpy;
    }

    public function setCommandSpy(bool $commandSpy) : void {
        $this->commandSpy = $commandSpy;
    }

    public function hasCooldownFor(string $commandName, float $cooldownSeconds) : bool {
        return isset($this->cooldowns[$commandName]) && $this->cooldowns[$commandName] + $cooldownSeconds > microtime(true);
    }

    public function setCooldownFor(string $commandName) : void {
        $this->cooldowns[$commandName] = microtime(true);
    }

    public function getCooldownFor(string $commandName, float $cooldownSeconds) : float {
        return max($this->cooldowns[$commandName] + $cooldownSeconds - microtime(true), 0);
    }

    public function getCooldowns() : array {
        return $this->cooldowns;
    }

    public function setCooldowns(array $cooldowns) : void {
        $this->cooldowns = $cooldowns;
    }

    public function getVault() : Vault {
        return VaultManager::getInstance()->get($this);
    }

    public function getTotalEarnedMoney() : float {
        return $this->totalEarned;
    }

    public function setTotalEarnedMoney(float $money) : void {
        $this->totalEarned = $money;
    }

    public function isFlying() : bool {
        return $this->fly;
    }

    public function setFly(bool $fly) : void {
        $this->fly = $fly;
        $player = $this->getPlayer();
        if(!$player instanceof Player) {
            return;
        }

        $player->setAllowFlight($fly);
        $player->setFlying($fly);
    }

    public function getNick() : string {
        return $this->nick;
    }

    public function setNick(string $nick = "") : void {
        $this->nick = $nick;
        $player = $this->getPlayer();
        if(!$player instanceof Player) {
            return;
        }

        $player->setDisplayName($nick !== "" ? $nick : $player->getName());
    }

    public function hasNick() : bool {
        return $this->nick !== "";
    }

    public function getAchievements() : array {
        return $this->achievements;
    }

    public function setAchievements(array $achievements) : void {
        $this->achievements = $achievements;
    }

    public function isMuted() : bool {
        return $this->muted;
    }

    public function setMuted(bool $muted) : void {
        $this->muted = $muted;
    }

    public function hasAppliedTag() : bool {
        return $this->appliedTag !== "";
    }

    public function getAppliedTag() : string {
        return $this->appliedTag;
    }

    public function setAppliedTag(string $tag) : void {
        $this->appliedTag = $tag;
    }

    public function getPrestigePoints() : int {
        return $this->prestigePoints;
    }

    public function setPrestigePoints(int $points) : void {
        $this->prestigePoints = $points;
    }

    /**
     * @return array|Tag[]
     */
    public function getTags() : array {
        return $this->tags;
    }

    public function setTags(array $tags) : void {
        $this->tags = $tags;
    }

    public function getPrestige() : int {
        return $this->prestige;
    }

    public function setPrestige(int $prestige) : void {
        $this->prestige = $prestige;
    }

    public function getRank() : RankInstance {
        return $this->rankComponent->getMainRank();
    }

    public function getMineRankId() : int {
        return $this->mineRank;
    }

    public function getMineRank() : MineRank {
        return MineRankManager::getInstance()->get($this->mineRank);
    }

    public function setMineRank(MineRank $rank) : void {
        $this->mineRank = $rank->getId();
    }

    public function getMinedBlocks() : int {
        return $this->minedBlocks;
    }

    public function setMinedBlocks(int $minedBlocks) : void {
        $this->minedBlocks = $minedBlocks;
    }

    public function addMinedBlock() : void {
        ++$this->minedBlocks;
    }

    public function getPlayer() : IPlayer{
        return Server::getInstance()->getOfflinePlayer($this->name);
    }

    public function getBalance() : int {
        return (int)$this->balance;
    }

    public function setBalanceSilent(int $balance): void{
        $this->balance = $balance;
    }

    public function setBalance(int $balance) : void{
        $change = $balance - $this->balance;
        $this->setBalanceSilent((int)$balance);
        $this->onBalanceChange((int)$change);
    }

    public function takeMoney(int $money) : void {
        $this->balance -= $money;
        $this->onBalanceChange((int)$money*-1);
    }

    public function addMoney(int $amount) : void {
        $this->balance += $amount;
        $this->onBalanceChange((int)$amount);
    }

    public function onBalanceChange(int $changedAmount) : void {}

    public function isOnline() : bool {
        return false;
    }

    public function getName() : string {
        return $this->name;
    }
}