<?php

declare(strict_types=1);

namespace AndreasHGK\Core\achievement;

use AndreasHGK\Core\user\User;

class Achievement {

    public const TIME_TO_MINE = 0; //mine your first block
    public const GETTING_AN_UPGRADE = 1; //buy your first pickaxe
    public const BIG_BUCKS_1 = 2; //earn x money
    public const BIG_BUCKS_2 = 3;
    public const BIG_BUCKS_3 = 4;
    public const BIG_BUCKS_4 = 5;
    public const BIG_BUCKS_5 = 6;
    public const GRINDER_1 = 7; //mine x blocks
    public const GRINDER_2 = 8;
    public const GRINDER_3 = 9;
    public const GRINDER_4 = 10;
    public const GRINDER_5 = 11;
    public const QUALITY_MARKSMANSHIP = 12; //get a legendary item
    public const DAREDEVIL = 13; //find out fall damage is disabled
    public const PRESTIGIOUS = 14; //get to prestige 2
    public const WHAT_DID_IT_COST = 15; //everything (get to mine Z)
    public const DIAMONDS = 16; //find diamonds
    public const TEAM_UP = 17; //create or find a gang
    public const KOTH = 18; //become king of the hill

    private string $name;

    private string $desc;

    private int $id;

    private $moneyReward = 0;
    private $prestigeReward = 0;

    public function getName() : string {
        return $this->name;
    }

    public function getDesc() : string {
        return $this->desc;
    }

    public function getId() : int {
        return $this->id;
    }

    public function getMoneyReward() : float {
        return $this->moneyReward;
    }

    public function getPrestigeReward() : float {
        return $this->prestigeReward;
    }

    public function isAchievedBy(User $user){
        return array_key_exists($this->getId(), $user->getAchievements());
    }

    public function __construct(int $id, string $name, string $desc, float $moneyReward = 0, float $prestigeReward = 0){
        $this->id = $id;
        $this->name = $name;
        $this->desc = $desc;
        $this->moneyReward = $moneyReward;
        $this->prestigeReward = $prestigeReward;
    }
}