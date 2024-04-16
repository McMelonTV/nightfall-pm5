<?php

declare(strict_types=1);

namespace AndreasHGK\Core\warning;

use AndreasHGK\Core\user\OfflineUser;
use AndreasHGK\Core\user\UserManager;
use pocketmine\player\IPlayer;
use pocketmine\Server;

class Warning {

    public const WARN_EXPIRE = 604800;

    /**
     * Load a warning object from saved data
     *
     * @param IPlayer $player
     * @param array $data
     * @return Warning
     */
    public static function fromData(IPlayer $player, array $data) : Warning {
        return new Warning($player, $data["time"], $data["reason"], $data["staff"]);
    }

    /** @var IPlayer */
    private $target;
    /** @var int */
    private $time;
    /** @var string */
    private $reason;
    /** @var string */
    private $staff;

    public function __construct(IPlayer $target, int $time, string $reason, string $staffName) {
        $this->target = $target;
        $this->time = $time;
        $this->reason = $reason;
        $this->staff = $staffName;
    }

    /**
     * Get the player who got warned
     *
     * @return IPlayer
     */
    public function getTarget() : IPlayer {
        return $this->target;
    }

    /**
     * Get the user object for the target player
     *
     * @return OfflineUser
     */
    public function getTargetUser() : OfflineUser {
        return UserManager::getInstance()->get($this->getTarget());
    }

    /**
     * Get the time at which the player got warned
     *
     * @return int
     */
    public function getTime() : int {
        return $this->time;
    }

    /**
     * Check if the warn has expired
     *
     * @return bool
     */
    public function isExpired() : bool {
        return $this->getTime() + self::WARN_EXPIRE < time();
    }

    /**
     * Get the reason for which the player got warned
     *
     * @return string
     */
    public function getReason() : string {
        return $this->reason;
    }

    /**
     * Get the name of the staff member who got warned
     *
     * @return string
     */
    public function getStaffName() : string {
        return $this->staff;
    }

    /**
     * Convert the warning into saveable data
     *
     * @return array
     */
    public function toData() : array {
        return [
            "reason" => $this->getReason(),
            "time" => $this->getTime(),
            "staff" => $this->getStaffName(),
        ];
    }

}