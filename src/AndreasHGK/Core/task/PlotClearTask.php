<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\plot\PlotManager;
use pocketmine\block\BlockLegacyIds;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\World;

class PlotClearTask extends AsyncTask {

    protected $id;

    protected $pos1;

    protected $pos2;

    protected $chunks;

    public function __construct(string $id, $pos1, $pos2, array $chunks){
        $this->id = $id;
        $this->pos1 = unserialize($pos1);
        $this->pos2 = unserialize($pos2);
        $this->chunks = serialize($chunks);
    }

    private function isInPlot($x, $y, $z){
        $pos1 = $this->pos1;
        $pos2 = $this->pos2;
        return ($x >= min($pos1->getX(), $pos2->getX())) && ($x <= max($pos1->getX(), $pos2->getX()))
            && ($y >= min($pos1->getY(), $pos2->getY())) && ($y <= max($pos1->getY(), $pos2->getY()))
            && ($z >= min($pos1->getZ(), $pos2->getZ())) && ($z <= max($pos1->getZ(), $pos2->getZ()));
    }

    public function onRun() : void{
        $newChunks = [];

        $air = $this->getFullId(BlockLegacyIds::AIR);
        $bedrock = $this->getFullId(BlockLegacyIds::BEDROCK);
        $grass = $this->getFullId(BlockLegacyIds::GRASS);
        $dirt = $this->getFullId(BlockLegacyIds::DIRT);

        $completedBlocks = 0;
        foreach(unserialize($this->chunks) as $key => $chunkHash){
            World::getXZ($key, $chunkX, $chunkZ);
            $chunkX <<= 4;
            $chunkZ <<= 4;
            $chunk = FastChunkSerializer::deserialize($chunkHash);
            for($x = 0; $x < 16; ++$x){
                for($z = 0; $z < 16; ++$z){
                    if(!$this->isInPlot($x + $chunkX, 64, $z + $chunkZ)) {
                        continue;
                    }

                    for($y = 0; $y < 256; ++$y){
                        switch(true){
                            case $y === 0:
                                $chunk->setFullBlock($x, $y, $z, $bedrock);
                                break;
                            case $y > 64:
                                $chunk->setFullBlock($x, $y, $z, $air);
                                break;
                            case $y < 64:
                                $chunk->setFullBlock($x, $y, $z, $dirt);
                                break;
                            case $y === 64:
                                $chunk->setFullBlock($x, $y, $z, $grass);
                                break;
                        }

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
        $world = Server::getInstance()->getWorldManager()->getWorldByName(PlotManager::$plotworld);
        if($world === null){
            return;
        }

        foreach($chunks as $key => $chunk){
            World::getXZ($key, $chunkX, $chunkZ);
            $world->setChunk($chunkX, $chunkZ, FastChunkSerializer::deserialize($chunk), true);
        }

        PlotManager::getInstance()->getById($this->id)->setClearing(false);
    }

    /**
     * @internal
     *
     * @param int $id
     * @param int $meta
     *
     * @return int
     */
    public function getFullId(int $id, int $meta = 0) : int{
        return ($id << 4) | $meta;
    }

}