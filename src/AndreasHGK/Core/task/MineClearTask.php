<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\mine\RegenerationObserver;
use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\AsyncTask;
use pocketmine\world\format\io\FastChunkSerializer;

class MineClearTask extends AsyncTask {

    protected $id;

    protected $pos1;

    protected $pos2;

    protected $chunks;

    public function __construct(int $id, $pos1, $pos2, array $chunks){
        $this->id = $id;
        $this->pos1 = unserialize($pos1);
        $this->pos2 = unserialize($pos2);
        $this->chunks = serialize($chunks);
    }

    private function isInMine($x, $y, $z){
        $pos1 = $this->pos1;
        $pos2 = $this->pos2;
        return ($x >= min($pos1->getX(), $pos2->getX())) && ($x <= max($pos1->getX(), $pos2->getX()))
            && ($y >= min($pos1->getY(), $pos2->getY())) && ($y <= max($pos1->getY(), $pos2->getY()))
            && ($z >= min($pos1->getZ(), $pos2->getZ())) && ($z <= max($pos1->getZ(), $pos2->getZ()));
    }

    public function onRun(): void{
        $pos1 = $this->pos1;
        $pos2 = $this->pos2;

        $newChunks = [];

        $completedBlocks = 0;
        foreach(unserialize($this->chunks) as $key => $chunkHash){
            $chunk = FastChunkSerializer::deserialize($chunkHash);
            $minY = min($pos1->getY(), $pos2->getY());
            $maxY = max($pos1->getY(), $pos2->getY());
            for(; $minY <= $maxY; ++$minY){
                for($x = 0; $x <= 15; ++$x){
                    for($z = 0; $z <= 15; ++$z){
                        if(!$this->isInMine($x + $chunk->getX() * 16, $minY, $z + $chunk->getZ() * 16)) continue;

                        $blockClass = VanillaBlocks::AIR();
                        $chunk->setFullBlock($x, $minY, $z, $blockClass->getFullId());

                        ++$completedBlocks;
                    }
                }
            }

            $newChunks[$key] = FastChunkSerializer::serialize($chunk, false);
        }

        $this->setResult($newChunks);
    }

    public function onCompletion(): void{
        $chunks = $this->getResult();
        $mine = MineManager::getInstance()->get($this->id);
        $world = $mine->getWorld();
        foreach($chunks as $key => $chunk){
            $loc = explode(":", $key);
            $world->setChunk((int)$loc[0], (int)$loc[1], FastChunkSerializer::deserialize($chunk));
        }

        $mine->setRegenerating(false);

        RegenerationObserver::getInstance()->completeClear($this->id);
    }
}