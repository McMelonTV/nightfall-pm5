<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\session;

use AndreasHGK\Core\Core;
use AndreasHGK\RankSystem\rank\Rank;
use AndreasHGK\RankSystem\rank\RankInstance;
use AndreasHGK\RankSystem\RankSystem;
use pocketmine\permission\Permissible;
use pocketmine\permission\PermissionAttachment;
use pocketmine\player\IPlayer;
use pocketmine\player\Player;

class OnlineRankComponent extends RankComponent {

    public static function fromData(array $data, IPlayer $player) : RankComponent {

        if($player instanceof Player) {
            $component = new OnlineRankComponent($player, []);
        }else{
            $component = new RankComponent($player, []);
        }
        foreach($data["ranks"] ?? [] as $rank) {
            $component->addRank(RankInstance::fromData($rank));
        }
        return $component;
    }

    /** @var PermissionAttachment */
    private $attachments = [];

    public function __construct(IPlayer $player, array $ranks) {
        parent::__construct($player, $ranks);
        foreach(RankSystem::getInstance()->getRankManager()->getDefaultRanks() as $defaultRank) {
            $this->addAttachment($defaultRank->getRank());
        }
    }

    /**
     * Add a rank for the player
     *
     * @param RankInstance $rank
     */
    public function addRank(RankInstance $rank) : void {
        parent::addRank($rank);
        $this->addAttachment($rank->getRank());
    }

    /**
     * Remove a rank from the player
     *
     * @param string $id
     */
    public function removeRank(string $id) : void {
        $rankInstance = $this->getRanks()[$id] ?? null;
        if($rankInstance !== null) {
            $this->removeAttachment($rankInstance->getRank());
        }
        parent::removeRank($id);
    }

    /**
     * Clear all the attachments and recalculate them
     */
    public function recalculateAttachments() : void {
        $player = $this->getPlayer();
        if(!$player instanceof Permissible) return;

        foreach($this->attachments as $attachment) {
            $player->removeAttachment($attachment);
        }
        $this->attachments = [];

        foreach($this->getRanks() as $rankInstance) {
            $this->addAttachment($rankInstance->getRank());
        }
    }

    /**
     * Give the permission attachment of a rank to a player
     *
     * @param Rank $rank
     */
    protected function addAttachment(Rank $rank) : void {
        $player = $this->getPlayer();
        if(!$player instanceof Permissible) return;

        $attachment = $player->addAttachment(Core::getInstance());
        foreach($rank->getAllPermissions() as $permission) {
            if($permission[0] === "-") {
                $attachment->setPermission(ltrim($permission, "-"), false);
            }else{
                $attachment->setPermission($permission, true);
            }
        }
        $this->attachments[$rank->getId()] = $attachment;
    }

    /**
     * Remove the permission attachment of a rank from a player
     *
     * @param Rank $rank
     */
    public function removeAttachment(Rank $rank) : void {
        $player = $this->getPlayer();
        if(!$player instanceof Permissible) return;

        $player->removeAttachment($this->attachments[$rank->getId()]);
        unset($this->attachments[$rank->getId()]);
    }

}