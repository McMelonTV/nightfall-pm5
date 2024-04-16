<?php

declare(strict_types=1);

namespace AndreasHGK\Core\mine;

use AndreasHGK\Core\task\MineClearTask;
use AndreasHGK\Core\task\MineRegenerateTask;
use AndreasHGK\Core\user\User;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\Position;
use pocketmine\world\World;
use function abs;

class Mine {

    private $name;

    private $id;

    private $world;

    private Vector3 $minPos;

    private Vector3 $maxPos;

    private $blocks = [];

    private $prices = [];

    private $isRegenerating = false;

    private $spawnPosition;

    private $isDisabled = false;

    private $blockCount = 0;

    public function __construct(int $id, string $name, World $world, Vector3 $pos1, Vector3 $pos2){
        $this->id = $id;
        $this->name = $name;
        $this->world = $world;
        $this->minPos = Vector3::minComponents($pos1, $pos2);
        $this->maxPos = Vector3::maxComponents($pos1, $pos2);
    }

    public function getBlockCount() : int {
        return $this->blockCount;
    }

    public function setBlockCount(int $blockCount) : void {
        $this->blockCount = $blockCount;
    }

    public function reduceBlockCount(int $reduce = 1) : void {
        $this->blockCount -= $reduce;
    }

    public function resetBlockCount() : void {
        $this->blockCount = $this->getTotalBlocks();
    }

    public function hasAccessTo(User $player) : bool {
        if($player->getAdminMode()) {
            return true;
        }

        if($this->isDisabled()) {
            return false;
        }

        $id = $this->getId();
        /*if($id === -1 && $player->getPrestige() < 2){
            return false;
        }*/

        return $id <= $player->getMineRankId();
    }

    public function isDisabled() : bool {
        return $this->isDisabled;
    }

    public function setDisabled(bool $bool = true) : void {
        $this->isDisabled = $bool;
    }

    public function getSpawnPosition() : Position {
        return $this->spawnPosition;
    }

    public function setSpawnPosition(Position $spawnPosition) : void {
        $this->spawnPosition = $spawnPosition;
    }

    public function getTotalBlocks() : int {
        $pos1 = $this->minPos;
        $pos2 = $this->maxPos;
        return (abs($pos1->x - $pos2->x)+1)
            * (abs($pos1->y - $pos2->y)+1)
            * (abs($pos1->z - $pos2->z)+1);
    }

    public function isInMine($x, $y, $z, World $world = null) : bool{
        $minPos = $this->minPos;
        $maxPos = $this->maxPos;
        if($world !== $this->world && $world !== null) {
            return false;
        }

        return ($x >= $minPos->x) && ($x <= $maxPos->x)
            && ($y >= $minPos->y) && ($y <= $maxPos->y)
            && ($z >= $minPos->z) && ($z <= $maxPos->z);
    }

    public function isRegenerating() : bool {
        return $this->isRegenerating;
    }

    public function setRegenerating(bool $reset = true) : void {
        $this->isRegenerating = $reset;
    }

    public function getWorld() : World {
        return $this->world;
    }

    public function setWorld(World $world) : void {
        $this->world = $world;
    }

    public function getBlocks() : array {
        return $this->blocks;
    }

    public function setBlocks(array $blocks) : void {
        $this->blocks = $blocks;
    }

    public function getPrices() : array {
        return $this->prices;
    }

    public function setPrices(array $prices) : void {
        $this->prices = $prices;
    }

    public function getPos1() : Vector3 {
        return $this->minPos;
    }

    public function getPos2() : Vector3 {
        return $this->maxPos;
    }

    public function isHigherThan(Mine $m) : bool {
        return $m->getId() < $this->id;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getId() : int {
        return $this->id;
    }

    public function regenerate() : void {
        $pos1 = $this->minPos;
        $pos2 = $this->maxPos;
        $chunks = [];

        $xMin = $pos1->getX();
        $xMax = $pos2->getX();
        $zMin = $pos1->getZ();
        $zMax = $pos2->getZ();
        for($x = $xMin; $x - 16 <= $xMax; $x += 16){
            for($z = $zMin; $z - 16 <= $zMax; $z += 16){
                $chunks[World::chunkHash(($x >> 4), ($z >> 4))] = FastChunkSerializer::serialize($this->world->loadChunk($x >> 4, $z >> 4), false);
            }
        }

        $this->isRegenerating = true;

        $resetTask = new MineRegenerateTask($this->getId(), serialize($this->getPos1()), serialize($this->getPos2()), $chunks, $this->getBlocks());
        Server::getInstance()->getAsyncPool()->submitTask($resetTask);
    }

    public function clear() : void {
        $pos1 = $this->minPos;
        $pos2 = $this->maxPos;
        $chunks = [];

        $xMin = min($pos1->getX(), $pos2->getX());
        $xMax = max($pos1->getX(), $pos2->getX());
        for(; $xMin - 16 <= $xMax; $xMin += 16){
            $zMin = min($pos1->getZ(), $pos2->getZ());
            $zMax = max($pos1->getZ(), $pos2->getZ());
            for(; $zMin - 16 <= $zMax; $zMin += 16){
                $chunks[($xMin >> 4).":".($zMin >> 4)] = FastChunkSerializer::serialize($this->world->getChunk($xMin >> 4, $zMin >> 4), false);
            }
        }

        $this->isRegenerating = true;

        $resetTask = new MineClearTask($this->getId(), serialize($this->getPos1()->asVector3()), serialize($this->getPos2()->asVector3()), $chunks);
        Server::getInstance()->getAsyncPool()->submitTask($resetTask);
    }
}