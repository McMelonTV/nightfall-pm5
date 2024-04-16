<?php

namespace AndreasHGK\Core\gang;

use pocketmine\utils\EnumTrait;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever enum members are added, removed or changed.
 * @see EnumTrait::_generateMethodAnnotations()
 *
 * @method static self LEADER()
 * @method static self OFFICER()
 * @method static self MEMBER()
 * @method static self RECRUIT()
 */
final class GangRank{
    use EnumTrait;

    protected static function setup() : void{
        self::registerAll(
            new self("leader"),
            new self("officer"),
            new self("member"),
            new self("recruit")
        );
    }

    public function above() : GangRank{
        switch(true){
            case $this->equals(GangRank::OFFICER()):
                return GangRank::LEADER();
            case $this->equals(GangRank::MEMBER()):
                return GangRank::OFFICER();
            case $this->equals(GangRank::RECRUIT()):
                return GangRank::MEMBER();
        }

        return GangRank::RECRUIT();
    }

    public function below() : GangRank{
        switch(true){
            case $this->equals(GangRank::LEADER()):
                return GangRank::OFFICER();
            case $this->equals(GangRank::OFFICER()):
                return GangRank::MEMBER();
            case $this->equals(GangRank::MEMBER()):
                return GangRank::RECRUIT();
        }

        return GangRank::RECRUIT();
    }
}