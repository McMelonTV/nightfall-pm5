<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\command;

use AndreasHGK\Core\user\UserManager;
use AndreasHGK\RankSystem\rank\RankInstance;
use AndreasHGK\RankSystem\RankSystem;
use AndreasHGK\RankSystem\utils\StringUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class AddrankCommand extends BaseCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("addrank", $owner);
        $this->createPermission("ranksystem.command.addrank");
        $this->setDescription("give a rank to a player");
        $this->setUsage("/addrank <player> <rank> <expire> <persist>");
        $this->setAliases(["giverank"]);
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

        $currentTime = time();
        if(isset($args[0])) {
            if(is_numeric($args[0])) {
                $expire = $currentTime + (int)$args[0];
            }else{
                $sender->sendMessage("§r§c§l> §r§7The only currently supported syntax for rank expiration is expiration in seconds.");
                return;
            }
        }else{
            $expire = -1;
        }

        $persist = true;
        if(isset($args[1])) {
            if(strtolower($args[1]) === "false") $persist = false;
        }

        $user->getRankComponent()->addRank(RankInstance::create($rank, $expire, $persist));
        UserManager::getInstance()->save($user);

        $sender->sendMessage("§r§a§l> §r§a{$target->getName()} §r§7has been given the §r§a{$rank->getName()}§r§7 rank that expires in §r§a".($expire === -1 ? "never" : StringUtils::intToTimeString($expire - $currentTime, true))." §r§7and with persist=§r§a".($persist ? "true" : "false")."§r§7.");
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