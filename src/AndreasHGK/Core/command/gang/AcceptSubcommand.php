<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\achievement\Achievement;
use AndreasHGK\Core\achievement\AchievementManager;
use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class AcceptSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("accept", "accept an invite", "accept <gang>", "nightfall.command.gang.accept");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command in-game.");

            return true;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        if($user->isInGang()){
            $sender->sendMessage("§r§c§l>§r§7 You are already in a gang.");

            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter the gang that invited you.");

            return true;
        }

        $name = $args[0];
        if(!GangManager::getInstance()->exists($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 That gang does not exist.");

            return true;
        }

        $gang = GangManager::getInstance()->getByName($name);
        if(!$gang->hasInvite($sender)){
            $sender->sendMessage("§r§c§l>§r§7 You were not invited to that gang.");

            return true;
        }

        if($gang->getMemberCount() >= GangManager::MAX_MEMBERS + 1){
            $sender->sendMessage("§r§c§l>§r§7 That gang is full!.");

            return true;
        }

        $gang->revokeInvite($sender);
        $gang->addMember($sender);

        AchievementManager::getInstance()->tryAchieve($user, Achievement::TEAM_UP);

        $sender->sendMessage("§r§b§l> §r§7You have joined §b" . $gang->getName() . "§r§7.");

        Server::getInstance()->broadcastMessage("§r§b§l> §r§b" . $user->getName() . "§r§7 has joined the gang.", $gang->getOnlineMembers());
        return true;
    }
}