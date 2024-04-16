<?php

namespace AndreasHGK\Core\achievement;

use AndreasHGK\Core\user\OfflineUser;
use AndreasHGK\Core\user\UserManager;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\player\Player;

class AchievementManager {

    private static $instance;

    /**
     * @var array|Achievement[]
     */
    private $achievements = [];

    public function tryAchieve(OfflineUser $user, int $achievementId) : bool {
        if(isset($user->getAchievements()[$achievementId]) && $user->getAchievements()[$achievementId] === true){
            return false;
        }else{
            $achievement = $this->get($achievementId);
            $achievements = $user->getAchievements();
            $achievements[$achievementId] = true;

            $user->setAchievements($achievements);
            $user->addMoney($achievement->getMoneyReward());
            $user->setPrestigePoints($user->getPrestigePoints()+$achievement->getPrestigeReward());

            UserManager::getInstance()->save($user);
            if(!$user->isOnline()) {
                return true;
            }

            $string = "§8§l<--§bNF§8-->"."\n §r§7You have achieved: §b".$achievement->getName()."\n §r§b§l>§r§7 Description: §b".$achievement->getDesc();
            if($achievement->getMoneyReward() > 0 && $achievement->getPrestigeReward() > 0){
                $string .= "\n §r§b§l> §r§7You have been rewarded §b$".$achievement->getMoneyReward()."§r§7 and §b".$achievement->getPrestigeReward()."§opp§r§7 for achieving this.";
            }elseif($achievement->getMoneyReward() > 0){
                $string .= "\n §r§b§l> §r§7You have been rewarded §b$".$achievement->getMoneyReward()."§r§7 for achieving this.";
            }elseif($achievement->getPrestigeReward() > 0){
                $string .= "\n §r§b§l> §r§7You have been rewarded §b".$achievement->getPrestigeReward()."§opp§r§7 for achieving this.";
            }

            $string .= "\n§r§8§l<--++-->⛏";
            $player = $user->getPlayer();
            /** @var $player Player */
            $player->sendMessage($string);
            $pk = LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_LEVELUP, $player->getPosition(), 0x10000000 * intdiv(30, 5));
            $player->getNetworkSession()->sendDataPacket($pk);
            return true;
        }
    }

    public function getFromName(string $name) : ?Achievement{
        foreach($this->achievements as $mr){
            if(strtolower($mr->getName()) === strtolower($name)) return $mr;
        }
        return null;
    }

    /**
     * @return array|Achievement[]
     */
    public function getAll() : array {
        return $this->achievements;
    }

    public function get(int $id) : ?Achievement {
        return $this->achievements[$id] ?? null;
    }

    public function exist(int $id) : bool {
        return isset($this->achievements[$id]);
    }

    public function register(Achievement $achievement) : void {
        $this->achievements[$achievement->getId()] = $achievement;
    }

    public function registerDefaults() : void {
        $this->register(new Achievement(Achievement::TIME_TO_MINE, "Time to mine", "Mine your first block", 1000, 100));
        $this->register(new Achievement(Achievement::GETTING_AN_UPGRADE, "Getting an upgrade", "Forge a better pickaxe in the forge", 0, 100));
        $this->register(new Achievement(Achievement::BIG_BUCKS_1, "Big Bucks I", "Earn a total of $10000", 0, 100));
        $this->register(new Achievement(Achievement::BIG_BUCKS_2, "Big Bucks II", "Earn a total of $100000", 0, 150));
        $this->register(new Achievement(Achievement::BIG_BUCKS_3, "Big Bucks III", "Earn a total of $1000000", 0, 200));
        $this->register(new Achievement(Achievement::BIG_BUCKS_4, "Big Bucks IV", "Earn a total of $10000000", 0, 250));
        $this->register(new Achievement(Achievement::BIG_BUCKS_5, "Big Bucks V", "Earn a total of $100000000", 0, 250));
        $this->register(new Achievement(Achievement::GRINDER_1, "Grinder I", "Mine a total of 10000 blocks", 0, 100));
        $this->register(new Achievement(Achievement::GRINDER_2, "Grinder II", "Mine a total of 50000 blocks", 0, 150));
        $this->register(new Achievement(Achievement::GRINDER_3, "Grinder III", "Mine a total of 100000 blocks", 0, 200));
        $this->register(new Achievement(Achievement::GRINDER_4, "Grinder IV", "Mine a total of 250000 blocks", 0, 250));
        $this->register(new Achievement(Achievement::GRINDER_5, "Grinder V", "Mine a total of 1000000 blocks", 0, 250));
        //$this->register(new Achievement(Achievement::MASTER, "Master", "Fully master an item", 0, 40));
        $this->register(new Achievement(Achievement::QUALITY_MARKSMANSHIP, "Quality Marksmanship", "Forge a legendary item", 0, 300));
        $this->register(new Achievement(Achievement::DAREDEVIL, "Daredevil", "Find out fall damage is disabled", 0, 250));
        $this->register(new Achievement(Achievement::PRESTIGIOUS, "Prestigious", "Reach prestige 2", 0, 100));
        $this->register(new Achievement(Achievement::WHAT_DID_IT_COST, "What did it cost?", "Everything.", 0, 200));
        $this->register(new Achievement(Achievement::TEAM_UP, "Team Up", "Join or create a gang", 1000, 150));
        $this->register(new Achievement(Achievement::KOTH, "King of the Hill", "Become King of the Hill", 1000, 150));
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}