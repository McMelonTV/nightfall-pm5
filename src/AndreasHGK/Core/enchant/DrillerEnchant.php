<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\world\utils\SubChunkExplorer;
use pocketmine\world\utils\SubChunkExplorerStatus;

class DrillerEnchant extends CustomEnchant {

    private const AIR_STATE = BlockLegacyIds::AIR << 4;

    public function getCompatible() : array {
        return [self::GROUP_TOOLS];
    }

    public function getDescription() : string {
        return "Mine in a 3x3 area.";
    }

    public function getName() : string {
        return "Driller";
    }

    public function getId() : int {
        return CustomEnchantIds::DRILLER;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::PICKAXE | ItemFlags::SHOVEL;
    }

    public function getRarity() : int {
        return self::RARITY_LEGENDARY;
    }

    public function getMaxLevel() : int {
        return 10;
    }

    //events

    public function onMine(CEMineEvent $ev) : void{
        $block = $ev->getEvent()->getBlock();

        $chance = 10*$this->level;

        if(mt_rand(0, 100) > $chance) {
            return;
        }

        $blockFactory = BlockFactory::getInstance();
        $array = [];

        $pos = $block->getPos();
        $world = $pos->getWorld();

        $subChunkHandler = new SubChunkExplorer($world);

        $minX = $pos->x-1;
        $minY = $pos->y-1;
        $minZ = $pos->z-1;
        $maxX = $pos->x+2;
        $maxY = $pos->y+2;
        $maxZ = $pos->z+2;
        for($x = $minX; $x < $maxX; ++$x){
            for($y = $minY; $y < $maxY; ++$y){
                for($z = $minZ; $z < $maxZ; ++$z){
                    if($subChunkHandler->moveTo($x, $y, $z) === SubChunkExplorerStatus::INVALID){
                        continue;
                    }

                    $state = $subChunkHandler->currentSubChunk->getFullBlock($x & 15, $y & 15, $z & 15);
                    if($state !== static::AIR_STATE){
                        /** @var Block $block */
                        $block = $blockFactory->fromFullBlock($state);
                        $block->position($world, $x, $y, $z);
                        $array[] = $block;
                    }
                }
            }
        }

        $ev->setMinedBlocks($array);
    }
}