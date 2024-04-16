<?php

declare(strict_types=1);

namespace AndreasHGK\Core\user;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\item\CrateKey;
use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\lottery\Lottery;
use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\plot\Plot;
use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\pvp\PVPZoneManager;
use AndreasHGK\Core\rank\MineRankManager;
use AndreasHGK\Core\tag\Tag;
use AndreasHGK\Core\tag\TagManager;
use AndreasHGK\Core\utils\IntUtils;
use AndreasHGK\Core\utils\MineUtils;
use AndreasHGK\Core\utils\TagUtils;
use AndreasHGK\Core\warning\Warning;
use muqsit\invmenu\InvMenu;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\permission\PermissionAttachment;
use pocketmine\player\IPlayer;
use pocketmine\player\OfflinePlayer;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\sound\Sound;
use function time;

class User extends OfflineUser {

    /** @var Player */
    private $player;

    public $barChange = 0;

    /** @var InvMenu|null */
    protected $shopInv = null;

    /** @var string|null */
    protected $category = null;

    protected $lastActivity = 0;

    public $activity = true;

    protected $afkStatus = false;

    protected $lastHitter = null;

    protected $lastHit = 0;

    protected $lastMsgSender = null;

    protected $adminMode = false;

    protected $waitingForCommand = false;

    /** @var TaskHandler */
    protected $commandDelayTask;

    protected $aucInv = null;

    protected $aucPage = 0;

    protected $viewingExpiredAuc = false;

    protected $viewingOwnItems = false;

    /**
     * @var PermissionAttachment
     */
    protected $permissionAttachment;

    protected $lastRelic = 0;

    protected $lastMessage = "";

    protected $spamScore = 0;

    protected $lastMessageTime = 0;

    protected int $joinTime = 0;

    public function __construct(IPlayer $player, array $data) {
        parent::__construct($player, $data);
        $this->player = $player;
    }

    public function getJoinTime() : int{
        return $this->joinTime;
    }

    public function setJoinTime(int $joinTime) : void{
        $this->joinTime = $joinTime;
    }

    public function getSessionTime() : int{
        return time() - $this->joinTime;
    }

    /**
     * Change the vanished state
     *
     * @param bool $vanished
     */
    public function setVanished(bool $vanished) : void {
        parent::setVanished($vanished);
        if($vanished) {
            Server::getInstance()->removeOnlinePlayer($this->getPlayer());
        }else {
            Server::getInstance()->addOnlinePlayer($this->getPlayer());
        }
    }

    /**
     * @param Warning $warning
     */
    public function warn(Warning $warning) : void {
        parent::warn($warning);
        $this->getPlayer()->sendMessage("§r§4§l> §r§7You have been warned by §r§4{$warning->getStaffName()}§r§7 with reason §r§4{$warning->getReason()}§r§7. "
        ."§r§7You now have §r§4{$this->countValidWarns()} §r§7warnings.");
    }

    public function getPrestigeBoost() : float {
        return 1 + ($this->getPrestige() - 1) * 0.1;
        //return $this->getPrestige() + ($this->getPrestige() - 1) * 0.0685;
    }

    public function getLastMessageTime() : float {
        return $this->lastMessageTime;
    }

    public function setLastMessageTime(float $time) : void {
        $this->lastMessageTime = $time;
    }

    public function getLastMessage() : string {
        return $this->lastMessage;
    }

    public function setLastMessage(string $msg) : void {
        $this->lastMessage = $msg;
    }

    public function canFly() : bool{
        return $this->getPlayer()->hasPermission("nightfall.command.fly");
    }

    public function getSpamScore() : float {
        return $this->spamScore;
    }

    public function setSpamScore(float $spamScore) : void {
        $this->spamScore = $spamScore;
    }

    public function addSpamScore(float $spamScore) : void {
        $this->spamScore += $spamScore;
    }

