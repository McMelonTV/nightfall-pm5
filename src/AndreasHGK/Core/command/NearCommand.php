<?php

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class NearCommand extends Executor{

    public function __construct(){
        parent::__construct("near", "check nearby players", "/near", "nightfall.command.near");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) return false;
        $str = "§8§l<--§bNF§8--> "."\n§r§7 Nearby players in world §r§b".$sender->getWorld()->getDisplayName();

        $players = [];
        foreach($sender->getWorld()->getPlayers() as $player){
            if(!$sender->canSee($player)) continue;
            if($player === $sender) continue;
            $players[] = $player;
        }

        $pos = $sender->getPosition();
        usort($players, function ($item, $item2) use ($pos) {
            return $pos->distanceSquared($item->getPosition()) > $pos->distanceSquared($item2->getPosition());
        });

        foreach($players as $player){
            $str .= "\n§b" . $player->getName() . "§r§8 ({$pos->distance($player->getPosition())}m)";
        }

        $sender->sendMessage(TextFormat::colorize($str."\n§r§8§l<--++-->⛏"));
        return true;
    }
}