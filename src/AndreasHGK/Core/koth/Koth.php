<?php

declare(strict_types=1);

namespace AndreasHGK\Core\koth;

use AndreasHGK\Core\achievement\Achievement;
use AndreasHGK\Core\achievement\AchievementManager;
use AndreasHGK\Core\item\CrateKey;
use AndreasHGK\Core\user\UserManager;
use JackMD\ScoreFactory\ScoreFactory;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use function mt_rand;

final class Koth{

    private Server $server;

    private string $name;
    private string $color;
    private int $keys;

    private Vector3 $minPos;
    private Vector3 $maxPos;
    private World $world;

    private int $capTime;
    private ?Player $capper = null;
    private int $capperTimer;

    public function __construct(string $name, string $color, int $capTime, int $keys, Vector3 $minPos, Vector3 $maxPos, World $world) {
        $this->server = $world->getServer();
        $this->name = $name;
        $this->color = $color;
        $this->capTime = $capTime;
        $this->keys = $keys;
        $this->minPos = Vector3::minComponents($minPos, $maxPos);
        $this->maxPos = Vector3::maxComponents($minPos, $maxPos);
        $this->world = $world;

        $this->capperTimer = $this->capTime;
    }

    public function tick() : bool{
        $players = $this->server->getOnlinePlayers();
        shuffle($players);
        $onKoth = [];
        foreach($players as $player){
            KothScoreboard::update($player);
            if($player->getWorld() !== $this->getWorld()){
                continue;
            }

            $pos = $player->getPosition();
            if($this->isInArea($pos->x, $pos->y, $pos->z)) {
                $onKoth[] = $player;
            }
        }

        if(count($onKoth) === 0 && $this->capper === null){
            return false;
        }

        $capperStillOn = false;
        foreach($onKoth as $player){
            if($this->capper === $player){
                $capperStillOn = true;
                break;
            }
        }

        if($this->capper !== null && !$capperStillOn){
            //Server::getInstance()->broadcastMessage("§r§8[§bNF§8] §r§b" . $this->capper->getName() . " §7has been knocked off KOTH", $players);
            $this->capper = null;
            $this->capperTimer = $this->capTime;
        }

        foreach($onKoth as $player) {
            $user = UserManager::getInstance()->getOnline($player);
            if(!$user->isInGang()){
                $player->sendPopup("You must be in a gang to be the king of the hill");
                continue;
            }elseif(!$capperStillOn){
                $this->capper = $player;
                $this->capperTimer = $this->capTime;
                $capperStillOn = true;

                //Server::getInstance()->broadcastMessage($player->getName() . " is now capturing KOTH", $players);
                continue;
            }

            if($this->capper === $player && --$this->capperTimer <= 0){
                if($this->keys === 0) {
                    $count = mt_rand(3, 4);
                }else{
                    $count = $this->keys;
                }
                $user->safeGive((new CrateKey())->getVariant(120)->setCount($count));
                AchievementManager::getInstance()->tryAchieve($user, Achievement::KOTH);
                $this->server->broadcastMessage("§8§l<-----§aKOTH§8----->§r".
                    "\n §a§l> §r§8[§7".$user->getGang()->getName()."§r§8] " . "§r§a" . $user->getName() . " §7has won KOTH!§r".
                    "\n §a§l> §r§7Thank you everyone who participated!§r".
                    "\n §a§l> §r§7The next Mine PvP KOTH will start in 30 hours.§r".
                    "\n §a§l> §r§7Spawn PvP KOTH will start in 6 hours.§r".
                    "\n§a§8§l<-----++----->");

                foreach($this->server->getOnlinePlayers() as $onlinePlayer){
                    ScoreFactory::removeScore($onlinePlayer);
                }
                return true;
            }
        }

        return false;
    }

    public function getName() : string{
        return $this->name;
    }

    public function getColor() : string{
        return $this->color;
    }

    public function getTimeLeft() : int{
        return $this->capperTimer;
    }

    public function getCapper() : ?Player{
        return $this->capper;
    }

    public function isInArea($x, $y, $z) : bool{
        $minPos = $this->minPos;
        $maxPos = $this->maxPos;
        return ($x >= $minPos->x) && ($x <= $maxPos->x)
            && ($y >= $minPos->y) && ($y <= $maxPos->y)
            && ($z >= $minPos->z) && ($z <= $maxPos->z);
    }

    public function getWorld() : World{
        return $this->world;
    }
}