<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\mine\RegenerationObserver;
use pocketmine\item\StringToItemParser;
use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\World;

class MineRegenerateTask extends AsyncTask {

    protected $id;

    protected $minPos;

    protected $maxPos;

    protected $chunks;

    protected $blocks;

    public function __construct(int $id, Vector3 $pos1, Vector3 $pos2, array $chunks, array $blocks){
        $this->id = $id;
        $this->minPos = serialize($pos1);
        $this->maxPos = serialize($pos2);
        $this->chunks = serialize($chunks);
        $this->blocks = serialize($blocks);
    }

    private function isInMine($x, $y, $z) : bool{
        $pos1 = unserialize($this->minPos);
        $pos2 = unserialize($this->maxPos);
        return ($x >= $pos1->x) && ($x <= $pos2->x)
            && ($y >= $pos1->y) && ($y <= $pos2->y)
            && ($z >= $pos1->z) && ($z <= $pos2->z);
    }

    private function isPlayerInMine($x, $y, $z) : bool{
        $pos1 = unserialize($this->minPos);
        $pos2 = unserialize($this->maxPos);
        return ($x >= $pos1->x) && ($x <= $pos2->x)
            && ($y >= $pos1->y) && ($y <= $pos2->y+1)
            && ($z >= $pos1->z) && ($z <= $pos2->z);
    }

    public function onRun(): void{
        $blockArray = [];
        foreach (unserialize($this->blocks) as $block => $value){
            // $blockArray = array_merge($blockArray, array_fill(0, $value, $block));
            // reminder to look at whatever the fuck the above line is
            $blockArray[] = $value;
        }

        $minY = unserialize($this->minPos)->getY();
        $maxY = unserialize($this->maxPos)->getY()+1;

        $newChunks = [];

        $completedBlocks = 0;
        foreach(unserialize($this->chunks) as $key => $chunkHash){
            World::getXZ($key, $chunkX, $chunkZ);
            $chunkX <<= 4;
            $chunkZ <<= 4;
            $chunk = FastChunkSerializer::deserializeTerrain($chunkHash);
            for($y = $minY; $y < $maxY; ++$y){
                for($x = 0; $x < 16; ++$x){
                    for($z = 0; $z < 16; ++$z){
                        if(!$this->isInMine($x + $chunkX, $y, $z + $chunkZ)){
                            continue;
                        }

                        // $block = explode(":", (string)$blockArray[array_rand($blockArray)]);
                        // $blockClass = BlockFactory::getInstance()->get((int)$block[0], isset($block[1]) ? (int)$block[1] : 0);
                        // $chunk->setFullBlock($x, $y, $z, $blockClass->getFullId());
                        // $chunk->setBlockStateId($x, $y, $z, (int)$block[0]);

                        $blockString = (string)$blockArray[array_rand($blockArray)];
                        $item = StringToItemParser::getInstance()->parse($blockString);
                        $block = $item->getBlock()->getStateId();
                        $chunk->setBlockStateId($x, $y, $z, $block);

                        ++$completedBlocks;
                    }
                }
            }

            $newChunks[$key] = FastChunkSerializer::serializeTerrain($chunk);
        }

        $this->setResult($newChunks);
    }

    public function onCompletion(): void{
        $chunks = $this->getResult();
        $mine = MineManager::getInstance()->get($this->id);
        $world = $mine->getWorld();
        $mine->resetBlockCount();
        if($world === null){
            return;
        }

        foreach($chunks as $key => $chunk){
            World::getXZ($key, $chunkX, $chunkZ);
            $world->setChunk($chunkX, $chunkZ, FastChunkSerializer::deserializeTerrain($chunk));
        }

        $maxY = $mine->getPos2()->y+1;
        $mine->setRegenerating(false);
        $mineName = $mine->getName();
        foreach($world->getPlayers() as $player){
            $pos = $player->getPosition();
            if($this->isPlayerInMine($pos->x, $pos->y, $pos->z)){
                $player->teleport(new Vector3($pos->x, $maxY, $pos->z));
                $player->sendMessage("§r§8[§bNF§8] §r§7Mine §b" . $mineName . "§r§7 has been reset.");
            }
        }

        RegenerationObserver::getInstance()->completeRegeneration($this->id);
    }
}