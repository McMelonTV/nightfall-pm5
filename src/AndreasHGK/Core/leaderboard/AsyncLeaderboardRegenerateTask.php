<?php

declare(strict_types=1);

namespace AndreasHGK\Core\leaderboard;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\rank\MineRankManager;
use AndreasHGK\Core\user\UserData;
use AndreasHGK\Core\utils\FileUtils;
use pocketmine\scheduler\AsyncTask;

class AsyncLeaderboardRegenerateTask extends AsyncTask {

    public const USER_FOLDER = "users".DIRECTORY_SEPARATOR;

    public $leaderboards;

    public $dataFolder;

    public function __construct($leaderboards, $dataFolder) {
        $this->leaderboards = $leaderboards;
        $this->dataFolder = $dataFolder;
    }

    public function onRun(): void {
        DataManager::$dataFolder = $this->dataFolder;
        $leaderboards = unserialize($this->leaderboards);

        $users = $this->getAllUsers();

        $result = [];
        foreach($leaderboards as $leaderboard) {
            switch($leaderboard["name"]) {
                case Leaderboards::BALTOP:
                    $copy = $users;
                    usort($copy,
                        static function (UserData $user1, UserData $user2) {
                            $v1 = $user1->getBalance();
                            $v2 = $user2->getBalance();
                            if($v1 > $v2) return -1;
                            if($v1 < $v2) return 1;
                            return 0;
                        }
                    );
                    $result[$leaderboard["name"]] = $copy;
                    break;
                case Leaderboards::KILLTOP:
                    $copy = $users;
                    usort($copy,
                        static function (UserData $user1, UserData $user2) {
                            $v1 = $user1->getKills();
                            $v2 = $user2->getKills();
                            if($v1 > $v2) return -1;
                            if($v1 < $v2) return 1;
                            return 0;
                        }
                    );
                    $result[$leaderboard["name"]] = $copy;
                    break;
                case Leaderboards::EARNTOP:
                    $copy = $users;
                    usort($copy,
                        static function (UserData $user1, UserData $user2) {
                            $v1 = $user1->getTotalEarnedMoney();
                            $v2 = $user2->getTotalEarnedMoney();
                            if($v1 > $v2) return -1;
                            if($v1 < $v2) return 1;
                            return 0;
                        }
                    );
                    $result[$leaderboard["name"]] = $copy;
                    break;
                case Leaderboards::KDTOP:
                    $copy = $users;
                    usort($copy,
                        static function (UserData $user1, UserData $user2) {
                            $v1 = $user1->getKDR();
                            $v2 = $user2->getKDR();
                            if($v1 > $v2) return -1;
                            if($v1 < $v2) return 1;
                            return 0;
                        }
                    );
                    $result[$leaderboard["name"]] = $copy;
                    break;
                case Leaderboards::BREAKTOP:
                    $copy = $users;
                    usort($copy,
                        static function (UserData $user1, UserData $user2) {
                            $v1 = $user1->getMinedBlocks();
                            $v2 = $user2->getMinedBlocks();
                            if($v1 > $v2) return -1;
                            if($v1 < $v2) return 1;
                            return 0;
                        }
                    );
                    $result[$leaderboard["name"]] = $copy;
                    break;
                case Leaderboards::MINETOP:
                    $copy = $users;
                    usort($copy,
                        static function (UserData $user1, UserData $user2) {
                            $v1 = $user1->getMineRankId() + $user1->getPrestige() * 100;
                            $v2 = $user2->getMineRankId() + $user2->getPrestige() * 100;
                            if($v1 > $v2) return -1;
                            if($v1 < $v2) return 1;
                            return 0;
                        }
                    );
                    $result[$leaderboard["name"]] = $copy;
                    break;
            }
        }

        $passableResult = [];

        foreach($result as $type => $top) {
            $int = 0;
            /** @var UserData $user */
            foreach($top as $user) {
                $int++;
                $passableResult[$type][] = $user->getName();
                if($int >= $leaderboards[$type]["maxUsers"]) break;
            }
        }

        $this->setResult($passableResult);
    }

    /**
     * @return UserData[]
     */
    public function getAllUsers() : array {
        MineRankManager::getInstance()->loadAll();

        $array = [];
        $scan = DataManager::getFilesIn(self::USER_FOLDER);
        foreach($scan as $filename){
            $u = $this->getUser(explode(".", $filename)[0]);
            $array[$u->getName()] = $u;
        }

        return $array;
    }

    public function getUser(string $name) : UserData {
        $file = DataManager::get(self::USER_FOLDER.FileUtils::MakeJSON(strtolower($name)));

        $user = new UserData($name);

        $user->setMinedBlocks($file->get("minedBlocks", 0));
        $user->setBalance($file->get("balance", 0));
        $user->setMineRank(MineRankManager::getInstance()->get($file->get("mineRank", 0)));
        $user->setPrestige(max(1, $file->get("prestige", 1)));
        $user->setKills($file->get("kills", 0));
        $user->setTotalEarnedMoney($file->get("totalearned", 0));

        return $user;
    }

    public function onCompletion(): void {
        $leaderboards = Leaderboards::getInstance();
        foreach($this->getResult() as $type => $board) {
            $leaderboard = $leaderboards->getLeaderboard($type);
            if($leaderboard === null) continue;
            $leaderboard->setUsersData($board);
        }
        Core::getInstance()->getLogger()->info("The leaderboards have been regenerated");
        //Server::getInstance()->broadcastMessage("§r§8[§bNF§8] §r§7The leaderboards have been regenerated!");
    }

}