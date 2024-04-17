<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\session;

use AndreasHGK\RankSystem\rank\Rank;
use AndreasHGK\RankSystem\rank\RankInstance;
use AndreasHGK\RankSystem\RankSystem;
use pocketmine\player\IPlayer;

class RankComponent {

    public static function fromData(array $data, IPlayer $player) : self {
        $ranks = [];
        foreach($data["ranks"] as $rank) {
            $ranks[] = RankInstance::fromData($rank);
        }
        return new self($player, $ranks);
    }

    /** @var IPlayer */
    protected IPlayer $player;
    /** @var RankInstance[] */
    protected array $ranks = [];
    /** @var bool */
    protected bool $hasChanged = false;

    public function __construct(IPlayer $player, array $ranks) {
        $this->player = $player;
        $this->ranks = $ranks;
    }

    /**
     * Check if a player is staff
     *
     * @return bool
     */
    public function isStaff() : bool {
        foreach($this->ranks as $rank) {
            if($rank->getRank()->isStaff()) return true;
        }
        return false;
    }

    /**
     * Gets the donator rank with the highest priority for the player
     *
     * @return RankInstance|null
     */
    public function getStaffRank() : ?RankInstance {
        /** @var RankInstance|null $rank */
        $rank = null;
        foreach($this->ranks as $rankInstance) {
            if(!$rank->getRank()->isStaff()) continue;
            if($rank === null) {
                $rank = $rankInstance;
                continue;
            }
            if($rank->getRank()->getPriority() < $rankInstance->getRank()->getPriority()) $rank = $rankInstance;
        }
        return $rank;
    }

    /**
     * Check if a player is a donator
     *
     * @return bool
     */
    public function isDonator() : bool {
        foreach($this->ranks as $rank) {
            if($rank->getRank()->isDonator()) return true;
        }
        return false;
    }

    /**
     * Gets the donator rank with the highest priority for the player
     *
     * @return RankInstance|null
     */
    public function getDonatorRank() : ?RankInstance {
        /** @var RankInstance|null $rank */
        $rank = null;
        foreach($this->ranks as $rankInstance) {
            if(!$rankInstance->getRank()->isDonator()) continue;
            if($rank === null) {
                $rank = $rankInstance;
                continue;
            }
            if($rank->getRank()->getPriority() < $rankInstance->getRank()->getPriority()) $rank = $rankInstance;
        }
        return $rank;
    }

    /**
     * Get the owner of the session
     *
     * @return IPlayer
     */
    public function getPlayer() : IPlayer {
        return $this->player;
    }

    /**
     * Check if the owner of the session is online
     *
     * @return bool
     */
    public function isOnline() : bool {
        return $this->player->isOnline();
    }

    /**
     * Get the ranks that the player has
     *
     * @return RankInstance[]
     */
    public function getRanks() : array {
        $array = $this->ranks;
        return array_merge($array, RankSystem::getInstance()->getRankManager()->getDefaultRanks());
    }

    /**
     * Get a user's ranks without the default ranks
     *
     * @return RankInstance[]|array
     */
    public function getActualRanks() : array {
        return $this->ranks;
    }

    /**
     * Get the rank with the highest priority
     *
     * @return RankInstance|null
     */
    public function getMainRank() : ?RankInstance {
        /** @var RankInstance $rank */
        $rank = null;
        foreach($this->getRanks() as $rankInstance) {
            if($rank === null || $rank->getRank()->getPriority() < $rankInstance->getRank()->getPriority()) $rank = $rankInstance;
        }
        return $rank;
    }

    /**
     * Set the ranks that the player has
     *
     * @param RankInstance[] $ranks
     */
    public function setRanks(array $ranks) : void {
        $this->ranks = $ranks;
        $this->hasChanged = true;
    }

    /**
     * Check if the player has a rank
     *
     * @param string $id
     * @return bool
     */
    public function hasRank(string $id) : bool {
        return isset($this->ranks[$id]);
    }

    /**
     * Add a rank for the player
     *
     * @param RankInstance $rank
     */
    public function addRank(RankInstance $rank) : void {
        $this->ranks[$rank->getRank()->getId()] = $rank;
        $this->hasChanged = true;
    }

    /**
     * Remove a rank from the player
     *
     * @param string $id
     */
    public function removeRank(string $id) : void {
        unset($this->ranks[$id]);
        $this->hasChanged = true;
    }

    /**
     * Check if the session has changed since it was last loaded
     *
     * @return bool
     */
    public function hasChanged() : bool {
        return $this->hasChanged;
    }

    public function toData() : array {
        $ranks = [];
        foreach($this->ranks as $rank) {
            $ranks[$rank->getRank()->getId()] = $rank->toData();
        }

        return [
            "ranks" => $ranks
        ];
    }

}