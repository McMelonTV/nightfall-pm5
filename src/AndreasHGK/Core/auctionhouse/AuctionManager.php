<?php

namespace AndreasHGK\Core\auctionhouse;

use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\FileUtils;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class AuctionManager {

    private static $instance;

    /**
     * @var array|AuctionItem[]
     */
    private $auctionItems = [];

    private ?Webhook $webhook = null;

    public function expire(string $seller, string $id) : void {
        $auc = $this->get($seller, $id);
        $this->remove($seller, $id);

        /*$embed = new Embed();
        $embed->setTitle("Auction House");
        $embed->setDescription("Item Expired!");
        $embed->setColor(745356);
        $embed->addField("ID", $id, true);
        $embed->addField("Seller", $auc->getSeller(), true);
        $embed->addField("Price", "$" . $auc->getPrice(), true);

        $item = $auc->getItem();
        $embed->addField("Count", $auc->getItem()->getCount(), true);
        $embed->addField("Item Name", TextFormat::clean($item->getName()), true);

        $item = $auc->getItem();
        $desc = "";
        foreach($item->getLore() as $lore){
            $desc .= "\n" . TextFormat::clean($lore);
        }
        $embed->addField("Item Description", $desc);

        $this->webhookMessage("", $embed);*/

        $user = UserManager::getInstance()->get(Server::getInstance()->getOfflinePlayer($seller));
        $user->addExpiredAuctionItem($auc);
        if($user instanceof User){
            $user->getPlayer()->sendMessage("§r§b§l> §r§7One of your auction items expired.");
        }else{
            UserManager::getInstance()->save($user);
        }
    }

    public function countItems() : int {
        return count($this->getAllArray());
    }

    public function countPages() : int {
        return ceil($this->countItems()/45);
    }

    public function remove(string $sender, string $id) : void {
        if(isset($this->auctionItems[$sender][$id])){
            unset($this->auctionItems[$sender][$id]);
            //$this->webhookMessage("");
        }
    }

    public function addItem(AuctionItem $item) : void {
        $this->auctionItems[$item->getSeller()][$item->getId()] = $item;
    }

    /**
     * @return array[]|AuctionItem[][]
     */
    public function getAll() : array {
        return $this->auctionItems;
    }

    public function setAll(array $auc) : void {
        $this->auctionItems = $auc;
    }

    /**
     * @return array|AuctionItem[]
     */
    public function getAllArray() : array {
        $array = [];
        foreach($this->getAll() as $seller => $sellerArray){
            foreach($sellerArray as $id => $item){
                $array[] = $item;
            }
        }

        return $array;
    }

    /**
     * @param string $seller
     *
     * @return array|AuctionItem[]
     */
    public function getAllSellerItems(string $seller) : array {
        return isset($this->auctionItems[$seller]) ? $this->auctionItems[$seller] : [];
    }

    public function setAllSellerItems(string $seller, array $items) : void {
        $this->auctionItems[$seller] = $items;
    }

    public function get(string $seller, string $id) : ?AuctionItem {
        return $this->auctionItems[$seller][$id] ?? null;
    }

    public function getByFullId(string $fullId) : ?AuctionItem{
        $fullId = explode(":", $fullId);
        $seller = array_shift($fullId);
        $id = array_shift($fullId);
        return $this->auctionItems[$seller][$id] ?? null;
    }

    public function exists(string $fullId) : bool {
        return $this->getByFullId($fullId) !== null;
    }

    public function webhookMessage(string $message, ?Embed $embed = null) : void{
        if($this->webhook === null){
            return;
        }

        $msg = new Message();
        $msg->setAvatarURL("");
        $msg->setUsername("Auction House Logger");
        $msg->setContent($message);
        if($embed !== null){
            $msg->addEmbed($embed);
        }

        $this->webhook->send($msg);
    }

    public function loadAll() : void {
        $webhook = DataManager::getKey(FileUtils::MakeJSON("auction"),  "webhook", "");
        if($webhook !== "") {
            $this->webhook = new Webhook($webhook);
        }

        $items = DataManager::getKey(FileUtils::MakeJSON("auction"),  "auction", []);
        foreach($items as $seller => $sellerItems){
            foreach($sellerItems as $id => $item){
                $this->load($seller, $id);
            }
        }
    }

    public function load(string $seller, string $itemId) : ?AuctionItem {
        $itemData = DataManager::getKey(FileUtils::MakeJSON("auction"), "auction")[$seller][$itemId];
        $id = $itemData["id"];
        $seller = $itemData["seller"];
        $sellTime = $itemData["sellTime"];
        $item = Item::jsonDeserialize($itemData["item"]);
        $price = $itemData["price"];
        $tag = new AuctionItem($id, $item, $seller, $sellTime, $price);
        $this->auctionItems[$seller][$id] = $tag;
        return $tag;
    }

    public function saveAll() : void {
        $file = DataManager::get(DataManager::AUCTION);
        $auc = [];
        foreach($this->auctionItems as $seller => $sellerItems){
            $sellerArray = [];
            foreach($sellerItems as $id => $auctionItem){
                if(!$auctionItem instanceof AuctionItem) {
                    continue;
                }

                $data = [];
                $data["id"] = $auctionItem->getId();
                $data["seller"] = $auctionItem->getSeller();
                $data["sellTime"] = $auctionItem->getSellTime();
                $data["item"] = $auctionItem->getItem()->jsonSerialize();
                $data["price"] = $auctionItem->getPrice();
                $sellerArray[$auctionItem->getId()] = $data;
            }

            $auc[$seller] = $sellerArray;
        }

        $file->set("auction", $auc);
        $file->save();
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}