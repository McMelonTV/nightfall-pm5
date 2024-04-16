<?php

namespace AndreasHGK\Core\user;

use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\utils\FileUtils;
use AndreasHGK\Core\utils\StringSet;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;

class BannedUserManager {

    private static $instance;

    /**
     * @var array|BannedUser[]
     */
    private $bans = [];

    private StringSet $names;
    private StringSet $deviceIds;
    private StringSet $clientIds;
    private StringSet $selfSignedIds;
    private StringSet $ips;

    public function ban(OfflineUser $user, string $reason, int $duration = -1, string $banner = null) : BannedUser {
        $time = time();
        if($duration > 0){
            $expire = $time+$duration;
        }else{
            $expire = -1;
        }

        $ban = new BannedUser($user->getPlayer()->getName(), $time, $reason, $expire);
        if($banner !== null) {
            $ban->setBanner($banner);
        }

        $this->bans[strtolower($ban->getName())] = $ban;
        /*$this->names->add($user->getName());
        $this->deviceIds->add(...$user->getDeviceIdList()->toArray());
        $this->clientIds->add(...$user->getClientIdList()->toArray());
        $this->selfSignedIds->add(...$user->getSelfSignedIdList()->toArray());
        $this->ips->add(...$user->getIPList()->toArray());*/

        $p = $user->getPlayer();
        if($p instanceof Player){
            $p->kick("§r§8[§bNF§8]\n§r§7You have been banned from the server by §b".$ban->getBanner()."§r§7!\n§r§7Reason: §b".$reason."\n§r§7Expiration date: §b".($duration > 0 ? date("d/m/Y", $expire)." at ".date("h:i:s", $expire) : "never"), false);
        }

        return $ban;
    }

    public function unban(string $username) : void {
        unset($this->bans[strtolower($username)]);
    }

    public function isBanned(string $username) : bool {
        return isset($this->bans[strtolower($username)]);
    }

    public function isBannedCheckAll(PlayerInfo $info, string $ip) : bool {
        $username = $info->getUsername();

        if($this->names->contains(strtolower($username))){
            return true;
        }

        $player = Server::getInstance()->getOfflinePlayer($username);
        if($player !== null) {
            $user = UserManager::getInstance()->get($player);
        }

        $extraData = $info->getExtraData();
        if($this->deviceIds->contains((string) $extraData["DeviceId"])){
            return true;
        }

        if($this->clientIds->contains((string) $extraData["ClientRandomId"])){
            return true;
        }

        if($this->selfSignedIds->contains((string) $extraData["SelfSignedId"])){
            return true;
        }

        if($this->ips->contains($ip)){
            return true;
        }

        if(isset($user)){
            foreach($user->getIPList()->toArray() as $ip){
                if($this->ips->contains($ip)){
                    return true;
                }
            }

            foreach($user->getDeviceIdList()->toArray() as $did){
                if($this->deviceIds->contains($did)){
                    return true;
                }
            }

            foreach($user->getClientIdList()->toArray() as $cid){
                if($this->clientIds->contains($cid)){
                    return true;
                }
            }

            foreach($user->getSelfSignedIdList()->toArray() as $ssid){
                if($this->selfSignedIds->contains($ssid)){
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array|BannedUser[]
     */
    public function getAll() : array {
        return $this->bans;
    }

    public function get(string $id) : ?BannedUser {
        return $this->bans[strtolower($id)] ?? null;
    }

    public function loadAll() : void {
        $bans = DataManager::getKey(FileUtils::MakeJSON("bans"),  "bans", []);
        foreach($bans as $name => $ban){
            $this->load($name);
        }
    }

    public function load(string $tagId) : ?BannedUser {
        //if($file === "." || $file === "..") return null;
        $banData = DataManager::getKey(FileUtils::MakeJSON("bans"), "bans")[$tagId];
        $bannedUser = new BannedUser($banData["name"], $banData["banDate"], $banData["reason"], $banData["expire"]);
        $bannedUser->setBanner($banData["banner"]);
        $bannedUser->setSuperban($banData["superban"] ?? false);
        $this->bans[strtolower($banData["name"])] = $bannedUser;
        return $bannedUser;
    }

    public function saveAll() : void {
        $file = DataManager::get(DataManager::BANS);
        $banData = [];
        foreach($this->bans as $banName => $ban){
            $data = [];
            $data["name"] = $ban->getName();
            $data["reason"] = $ban->getReason();
            $data["banDate"] = $ban->getBanDate();
            $data["expire"] = $ban->getBanExpire();
            $data["banner"] = $ban->getBanner();
            $data["superban"] = $ban->getSuperban() ?? false;
            $banData[$ban->getName()] = $data;
        }
        $file->set("bans", $banData);
        $file->save();
    }

    public function exist(string $tag) : bool {
        return isset($this->bans[strtolower($tag)]);
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}