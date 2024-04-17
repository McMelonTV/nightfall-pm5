<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\command;

use AndreasHGK\Core\user\UserManager;
use AndreasHGK\RankSystem\RankSystem;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class PlayerranksCommand extends BaseCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("playerranks", $owner);
        $this->createPermission("ranksystem.command.playerranks");
        $this->setDescription("Display a player's ranks");
        $this->setUsage("/playerranks <player>");
        $this->setAliases(["pranks"]);
    }

    public function onCommand(CommandSender $sender, string $commandLabel, array $args) : void {
        if(!isset($args[0])) {
            $sender->sendMessage("§r§c§l> §r§7Please enter a player to show ranks for.");
            return;
        }

        $targetName = implode($args);
        $target = Server::getinstance()->getPlayerExact($targetName);

        if($target === null) {
            $target = Server::getInstance()->getOfflinePlayer($targetName);
        }

        if(!$target->hasPlayedBefore() && !$target instanceof Player) {
            $sender->sendMessage("§r§c§l> §r§7The provided player could not be found.");
            return;
        }

        $user = UserManager::getInstance()->get($target);

        if($user === null) {
            $sender->sendMessage("§r§c§l> §r§7The provided player has no associated data.");
            return;
        }

        $str = "§r§8<--§r§aNF§r§8-->\n§r§7 §r§a{$target->getName()}§r§7's ranks:§r";

        foreach($user->getRankComponent()->getRanks() as $rank) {
            $str .= "\n§r§8 - §r§7Id: §r§a{$rank->getRank()->getId()} §r§8| §r§7Expire: §r§a{$rank->getExpire()} §r§8| §r§7Persistent: §r§a".($rank->isPersistent() ? "yes" : "no");
        }

        $sender->sendMessage($str."\n§r§8§l<--++-->⛏");
    }

}