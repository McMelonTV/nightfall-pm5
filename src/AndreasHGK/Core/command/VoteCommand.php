<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class VoteCommand extends Executor{

    public function __construct(){
        parent::__construct("vote", "vote for the server", "/vote", "nightfall.command.vote");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player && !isset($args[0])){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        $string = "§8§l<--§bNF§8-->§r".
            "\n§b Nightfall§r§7 voting§r".
            "\n§b > §r§7Vote link: §bvote.nightfall.xyz§r".
            "\n§b §7Once voted, your rewards will be given automatically.".
            "\n§r§8§l<--++-->⛏";

        $sender->sendMessage($string);
        return true;
    }
}