<?php

declare(strict_types=1);

namespace AndreasHGK\Core\leaderboard;

use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\Server;

class Leaderboard {

    /** @var UserManager */
    protected $userManager;
    /** @var string[] */
    protected $users = [];
    /** @var int */
    protected $maxUsers;
    /** @var string */
    protected $name;

    /**
     * @return User[]
     */
    public function getUsers() : array {
        $users = [];
        foreach($this->users as $userName) {
            $p = Server::getInstance()->getOfflinePlayer($userName);
            if($p === null) continue;
            $user = $this->userManager->get($p);
            if($user === null) continue;
            $users[] = $user;
        }
        return $users;
    }

    /**
     * @return string[]
     */
    public function getUsersData() : array {
        return $this->users;
    }

    /**
     * @param array $data
     */
    public function setUsersData(array $data) : void {
        $this->users = $data;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMaxUsers() : int {
        return $this->maxUsers;
    }

    /**
     * @param int $users
     */
    public function setMaxUsers(int $users) : void {
        $this->maxUsers = $users;
    }

    /**
     * Leaderboard constructor.
     * @param string $name
     * @param int $maxUsers
     */
    public function __construct(string $name, int $maxUsers = 30)  {
        $this->name = $name;
        $this->maxUsers = $maxUsers;
        $this->userManager = UserManager::getInstance();
    }

    public function asData() : array {
        return [
            "name" => $this->name,
            "maxUsers" => $this->maxUsers,
        ];
    }

}