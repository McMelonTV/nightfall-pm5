<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\crate\CrateItem;
use AndreasHGK\Core\holotext\Holotext;
use AndreasHGK\Core\holotext\HolotextManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\world\sound\ChestCloseSound;
use pocketmine\world\sound\ChestOpenSound;
use pocketmine\world\sound\XpLevelUpSound;

class CrateAnimation extends Task {

    public $player;

    public $user;

    public $position;

    /** @var CrateItem */
    public $item;

    public $frame = 0;

    private ?Holotext $hologram;

    public function __construct(Player $player, Position $position, CrateItem $item){
        $this->player = $player;
        $this->user = UserManager::getInstance()->getOnline($player);
        $this->position = $position;
        $this->item = $item;
    }

    public function onRun() : void {
        if($this->user === null) {
            return;
        }

        $user = $this->user;
        $player = $this->player;
        $pos = $this->position;
        $item = $this->item->getItem();
        $cItem = $this->item;

        $newPos = $pos->add(0.5, 1.5, 0.5);
        switch ($this->frame){
            case 0:
                $user->playSound(new ChestOpenSound());
                $pk = BlockEventPacket::create(1, 1, $pos);
                $player->getNetworkSession()->sendDataPacket($pk);

                $this->hologram = new Holotext(HolotextManager::getInstance()->getNextId(), new Location($newPos->x, $newPos->y, $newPos->z, 0, 0, $pos->getWorld()), "§r§b".$item->getCount()."§r§bx ".($cItem->getCrateName() ?? $item->getName()));
                $this->hologram->spawnTo($player);
                break;
            case 1:
                $user->playSound(new XpLevelUpSound(5));
                break;
            case 40:
                $pk = BlockEventPacket::create(1, 0, $pos);

                if(!$player->isConnected()) return;
                $session = $player->getNetworkSession();
                $session->sendDataPacket($pk);
                $user->playSound(new ChestCloseSound());

                $this->hologram->despawnFrom($player);
                $this->hologram->flagForDespawn();
                break;
            case 60:
                $this->getHandler()->cancel();
                //Core::getInstance()->getScheduler()->cancelTask($this->getTaskId());
                break;
        }

        ++$this->frame;
    }
}