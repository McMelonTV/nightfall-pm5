<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\koth\KothManager;
use AndreasHGK\Core\utils\TimeUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use function time;

class KothCommand extends Executor{

    public function __construct(){
        parent::__construct("koth", "get info about koth", "/koth", Core::PERM_MAIN."command.koth");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $kothManager = KothManager::getInstance();

        $running = $kothManager->getRunning();
        if($running === null){
            $next = $kothManager->getNextKoth();
            if($kothManager->spawnNext()){
                $sender->sendMessage("§r§b§l>§r§7 The next Spawn KOTH event is in §b" . TimeUtils::intToTimeString($next - time()) . "§7.");
            }else {
                $sender->sendMessage("§r§b§l>§r§7 The next Mine PvP KOTH event is in §b" . TimeUtils::intToTimeString($next - time()) . "§7.");
            }
            return true;
        }

        if($kothManager->spawnNext()){
            $sender->sendMessage("§r§b§l>§r§b " . $running->getName() . " §7KOTH is currently occurring at §bSpawn PvP§7!");
        }else{
            $sender->sendMessage("§r§b§l>§r§b " . $running->getName() . " §7KOTH is currently occurring at §bMine PvP§7!");
        }
        return true;
    }
}