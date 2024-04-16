<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\UserManager;
use pocketmine\block\BlockLegacyIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;

class ProtectionListener implements Listener {

    public function onExplode(EntityExplodeEvent $ev) : void {
        $ev->cancel();
    }

    public function onPrime(ExplosionPrimeEvent $ev) : void {
        $ev->cancel();
    }

    public function onDamage(EntityDamageEvent $ev) : void {
        $player = $ev->getEntity();
        if(!$player instanceof Player) return;
        /*$user = UserManager::getInstance()->getOnline($player);
        if(!$user->canPvPAt($player->getPosition())) {
            $ev->cancel();
        }*/
        if($ev instanceof EntityDamageByEntityEvent) {
            return;
        }

        switch ($ev->getCause()){
            case EntityDamageEvent::CAUSE_FALL:
            case EntityDamageEvent::CAUSE_SUFFOCATION:
            case EntityDamageEvent::CAUSE_VOID:
            case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
            case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
            case EntityDamageEvent::CAUSE_STARVATION:
            case EntityDamageEvent::CAUSE_DROWNING:
                $ev->cancel();
                break;
        }
    }

    /**
     * @param BlockBreakEvent $ev
     *
     * @priority Low
     */
    public function onBlockBreak(BlockBreakEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if(!$user->canDestroyAt($ev->getBlock()->getPos())){
            $ev->cancel();
            $user->sendTip("§r§8[§bNF§8]§r\n§r§7You can't build here!");
        }
    }

    /**
     * @param BlockPlaceEvent $ev
     *
     * @priority Low
     */
    public function onBuild(BlockPlaceEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if(!$user->canBuildAt($ev->getBlock()->getPos())){
            $ev->cancel();
            $user->sendTip("§r§8[§bNF§8]§r\n§r§7You can't build here!");
        }
    }

    public function onChest(PlayerInteractEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if(!$user->canInteractAt($ev->getBlock()->getPos(), $ev->getBlock()->getId())){
            $ev->cancel();
            $user->sendTip("§r§8[§bNF§8]§r\n§r§7You can't use that here!");
        }
    }

    public function onLiquidPlace(PlayerBucketEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if(!$user->canBuildAt($ev->getBlockClicked()->getPos())){
            $ev->cancel();
            $user->sendTip("§r§8[§bNF§8]§r\n§r§7You can't build here!");
        }
    }

    public function onSignChange(SignChangeEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if(!$user->canBuildAt($ev->getSign()->getPos())){
            $ev->cancel();
            $user->sendTip("§r§8[§bNF§8]§r\n§r§7You can't build here!");
        }
    }

    public function onBucketEmpty(PlayerBucketEmptyEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if(!$user->canBuildAt($ev->getBlockClicked()->getPos())){
            $ev->cancel();
            $user->sendTip("§r§8[§bNF§8]§r\n§r§7You can't build here!");
        }
    }

    public function onBucketFill(PlayerBucketFillEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if(!$user->canBuildAt($ev->getBlockClicked()->getPos())){
            $ev->cancel();
            $user->sendTip("§r§8[§bNF§8]§r\n§r§7You can't build here!");
        }
    }

    public function onInteract(PlayerInteractEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if($ev->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK && !$user->canDestroyAt($ev->getBlock()->getPos())){
            $ev->cancel();
            //$user->sendTip("§r§8[§bNF§8]§r\n§r§7You can't build here!");
            return;
        }

        if($ev->getBlock()->getId() !== BlockLegacyIds::CHEST && $ev->getAction() !== PlayerInteractEvent::LEFT_CLICK_BLOCK && !$user->canBuildAt($ev->getBlock()->getPos())){
            $ev->cancel();
        }
    }

    public function onPickBlock(PlayerBlockPickEvent $ev) : void {
        $ev->cancel();
    }
}