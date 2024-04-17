<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\command;

use AndreasHGK\Core\user\UserManager;
use AndreasHGK\RankSystem\rank\RankInstance;
use AndreasHGK\RankSystem\RankSystem;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class RemoverankCommand extends BaseCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("removerank", $owner);
        $this->createPermission("ranksystem.command.removerank");
        $this->setDescription("take a rank from a player");
        $this->setUsage("/removerank <player> <rank>");
        $this->setAliases(["takerank"]);
    }

    public function onCommand(CommandSender $sender, string $commandLabel, array $args) : void {
        if(!isset($args[0])) {
            $this->sendUsage($sender);
            return;
        }

        $targetName = array_shift($args);
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

        if(!isset($args[0])) {
            $this->sendUsage($sender);
            return;
        }

        $rankId = array_shift($args);

        $rank = RankSystem::getInstance()->getRankManager()->get($rankId);
        if($rank === null) {
            $sender->sendMessage("§r§c§l> §r§7That rank could not be found.");
            return;
        }

        $user->getRankComponent()->removeRank($rank->getId());
        UserManager::getInstance()->save($user);

        $sender->sendMessage("§r§a§l> §r§7The §r§a{$rank->getName()}§r§7 rank has been removed from §r§a{$target->getName()}§r§7.");
    }

    /**
     * Send the usage message
     *
     * @param CommandSender $sender
     */
    public function sendUsage(CommandSender $sender) : void {
        $sender->sendMessage("§r§c§l> §r§7Usage: §r§a".$this->getUsage());
    }

}