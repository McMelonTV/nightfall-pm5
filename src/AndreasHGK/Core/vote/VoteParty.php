<?php

declare(strict_types=1);

namespace AndreasHGK\Core\vote;

use AndreasHGK\Core\manager\DataManager;
use pocketmine\utils\SingletonTrait;

class VoteParty {

    public const FILE = "vote_party.json";

    public const PARTY = 50;

    private $votes = 0;

    public function getVotes() : int {
        return $this->votes;
    }

    public function addVote(int $votes = 1) : void {
        $this->votes += $votes;
    }

    public function setVotes(int $votes) : void {
        $this->votes = $votes;
    }

    public function load() : void {
        $file = DataManager::get(self::FILE);
        $this->votes = $file->get("votes", 0);
    }

    public function save() : void {
        $file = DataManager::get(self::FILE);
        $file->set("votes", $this->votes);
        $file->save();
    }

    /** @var self|null */
    private static $instance = null;

    private static function make() : self{
        return new self;
    }

    public static function getInstance() : self{
        if(self::$instance === null){
            self::$instance = self::make();
        }
        return self::$instance;
    }

    public static function setInstance(self $instance) : void{
        self::$instance = $instance;
    }
}