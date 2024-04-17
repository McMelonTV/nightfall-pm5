<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\rank;

use AndreasHGK\RankSystem\RankSystem;

class RankInstance {

    /**
     * @param Rank $rank
     * @param int $expire
     * @param bool $isPersistent
     * @return RankInstance
     */
    public static function create(Rank $rank, int $expire = -1, bool $isPersistent = true) : RankInstance {
        return new RankInstance($rank, $expire, $isPersistent);
    }


    /**
     * Convert saved data into a rank instance
     *
     * @param array $data
     * @return RankInstance
     */
    public static function fromData(array $data) : RankInstance {
        $rank = RankSystem::getInstance()->getRankManager()->get($data["rankId"]);
        return new RankInstance($rank, $data["expire"], $data["isPersistant"]);
    }

    /** @var Rank */
    private $rank;
    /** @var int */
    private $expire = -1;
    /** @var bool  */
    private $isPersistent = true;

    public function __construct(Rank $rank, int $expire = -1, bool $isPersistent = true) {
        $this->rank = $rank;
        $this->expire = $expire;
        $this->isPersistent = $isPersistent;
    }

    /**
     * Get the time that this rank will expire
     *
     * @return int
     */
    public function getExpire() : int {
        return $this->expire;
    }

    /**
     * Check if the rank will expire after a certain amount of time
     *
     * @return bool
     */
    public function isPermanent() : bool {
        return $this->expire === -1;
    }

    /**
     * Get the actual rank class
     *
     * @return Rank
     */
    public function getRank() : Rank {
        return $this->rank;
    }

    /**
     * Whether or not the rank will stay with resets
     *
     * @return bool
     */
    public function isPersistent() : bool {
        return $this->isPersistent;
    }

    /**
     * Convert the rank instance to saveable data
     *
     * @return array
     */
    public function toData() : array {
        return [
            "rankId" => $this->getRank()->getId(),
            "isPersistant" => $this->isPersistent(),
            "expire" => $this->getExpire(),
        ];
    }

}