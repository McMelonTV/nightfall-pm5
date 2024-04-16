<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use pocketmine\block\BlockLegacyIds;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\world\World;

class ConvertWorldTask extends Task {

    public static $blocks = [
        158 => [BlockLegacyIds::WOODEN_SLAB, 0],
        125 => [BlockLegacyIds::DOUBLE_WOODEN_SLAB],
        188 => [BlockLegacyIds::FENCE, 0],
        189 => [BlockLegacyIds::FENCE, 1],
        190 => [BlockLegacyIds::FENCE, 2],
        191 => [BlockLegacyIds::FENCE, 3],
        192 => [BlockLegacyIds::FENCE, 4],
        193 => [BlockLegacyIds::FENCE, 5],
        166 => [BlockLegacyIds::BARRIER, 0],
        208 => [BlockLegacyIds::GRASS_PATH, 0],
        198 => [BlockLegacyIds::END_ROD, 0],
        126 => [BlockLegacyIds::WOODEN_SLAB],
        95 => [BlockLegacyIds::STAINED_GLASS],
        199 => [BlockLegacyIds::CHORUS_PLANT, 0],
        202 => [BlockLegacyIds::PURPUR_BLOCK, 0],
        251 => [BlockLegacyIds::CONCRETE, 0],
        204 => [BlockLegacyIds::PURPUR_BLOCK, 0],
        248 => [BlockLegacyIds::BARRIER, 0],
    ];

    public $world;

    public function __construct(World $world){
        $this->world = $world;
    }

    public function onRun() : void{
        $world = $this->world;

        $chunks = count($world->getChunks());
        $count = 0;
        foreach($world->getChunks() as $chunk){
            for($x = 0; $x < 16; ++$x){
                for($y = 0; $y < 256; ++$y){
                    for($z = 0; $z < 16; ++$z){
                        $blockFullId = $chunk->getFullBlock($x, $y, $z);
                        $id = $blockFullId >> 4;
                        $meta = $blockFullId & 0xf;
                        $newBlock = null;
                        if($id === 44 && $meta === 0){
                            $chunk->setFullBlock($x, $y, $z, 43 << 4);
                        }elseif($id === 3 && $meta === 2){
                            $chunk->setFullBlock($x, $y, $z, 3 << 4);
                        }
                    }
                }
            }
            ++$count;
            if($count % 10 !== 1 || $count === $chunks) {
                continue;
            }

            Server::getInstance()->getLogger()->info($count." out of ".$chunks." chunks converted.");
        }
    }
}