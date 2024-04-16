<?php

declare(strict_types=1);

namespace AndreasHGK\Core\pvp;

use pocketmine\math\Vector3;
use pocketmine\world\World;

class PVPZone {

    private string $id;

    private World $world;

    private Vector3 $pos1;

    private Vector3 $pos2;

    private bool $safeZone;

    public function __construct(string $id, World $world, Vector3 $pos1, Vector3 $pos2, bool $safe = false){
        $this->id = $id;
        $this->world = $world;
        $this->pos1 = Vector3::minComponents($pos1, $pos2);
        $this->pos2 = Vector3::maxComponents($pos1, $pos2);
        $this->safeZone = $safe;
    }

    public function isSafe() : bool {
        return $this->safeZone;
    }

    public function setSafe(bool $safe) : void {
        $this->safeZone = $safe;
    }

    public function isInZone($x, $y, $z, World $world = null) : bool{
        $pos1 = $this->pos1;
        $pos2 = $this->pos2;
        if($world !== $this->world && $world !== null) {
            return false;
        }

        return ($x >= $pos1->x) && ($x <= $pos2->x)
            && ($y >= $pos1->y) && ($y <= $pos2->y)
            && ($z >= $pos1->z) && ($z <= $pos2->z);
    }

    public function getId() : string {
        return $this->id;
    }

    public function getWorld() : World {
        return $this->world;
    }

    public function getPos1() : Vector3 {
        return $this->pos1;
    }

    public function getPos2() : Vector3 {
        return $this->pos2;
    }
}