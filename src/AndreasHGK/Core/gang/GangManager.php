<?php

namespace AndreasHGK\Core\gang;

use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\FileUtils;
use pocketmine\Server;

class GangManager {

    public const NAME_MAX = 15;
    public const NAME_MIN = 3;

    public const MAX_MEMBERS = 15;

    public const GANG_FOLDER = "gangs".DIRECTORY_SEPARATOR;

    private static $instance;

    /**
     * @var array|Gang[]
     */
    private $gangs = [];

    public function validateGangNameLength(string $name) : bool {
        return strlen($name) >= self::NAME_MIN && strlen($name) <= self::NAME_MAX;
    }

    public function validateGangNameValidity(string $name) : bool {
        return ctype_alnum($name);
    }

    public function create(string $name, User $owner) : Gang {
        $gang = new Gang(uniqid(), $name, time(), "A new gang!", [], []);
        $this->gangs[$gang->getId()] = $gang;
        $gang->addMember($owner->getPlayer());
        $owner->setGangRank(GangRank::LEADER());
        $this->save($gang->getId());
        return $gang;
    }

    public function delete(Gang $gang) : void {
        unset($this->gangs[$gang->getId()]);
        foreach($gang->getMembers() as $member){
            $user = UserManager::getInstance()->get(Server::getInstance()->getOfflinePlayer($member));
            $user->setGangId("");
            $user->setGangRank(null);
            UserManager::getInstance()->save($user);
        }

        foreach($gang->getAllies() as $ally){
            $allyGang = $this->getByName($ally);
            $allyGang->removeAlly($gang);
        }

        DataManager::deleteFile(self::GANG_FOLDER.$gang->getFileName());
    }

    public function matchGang(string $name) : ?Gang{
        $found = null;
        $name = strtolower($name);
        $delta = PHP_INT_MAX;
        foreach($this->getAll() as $gang){
            if(stripos($gang->getName(), $name) === 0){
                $curDelta = strlen($gang->getName()) - strlen($name);
                if($curDelta < $delta){
                    $found = $gang;
                    $delta = $curDelta;
                }

                if($curDelta === 0){
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * @param string $name
     * @return Gang|null
     */
    public function getByName(string $name) : ?Gang{
        $name = strtolower($name);
        foreach($this->gangs as $gang){
            if(strtolower($gang->getName()) === $name) {
                return $gang;
            }
        }

        return null;
    }

    /**
     * @return array|Gang[]
     */
    public function getAll() : array {
        return $this->gangs;
    }

    /**
     * @param string $id
     *
     * @return Gang|null
     */
    public function get(string $id) : ?Gang {
        return $this->gangs[$id] ?? null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function exists(string $name) : bool {
        return $this->getByName($name) !== null;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function existById(string $id) : bool {
        return isset($this->gangs[$id]);
    }

    public function loadAll() : void {
        $scan = DataManager::getFilesIn(self::GANG_FOLDER);
        foreach($scan as $filename){
            $this->load($filename);
        }
    }

    /**
     * @param string $file
     *
     * @return Gang|null
     */
    public function load(string $file) : ?Gang {
        if($file === "." || $file === "..") {
            return null;
        }

        $file = DataManager::get(self::GANG_FOLDER.$file, false);
        if($file->get("id") === false) {
            return null;
        }

        $data = $file->getAll();
        $gang = new Gang($data["id"], $data["name"], $data["creationDate"], $data["description"], $data["members"], $data["allies"] ?? []);
        $this->gangs[$gang->getId()] = $gang;
        return $gang;
    }

    public function saveAll() : void {
        foreach($this->getAll() as $gang){
            $this->save($gang->getId());
        }
    }

    /**
     * @param string $gangId
     */
    public function save(string $gangId) : void {
        $gang = $this->get($gangId);
        $file = DataManager::get(FileUtils::MakeJSON(self::GANG_FOLDER.$gangId), false);
        $file->set("name", $gang->getName());
        $file->set("id", $gang->getId());
        $file->set("creationDate", $gang->getCreationDate());
        $file->set("members", $gang->getMembers());
        $file->set("allies", $gang->getAllies());
        $file->set("description", $gang->getDescription());
        $file->save();
    }

    /**
     * @return GangManager
     */
    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}
