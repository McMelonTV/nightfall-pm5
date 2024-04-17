<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\command;

use AndreasHGK\RankSystem\RankSystem;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class ListranksCommand extends BaseCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("listranks", $owner);
        $this->createPermission("ranksystem.command.listranks");
        $this->setDescription("list all available ranks");
        $this->setUsage("/listranks");
    }

    public function onCommand(CommandSender $sender, string $commandLabel, array $args) : void {
        $rankManager = RankSystem::getInstance()->getRankManager();

        $str = "§r§8<--§r§aNF§r§8-->\n§r§7 List of available ranks§r";

        foreach($rankManager->getAll() as $rank) {
            $str .= "\n§r§8 - §r§7Id: §r§a{$rank->getId()}"
                ."§r§8 | §r§7Name: §r§a{$rank->getName()}"
                ."§r§8 | §r§7Priority: §r§a{$rank->getPriority()}";
        }

        $sender->sendMessage($str."\n§r§8§l<--++-->⛏");
    }

}