    public function safeGiveMultiple(array $items) : void {
        $failed = 0;
        foreach($items as $item){
            if(!$this->safeGiveSilent($item)) {
                ++$failed;
            }
        }

        if($failed > 0){
            $this->getPlayer()->sendMessage("§r§b§l>§r§b $failed §r§7items have been sent to your §b/vault §r§7because your inventory was full.");
        }
    }

    public function safeGive(Item $item) : void {
        if(!$this->safeGiveSilent($item)){
            $this->getPlayer()->sendMessage("§r§b§l> §r§7An item has been sent to your §b/vault §r§7because your inventory was full.");
        }
    }

    public function safeGiveSilent(Item $item) : bool {
        $p = $this->getPlayer();
        if($p->getInventory()->canAddItem($item)){
            $p->getInventory()->addItem($item);
            return true;
        }

        $this->getVault()->addItem($item);
        return false;
    }

    public function castVote(): void{
        parent::castVote();

        /** @var CrateKey $key */
        $key = CustomItemManager::getInstance()->get(CustomItem::CRATEKEY);

        $this->safeGive($key->getVariant(99)->setCount(2));
        $this->setPrestigePoints($this->getPrestigePoints()+250);
        $this->grantRandomTag();
        Lottery::getInstance()->buyTickets($this, 1);
        $this->getPlayer()->sendMessage("§r§b§l> §r§7You have been given §b2 vote keys§7, §b250 prestige points§7, §b1 lottery ticket§r§7 and a §brandom tag§r§7 for voting for the server!");
        $this->freeRankup();
    }

