<?php

declare(strict_types=1);

namespace AndreasHGK\Core\gang;

use AndreasHGK\Core\user\OfflineUser;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\FileUtils;
use AndreasHGK\Core\utils\StringSet;
use pocketmine\player\OfflinePlayer;
use pocketmine\player\Player;
use pocketmine\Server;

class Gang{

    /** @var string */
    protected $id;
    /** @var string */
    protected $name;
    /** @var int */
    protected $creationDate;
    /** @var string */
    protected $description = "A new gang!";
    /** @var string[] */
    protected $members = [];

    protected StringSet $allies;
    protected StringSet $allyRequests;

    protected StringSet $invites;

    public function __construct(string $id, string $name, int $creationDate, string $description, array $members, array $allies){
        $this->id = $id;
        $this->name = $name;
        $this->creationDate = $creationDate;
        $this->description = $description;
        $this->members = $members;
        $this->allies = StringSet::fromArray($allies);
        $this->allyRequests = new StringSet();
        $this->invites = new StringSet();
    }

    public function getMemberCount() : int {
        return count($this->members);
    }

    public function hasInvite(Player $player): bool{
        return $this->invites->contains(strtolower($player->getName()));
    }

    public function invitePlayer(Player $player): void{
        $this->invites->add(strtolower($player->getName()));
    }

    public function revokeInvite(Player $player): void{
        $this->invites->remove(strtolower($player->getName()));
    }

    public function addMember(Player $member): void{
        $this->members[] = strtolower($member->getName());
        $user = UserManager::getInstance()->getOnline($member);
        $user->setGang($this);
        $user->setGangRank(GangRank::RECRUIT());
    }

    public function removeMember(string $name) : void{
        unset($this->members[array_search(strtolower($name), $this->members)]);
        $player = Server::getInstance()->getOfflinePlayer($name);
        $user = UserManager::getInstance()->get($player);
        $user->setGang(null);
        $user->setGangRank(null);
        if(!$user->isOnline()) {
            UserManager::getInstance()->save($user);
        }
    }

    public function getMember(string $member): ?OfflineUser{
        $member = strtolower($member);
        foreach ($this->members as $m) {
            if(strtolower($m) === $member){
                $player = Server::getInstance()->getOfflinePlayer($m);
                if($player === null){
                    return null;
                }
                return UserManager::getInstance()->get($player);
            }
        }
        return null;
    }

    public function getMembers() : array{
        return $this->members;
    }

    public function getOnlineMembers(): array{
        $online = [];
        foreach($this->members as $member){
            if(($p = Server::getInstance()->getPlayerExact($member)) instanceof Player){
                $online[] = $p;
            }
        }
        return $online;
    }

    public function getLeader() : ?OfflineUser{
        foreach($this->members as $member){
            $player = Server::getInstance()->getOfflinePlayer($member);
            if($player === null){
                continue;
            }
            $user = UserManager::getInstance()->get($player);
            if($user->getGangRank()->equals(GangRank::LEADER())){
                return $user;
            }
        }
        return null;
    }

    public function getAllies() : array {
        return $this->allies->toArray();
    }

    public function setAllies(StringSet $allies) : void{
        $this->allies = $allies;
    }

    public function isAlliedWith(Gang $gang) : bool{
        return $this->allies->contains(strtolower($gang->getName()));
    }

    public function allyWith(Gang $gang) : void{
        if($this->askedToAllyWith($gang)){
            $this->allyRequests->remove(strtolower($gang->getName()));
        }
        if(!$this->isAlliedWith($gang)){
            $this->allies->add(strtolower($gang->getName()));
            $gang->allyWith($this);
        }
    }

    public function removeAlly(Gang $gang) : void{
        if($this->isAlliedWith($gang)) {
            $this->allies->remove(strtolower($gang->getName()));
            $gang->removeAlly($this);
        }
    }

    public function askedToAllyWith(Gang $gang) : bool{
        return $this->allyRequests->contains(strtolower($gang->getName()));
    }

    public function askToAllyWith(Gang $gang) : void{
        $this->allyRequests->add(strtolower($gang->getName()));
    }

    public function getId() : string{
        return $this->id;
    }

    public function getCreationDate() : int{
        return $this->creationDate;
    }

    public function getDescription() : string{
        return $this->description;
    }

    public function setDescription(string $description) : void{
        $this->description = $description;
    }

    public function getName() : string{
        return $this->name;
    }

    public function setName(string $name) : void{
        $this->name = $name;
    }

    public function getFileName() : string{
        return FileUtils::MakeJSON($this->id);
    }

    public function __toString() : string{
        return $this->name;
    }
}