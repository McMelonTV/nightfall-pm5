<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\pvp\PVPZoneManager;
use AndreasHGK\Core\task\DelayedCommandTask;
use AndreasHGK\Core\user\UserManager;
use pocketmine\block\BlockLegacyIds;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;

class PVPListener implements Listener {

    /**
     * @param PlayerCommandPreprocessEvent $ev
     *
     * @priority High
     */
    public function onCommand(PlayerCommandPreprocessEvent $ev) : void {
        $player = $ev->getPlayer();
        if(PVPZoneManager::getInstance()->isPVPZone($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), $player->getWorld())){
            $msg = $ev->getMessage();
            if(strpos($ev->getMessage(), "/") !== 0) {
                return;
            }

            $cname = strtolower(substr($msg, 1));
            $args = [];
            preg_match_all('/"((?:\\\\.|[^\\\\"])*)"|(\S+)/u', $cname, $matches);
            foreach($matches[0] as $k => $_){
                for($i = 1; $i <= 2; ++$i){
                    if($matches[$i][$k] !== ""){
                        $args[$k] = stripslashes($matches[$i][$k]);
                        break;
                    }
                }
            }

            $sentCommandLabel = "";
            $command = Server::getInstance()->getCommandMap()->matchCommand($sentCommandLabel, $args);
            if($command === null) {
                return;
            }

            switch (strtolower($command->getName())){
                case "fly":
                //case "heal":
                case "kill":
                case "mines":
                case "size":
                case "plot":
                case "shop":
                    $player->sendMessage("§r§c§l>§r§7 You can't execute this command in a PvP area!");
                    $ev->cancel();
                    break;
                case "mine":
                case "teleport":
                case "world":
                case "spawn":
                case "plots":
                case "crates":
                case "auction":
                case "vault":
                case "warp":
                    $ev->cancel();
                    $user = UserManager::getInstance()->getOnline($player);
                    if($user->getLastHit()+10 > time()){
                        $player->sendMessage("§r§c§l>§r§7 You can't execute this command while in combat. Please wait §c".($user->getLastHit()+10-time())."§r§7 more seconds.");
                        return;
                    }

                    $player->sendMessage("§r§b§l>§r§7 The command will be executed in §b5 seconds§7. Please hold still while waiting for the command to execute.");

                    $user->setWaitingforCommand(true);
                    $task = new DelayedCommandTask($player, $msg);
                    $handler = Core::getInstance()->getScheduler()->scheduleDelayedTask($task, (int)(Server::getInstance()->getTicksPerSecondAverage() * 5));
                    $user->cancelCommandDelayTask();
                    $user->setCommandDelayTask($handler);
                    break;
            }
        }
    }

    public function onPVP(EntityDamageByEntityEvent $ev) : void {
        $damager = $ev->getDamager();
        $target = $ev->getEntity();
        if(!$damager instanceof Player || !$target instanceof Player) {
            return;
        }

        $damagerUser = UserManager::getInstance()->getOnline($damager);
        $targetUser = UserManager::getInstance()->getOnline($target);
        if(!PVPZoneManager::getInstance()->canPvPHappen($damagerUser, $targetUser)){
            $damagerUser->sendTip("§8[§bNF§8]§r\n§r§7You can't PvP here.");
            $ev->cancel();
        }
    }

    public function onMove(PlayerMoveEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        $to = $ev->getTo();
        $from = $ev->getFrom();

        $canFlyTo = PVPZoneManager::getInstance()->canFlyAt($to->x, $to->y, $to->z, $to->world);
        if($player->getScale() !== 1.0 && !$canFlyTo){
            $player->setScale(1.0);
            $user->sendTip("§8[§bNF§8]\n§r§7/size is not enabled in PvP");
        }

        if($player->getGamemode()->equals(GameMode::SURVIVAL()) && !$user->getAdminMode() && !$canFlyTo && $player->getAllowFlight() && $user->isFlying()) {
            $player->setAllowFlight(false);
            $player->setFlying(false);
            $user->sendTip("§8[§bNF§8]\n§r§7Flying is not enabled in PvP");
        }

        $canFlyFrom = PVPZoneManager::getInstance()->canFlyAt($from->x, $from->y, $from->z, $from->world);
        if(!$canFlyFrom && $user->isFlying() && $canFlyTo){
            $player->setAllowFlight(true);
        }

        if(!$canFlyFrom && $user->getSize() !== 100 && $canFlyTo){
            $player->setScale($user->getSize()/100);
        }
    }

    /**
     * @param EntityDamageByEntityEvent $ev
     *
     * @priority High
     */
    public function onDamageByEntity(EntityDamageByEntityEvent $ev) : void {
        $player = $ev->getEntity();
        $damager = $ev->getDamager();

        if(!$player instanceof Player || !$damager instanceof Player) {
            return;
        }

        $user = UserManager::getInstance()->getOnline($player);
        $dUser = UserManager::getInstance()->getOnline($damager);
        $gang = $user->getGang();
        $dGang = $dUser->getGang();
        if($dGang !== null && $gang !== null){
            if(($gang) === ($dGang)){
                $dUser->sendTip("§8[§bNF§8]\n§r§7Friendly fire is disabled");
                $ev->cancel();
                return;
            }

            if($gang->isAlliedWith($dGang)){
                $dUser->sendTip("§8[§bNF§8]\n§r§7Friendly fire is disabled");
                $ev->cancel();
                return;
            }
        }

        $user->setlastHitter($damager->getName());
        if($user->getLastHit()+10 < time()){
            $player->sendMessage("§r§b§l>§r§7 You are now in combat. Please don't log out, or you will be killed.");
        }

        $user->updateLastHit();
        if($dUser->getLastHit()+10 < time()){
            $damager->sendMessage("§r§b§l>§r§7 You are now in combat. Please don't log out, or you will be killed.");
        }

        $dUser->updateLastHit();
    }

    public function onEntityDamageByBlock(EntityDamageByBlockEvent $ev) : void{
        $player = $ev->getEntity();
        if(!$player instanceof Player) {
            return;
        }

        if($ev->getDamager()->getId() === BlockLegacyIds::MAGMA){
            $ev->cancel();
        }
    }

    /**
     * @param EntityDamageEvent $ev
     *
     * @priority Highest
     */
    public function onDeath(EntityDamageEvent $ev) : void {
        $player = $ev->getEntity();
        if(!$player instanceof Player) {
            return;
        }

        if($ev->getFinalDamage() < $ev->getEntity()->getHealth()) {
            return;
        }

        $user = UserManager::getInstance()->getOnline($player);
        if($user === null) return;
        if($user->getLastHitter() === "" || $user->getLastHit()+20 < time()) {
            return;
        }

        $user->addDeath();

        $ku = UserManager::getInstance()->get(Server::getInstance()->getOfflinePlayer($user->getLastHitter()));
        if($ku !== null){
            $ku->addKill();
            $atk = $ku->getPlayer();
            if($atk instanceof Player && $atk->getWorld()->getDisplayName() === Core::PVPMINEWORLD) {
                $ku->setPrestigePoints($ku->getPrestigePoints() + 100);
                $atk->sendMessage("§r§b§l> §r§7You earned §b100 §opp§r§7 for killing §b" . $player->getName() . "§r§7.");
            }
        }

        switch ($ev->getCause()){
            case EntityDamageEvent::CAUSE_LAVA:
            case EntityDamageEvent::CAUSE_FIRE:
            case EntityDamageEvent::CAUSE_FIRE_TICK:
                switch (mt_rand(0, 3)){
                    case 0:
                        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 burned to death after fighting §b".$user->getLastHitter()."§r§7.");
                        break;
                    case 1:
                        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was fried by §b".$user->getLastHitter()."§r§7.");
                        break;
                    case 2:
                        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was fried after escaping §b".$user->getLastHitter()."§r§7.");
                        break;
                    default:
                        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 went up in flames after fighting §b".$user->getLastHitter()."§r§7.");
                        break;
                }
                break;
            case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                $damager = Server::getInstance()->getPlayerExact($user->getLastHitter());
                if($damager instanceof Player){
                    switch (mt_rand(0, 6)){
                        case 0:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was destroyed by §b".$user->getLastHitter()."§r§7 with their §r§f".$damager->getInventory()->getItemInHand()->getName()."§r§7.");
                            break;
                        case 1:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was mauled by §b".$user->getLastHitter()."§r§7 with their §r§f".$damager->getInventory()->getItemInHand()->getName()."§r§7.");
                            break;
                        case 2:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was killed by §b".$user->getLastHitter()."§r§7 with their §r§f".$damager->getInventory()->getItemInHand()->getName()."§r§7.");
                            break;
                        case 3:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was pacified by §b".$user->getLastHitter()."§r§7 with their §r§f".$damager->getInventory()->getItemInHand()->getName()."§r§7.");
                            break;
                        case 4:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was obliterated by §b".$user->getLastHitter()."§r§7 with their §r§f".$damager->getInventory()->getItemInHand()->getName()."§r§7.");
                            break;
                        case 5:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 got their feelings hurt by §b".$user->getLastHitter()."§r§7 with their §r§f".$damager->getInventory()->getItemInHand()->getName()."§r§7.");
                            break;
                        default:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was neutralized by §b".$user->getLastHitter()."§r§7 with their §r§f".$damager->getInventory()->getItemInHand()->getName()."§r§7.");
                            break;
                    }
                }else{
                    switch (mt_rand(0, 6)){
                        case 0:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was destroyed by §b".$user->getLastHitter()."§r§7 with their §r§f"."§r§7.");
                            break;
                        case 1:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was mauled by §b".$user->getLastHitter()."§r§7.");
                            break;
                        case 2:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was killed by §b".$user->getLastHitter()."§r§7.");
                            break;
                        case 3:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was pacified by §b".$user->getLastHitter()."§r§7.");
                            break;
                        case 4:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was obliterated by §b".$user->getLastHitter()."§r§7.");
                            break;
                        case 5:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 got their feelings hurt by §b".$user->getLastHitter()."§r§7.");
                            break;
                        default:
                            Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was neutralized by §b".$user->getLastHitter()."§r§7.");
                            break;
                    }
                }
                break;
            case EntityDamageEvent::CAUSE_PROJECTILE:
                if(Server::getInstance()->getPlayerExact($user->getLastHitter())){
                    $distance = round($player->getPosition()->distance(Server::getInstance()->getPlayerExact($user->getLastHitter())->getPosition()), 1);
                }else{
                    $distance = null;
                }

                switch (mt_rand(0, 2)){
                    case 0:
                        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was sniped by §b".$user->getLastHitter()."§r§7.". ($distance === null ? "" : "§8(".$distance."m)§r"));
                        break;
                    case 1:
                        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was shot to death by §b".$user->getLastHitter()."§r§7.". ($distance === null ? "" : "§8(".$distance."m)§r"));
                        break;
                    case 2:
                        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 was shot by §b".$user->getLastHitter()."§r§7.". ($distance === null ? "" : "§8(".$distance."m)§r"));
                        break;
                }
                break;
            case EntityDamageEvent::CAUSE_FALL:
                switch (mt_rand(0, 2)){
                    case 0:
                        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 fell to death after fighting §b".$user->getLastHitter()."§r§7.");
                        break;
                    case 1:
                        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 fell to death while trying to escape §b".$user->getLastHitter()."§r§7.");
                        break;
                    case 2:
                        Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 got crushed while fighting §b".$user->getLastHitter()."§r§7.");
                        break;
                }
                break;
            default:
                Server::getInstance()->broadcastMessage("§8[§bNF§8] §r§7§b".$player->getName()."§r§7 died while trying to escape from §b".$user->getLastHitter()."§r§7.");
                break;
        }
    }

    public function onDeathEvent(PlayerDeathEvent $ev) : void {
        $player = $ev->getPlayer();
        if($player->getWorld()->getDisplayName() !== Core::PVPMINEWORLD) {
            $ev->setKeepInventory(true);
            $ev->setXpDropAmount(0);
        }
        $ev->setDeathMessage("");
    }

    public function onTeleport(EntityTeleportEvent $ev) : void {
        $player = $ev->getEntity();
        if(!$player instanceof Player) {
            return;
        }

        $user = UserManager::getInstance()->getOnline($player);
        if($user === null) {
            return;
        }

        $to = $ev->getTo();
        $from = $ev->getFrom();

        $canFlyTo = PVPZoneManager::getInstance()->canFlyAt($to->x, $to->y, $to->z, $to->getWorld());
        if((int)$player->getScale() !== (int)1 && !$canFlyTo){
            $player->setScale(1);
            $user->sendTip("§8[§bNF§8]\n§r§7/size is not enabled in PvP");
        }

        if($player->isSurvival(true) && !$user->getAdminMode() && !$canFlyTo && $player->getAllowFlight() && $user->isFlying()) {
            $player->setAllowFlight(false);
            $player->setFlying(false);
            $user->sendTip("§8[§bNF§8]\n§r§7Flying is not enabled in PvP");
        }

        $canFlyFrom = PVPZoneManager::getInstance()->canFlyAt($from->x, $from->y, $from->z, $from->getWorld());
        if(!$canFlyFrom && $user->isFlying() && $canFlyTo){
            $player->setAllowFlight(true);
            $player->setAllowFlight(true);
        }

        if(!$canFlyFrom && $user->getSize() !== 100 && $canFlyTo){
            $player->setScale($user->getSize()/100);
        }
    }
}