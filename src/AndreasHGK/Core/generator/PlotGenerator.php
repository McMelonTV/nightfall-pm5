<?php

declare(strict_types=1);

namespace AndreasHGK\Core\generator;

use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\InvalidGeneratorOptionsException;
use pocketmine\world\generator\populator\Populator;

class PlotGenerator extends Generator {

    public const PLOT = 0;
    public const WALL = 1;
    public const ROAD = 2;

    /** @var Populator[] */
    private array $populators = [];
    /** @var int */
    private int $floorLevel = 64;

    private int $plotSize = 48;
    /**
     * @param int          $seed
     * @param string        $options
     *
     * @throws InvalidGeneratorOptionsException
     */
    public function __construct(int $seed, string $options){
        parent::__construct($seed, $options);
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{
        $chunk = $world->getChunk($chunkX, $chunkZ);
        $bedrock = VanillaBlocks::BEDROCK()->getStateId();
        $grass = VanillaBlocks::GRASS()->getStateId();
        $dirt = VanillaBlocks::DIRT()->getStateId();
        $path = VanillaBlocks::GRASS_PATH()->getStateId();
        $slab = VanillaBlocks::POLISHED_ANDESITE_SLAB()->getStateId();
        $stone = VanillaBlocks::POLISHED_ANDESITE()->getStateId();
        for($x = 0; $x < 16; ++$x){
            for($z = 0; $z < 16; ++$z){
                $type = self::getTypeAt($x+(16*$chunkX), $z+(16*$chunkZ), $this->plotSize);
                for($y = 0; $y <= $this->floorLevel+1; ++$y){
                    if($y === 0){
                        $chunk->setBlockStateId($x, $y, $z, $bedrock);
                        continue;
                    }

                    if($type === self::PLOT){
                        if($y === $this->floorLevel) {
                            $chunk->setBlockStateId($x, $y, $z, $grass);
                        }elseif($y < $this->floorLevel) {
                            $chunk->setBlockStateId($x, $y, $z, $dirt);
                        }

                        continue;
                    }

                    if($type === self::ROAD){
                        if($y === $this->floorLevel) {
                            $chunk->setBlockStateId($x, $y, $z, $path);
                        }elseif($y < $this->floorLevel) {
                            $chunk->setBlockStateId($x, $y, $z, $dirt);
                        }

                        continue;
                    }
                    if($y === $this->floorLevel+1) {
                        $chunk->setBlockStateId($x, $y, $z, $slab);
                    }else{
                        $chunk->setBlockStateId($x, $y, $z, $stone);
                    }
                }
            }
        }
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);
        foreach($this->populators as $populator){
            $populator->populate($world, $chunkX, $chunkZ, $this->random);
        }
    }

    public static function getTypeAt(int $x, int $z, int $plotSize) : int {
        $typeX = self::singleCoordType($x, $plotSize);
        $typeZ = self::singleCoordType($z, $plotSize);
        if(($typeZ === 1 && $typeX !== 2) || ($typeX === 1 && $typeZ !== 2)){
            return 1;
        }

        if($typeZ === 2 || $typeX === 2){
            return 2;
        }

        return 0;
    }

    public static function singleCoordType(int $coord, int $plotSize) : int {
        $rCoord = $coord % ($plotSize+7);
        if($rCoord < 0){
            $rCoord = ($plotSize+7) + $rCoord;
        }

        if($rCoord > ($plotSize+4)){
            return self::ROAD;
        }elseif($rCoord > ($plotSize+3)){
            return self::WALL;
        }elseif($rCoord > 3){
            return self::PLOT;
        }elseif($rCoord > 2){
            return self::WALL;
        }else{
            return self::ROAD;
        }
    }
}