    public function freeRankup() : bool {
        $rankFrom = $this->getMineRank();
        $rankTo = MineRankManager::getInstance()->get($rankFrom->getId()+1);
        if(!isset($rankTo)){
            return false;
        }

        $this->setMineRank($rankTo);
        $this->getPlayer()->sendMessage("§b§l> §r§7You have been ranked up to §b".TextFormat::colorize($rankTo->getTag()).'§r§7 for §bfree§r§7!');
        $pk = LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_LEVELUP, $this->getPlayer()->getPosition(), 0x10000000 * intdiv(30, 5));
        $this->getPlayer()->getNetworkSession()->sendDataPacket($pk);
        return true;
    }

    public function getLastRelic() : int {
        return $this->lastRelic;
    }

    public function setLastRelic(int $lastRelicTime) : void {
        $this->lastRelic = $lastRelicTime;
    }

    public function playSound(Sound $sound) : void {
        $player = $this->getPlayer();
        if($player instanceof OfflinePlayer) return;

        foreach($sound->encode($player->getPosition()->asVector3()) as $pk) {
            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }

    public function grantRandomTag() : void {
        $chanceArray = [];
        foreach (TagManager::getInstance()->getAll() as $tag){
            if(!$tag->isCrateDrop()) {
                continue;
            }

            $chanceArray = array_merge($chanceArray, array_fill(0, $tag->getWeight()*10, $tag));
        }

        shuffle($chanceArray);

        /** @var Tag $tag */
        $tag = $chanceArray[array_rand($chanceArray)];

        if(!$tag->isCrateDrop()){
            foreach($chanceArray as $chance){
                if(!$tag->isCrateDrop()) {
                    continue;
                }

                $tag = $chance;
                break;
            }
        }

        $this->grantTag($tag);
    }

    public function sendTip(string $tip) : void {
        $this->getPlayer()->sendTip($tip);
        $this->getPlayer()->sendPopup("");
        $this->barChange = time();
    }

    /**
     * @return array|Plot[]
     */
    public function getAccessiblePlots() : array {
        $return = [];
        foreach(PlotManager::getInstance()->getAll() as $plot){
            if($plot->hasAccess($this)) {
                $return[] = $plot;
            }
        }

        return $return;
    }

    public function getViewingOwnItems() : bool {
        return $this->viewingOwnItems;
    }

    public function setViewingOwnItems(bool $bool) : void {
        $this->viewingOwnItems = $bool;
    }

    public function getViewingExpiredAuc() : bool {
        return $this->viewingExpiredAuc;
    }

    public function setViewingExpiredAuc(bool $bool) : void {
        $this->viewingExpiredAuc = $bool;
    }

    public function getAucInv() : ?InvMenu {
        return $this->aucInv;
    }

    public function setAucInv(InvMenu $aucInv) : void {
        $this->aucInv = $aucInv;
    }

    public function getAucPage() : int {
        return $this->aucPage;
    }

    public function setAucPage(int $aucPage) : void {
        $this->aucPage = $aucPage;
    }

    public function clearLastHit() : void {
        $this->lastHit = 0;
    }

    public function getPermissionAttachment() : PermissionAttachment {
        return $this->permissionAttachment;
    }

    public function setPermissionAttachment() : void {
        $this->permissionAttachment = $this->getPlayer()->addAttachment(Core::getInstance(), "nightfallAttachment");
    }

    public function finishCommandDelay() : void {
        if(isset($this->commandDelayTask)) unset($this->commandDelayTask);
        $this->waitingForCommand = false;
    }
    /**
     * Cancel and remove the current CommandDelayTask
     */
    public function cancelCommandDelayTask() : void {
        if(!isset($this->commandDelayTask)) return;
        $this->commandDelayTask->cancel();
        unset($this->commandDelayTask);
    }

    public function getCommandDelayTask() : TaskHandler {
        return $this->commandDelayTask;
    }

    public function setCommandDelayTask(TaskHandler $taskHandler) : void {
        $this->commandDelayTask = $taskHandler;
    }

    public function isWaitingForCommand() : bool {
        return $this->waitingForCommand;
    }

    public function setWaitingforCommand(bool $bool) : void {
        $this->waitingForCommand = $bool;
    }

    public function grantTag(Tag $tag) : void {
        $player = $this->getPlayer();
        if($this->hasTag($tag)){
            $pp = $tag->getRarity()*50;
            $this->setPrestigePoints($this->getPrestigePoints()+$pp);
            $player->sendMessage("§r§b§l> §r§7You have received a duplicate §r §r".$tag->getTag()."§7 and received§b $pp §7prestige points.");
            return;
        }

        $this->grantTagSilent($tag);
        $player->sendMessage("§r§b§l> §r§7You have received the §r".$tag->getTag()."§r§7 tag with ".TagUtils::rarityColor($tag->getRarity()).TagUtils::rarityName($tag->getRarity())."§r§7 rarity.");
    }

    public function teleportToSpawn() : void {
        $vec = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->add(0.5, 0.5, 0.5);
        $pos = new Position($vec->getX(), $vec->getY(), $vec->getZ(), Server::getInstance()->getWorldManager()->getDefaultWorld());
        $this->getPlayer()->teleport($pos);
    }

    public function tryPrestige() : void {
        $rankFrom = $this->getMineRank();

        $rankTo = MineRankManager::getInstance()->get($rankFrom->getId()+1);
        if(isset($rankTo)){
            $this->getPlayer()->sendMessage("§c§l> §r§7You need to be in the last mine to be able to prestige!");
            return;
        }

        $prestige = $this->getPrestige();
        $prestigeTo = $prestige+1;

        $price = MineUtils::getPrestigePrice($prestigeTo);
        if($this->getBalance() < $price){
            $this->getPlayer()->sendMessage("§c§l> §r§7You don't have enough money to rank up to prestige §c".$prestigeTo."§r§7. You require §c$".IntUtils::shortNumberRounded($price)."§r§7.");
            return;
        }

        $this->takeMoney($price);
        $this->setBalance(0);
        $this->setPrestige($prestigeTo);
        $this->setMineRank(MineRankManager::getInstance()->get(0));

        //$this->teleportToSpawn();

        $reward = MineUtils::getPrestigeReward($prestigeTo);
        $this->setPrestigePoints($this->getPrestigePoints()+$reward);
        $this->getPlayer()->sendMessage("§b§l> §r§7You have been ranked up to prestige§b ".IntUtils::toRomanNumerals($prestigeTo)."§r§7 for §b$".IntUtils::shortNumberRounded($price)."§r§7 and have received §b".$reward."§opp§r§7!");
        $pk = LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_LEVELUP, $this->getPlayer()->getPosition(), 0x10000000 * intdiv(30, 5));
        $this->getPlayer()->getNetworkSession()->sendDataPacket($pk);
    }

    public function tryRankUp() : bool {
        $rankFrom = $this->getMineRank();
        $rankTo = MineRankManager::getInstance()->get($rankFrom->getId()+1);
        if(!isset($rankTo)){
            $this->getPlayer()->sendMessage("§c§l> §r§7You have already reached the current max rank!");
            return false;
        }

        $price = (int)($rankTo->getPrice() + ($rankTo->getPrice() * 0.6 * ($this->getPrestige() - 1 )));
        if($this->getBalance() < $price){
            $this->getPlayer()->sendMessage("§c§l> §r§7You don't have enough money to rank up to §c".TextFormat::colorize($rankTo->getTag())."§r§7. You require §c$".IntUtils::shortNumberRounded($price)."§r§7.");
            return false;
        }

        $this->takeMoney($price);
        $this->setMineRank($rankTo);
        $this->getPlayer()->sendMessage("§b§l> §r§7You have been ranked up to §b".TextFormat::colorize($rankTo->getTag())."§r§7 for §b$".IntUtils::shortNumberRounded($price)."§r§7!");
        $pk = LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_LEVELUP, $this->getPlayer()->getPosition(), 0x10000000 * intdiv(30, 5));
        $this->getPlayer()->getNetworkSession()->sendDataPacket($pk);
        return true;
    }

    public function canBuildAt(Position $pos) : bool {
        if($this->adminMode) return true;
        if($pos->getWorld()->getDisplayName() !== PlotManager::$plotworld) return false;
        if(PlotManager::getInstance()->isClaimed($pos->getX(), $pos->getZ()) && PlotManager::getInstance()->getPlotAt($pos->getX(), $pos->getZ())->hasAccess($this)) return true;
        return false;
    }

    public function canInteractAt(Position $pos, int $id) : bool {
        if($this->adminMode) {
            return true;
        }

        switch ($id){
            case ItemIds::CHEST:
            case ItemIds::OAK_DOOR:
            case ItemIds::BIRCH_DOOR:
            case ItemIds::SPRUCE_DOOR:
            case ItemIds::DARK_OAK_DOOR:
            case ItemIds::ACACIA_DOOR:
            case ItemIds::IRON_DOOR:
            case ItemIds::JUNGLE_DOOR:
            case ItemIds::TRAPDOOR:
            case ItemIds::SPRUCE_TRAPDOOR:
            case ItemIds::JUNGLE_TRAPDOOR:
            case ItemIds::DARK_OAK_TRAPDOOR:
            case ItemIds::BIRCH_TRAPDOOR:
            case ItemIds::IRON_TRAPDOOR:
            case ItemIds::FURNACE:
            case ItemIds::BLAST_FURNACE:
            case ItemIds::SMOKER:
            case ItemIds::TRAPPED_CHEST:
                if($pos->getWorld()->getDisplayName() !== PlotManager::$plotworld) {
                    return true;
                }

                break;
            default:
                return true;
        }
        if($pos->getWorld()->getDisplayName() === PlotManager::$plotworld && PlotManager::getInstance()->isClaimed($pos->getX(), $pos->getZ()) && PlotManager::getInstance()->getPlotAt($pos->getX(), $pos->getZ())->hasAccess($this)) {
            return true;
        }
        return false;
    }

    public function canPvPAt(Position $pos) : bool {
        if($this->adminMode) {
            return true;
        }

        //if(PVPZoneManager::getInstance()->isPVPZone($pos->getX(), $pos->getY(), $pos->getZ(), $pos->getWorld())) return true;
        if(PVPZoneManager::getInstance()->getZoneAt($pos->getX(), $pos->getY(), $pos->getZ(), $pos->getWorld())) {
            return true;
        }

        return false;
    }

    public function canDestroyAt(Position $pos) : bool {
        if($this->adminMode) {
            return true;
        }

        /*foreach(MineManager::getInstance()->getAll() as $mine){
            if($mine->isInMine($pos->getX(), $pos->getY(), $pos->getZ(), $pos->getWorld()) && $mine->hasAccessTo($this)) {
                return true;
            }elseif($mine->isInMine($pos->getX(), $pos->getY(), $pos->getZ(), $pos->getWorld()) && !$mine->hasAccessTo($this)){
                return false;
            }
        }*/

        $mine = MineManager::getInstance()->getMineAt($pos->x, $pos->y, $pos->z, $pos->getWorld());
        if($mine !== null){
            return $mine->hasAccessTo($this);
        }

        if($pos->getWorld()->getDisplayName() !== PlotManager::$plotworld) {
            return false;
        }

        $plot = PlotManager::getInstance()->getPlotAt($pos->x, $pos->z);
        if($plot !== null && $plot->isClaimed()) {
            return $plot->hasAccess($this);
        }

        return false;
    }

    public function getAdminMode() : bool {
        return $this->adminMode;
    }

    public function setAdminMode(bool $adminMode) : void {
        $this->adminMode = $adminMode;
    }

    public function getLastMsgSender() : ?string {
        return $this->lastMsgSender;
    }

    public function setLastMsgSender(string $name) : void {
        $this->lastMsgSender = $name;
    }

    /**
     * @return Player|OfflinePlayer
     */
    public function getPlayer(): IPlayer{
        return $this->player;
    }

    public function getLastHitter() : ?string {
        return $this->lastHitter;
    }

    public function setlastHitter(string $lastHitter) : void {
        $this->lastHitter = $lastHitter;
    }

    public function getLastHit() : int {
        return $this->lastHit;
    }

    public function updateLastHit() : void {
        $this->lastHit = time();
    }

    public function isAFK() : bool {
        return $this->afkStatus;
    }

    public function setAFK(bool $afk = true) : void {
        $this->afkStatus = $afk;
    }

    public function getLastActivity() : int {
        return $this->lastActivity;
    }

    public function updateLastActivity() : void {
        $this->lastActivity = time();
    }

    public function getOpenCategory() : ?string {
        return $this->category;
    }

    public function setOpenCategory(?string $categoryName) : void {
        $this->category = $categoryName;
    }

    public function getShopInv() : ?InvMenu{
        return $this->shopInv;
    }

    public function setShopInv(?InvMenu $shopInv = null) : void {
        $this->shopInv = $shopInv;
    }

    public function onBalanceChange(int $changedAmount): void{
        switch (true){
            case $changedAmount > 0:
                $this->getPlayer()->sendPopup("§b$".IntUtils::shortNumberRounded(($this->getBalance()-$changedAmount))." §7+ §a$".IntUtils::shortNumberRounded($changedAmount));
                break;
            case $changedAmount < 0:
                $this->getPlayer()->sendPopup("§b$".(IntUtils::shortNumberRounded($this->getBalance()-$changedAmount))." §7- §c$".IntUtils::shortNumberRounded(abs($changedAmount)));
                break;
            default:
                $this->getPlayer()->sendPopup("§b$".IntUtils::shortNumberRounded($this->getBalance()));
                break;
        }

        // will be done later when I add player settings on the server
        //$this->getPlayer()->getNetworkSession()->sendDataPacket(BossEventPacket::show($this->getPlayer()->getId(), "Mine progress", 10));
    }

    public function isOnline() : bool {
        return $this->getPlayer()->isOnline();
    }
}
