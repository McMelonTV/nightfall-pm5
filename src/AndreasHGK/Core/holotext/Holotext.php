<?php

declare(strict_types=1);

namespace AndreasHGK\Core\holotext;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\object\FallingBlock;

class Holotext extends FallingBlock {

    public string $hologramId;

    protected $gravity = 0.0;
    protected $drag = 0.0;

    public function __construct(string $id, Location $position, string $text, string $title = ""){
        parent::__construct($position, VanillaBlocks::AIR());
        $this->hologramId = $id;
        $this->setCanSaveWithChunk(false);
        $this->setNameTag($title . ($text !== "" ? "\n" . $text : ""));

        $this->setImmobile(true);
        $this->setHasGravity(false);
        $this->setNameTagAlwaysVisible(true);
    }

    protected function entityBaseTick(int $tickDiff = 1) : bool{ return false; }

    public function move(float $dx, float $dy, float $dz) : void{}

    public function canBeCollidedWith() : bool{ return false; }

    protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo(0.0, 0.0); }

    public function setOnFire(int $seconds) : void{}

    public function isOnFire() : bool{ return false; }

    public function isFireProof() : bool{ return true; }
}