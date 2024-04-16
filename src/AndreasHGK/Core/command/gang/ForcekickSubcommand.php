<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class ForcekickSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("forcekick", "forcefully kick a gang member", "forcekick <gang> <player>", "nightfall.command.gang.forcekick");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        if(!$this->testPermission($sender)) {
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a gang to force kick someone from.");
            return true;
        }

        $gang = GangManager::getInstance()->getByName(array_shift($args));
        if($gang === null) {
            $sender->sendMessage("§r§c§l>§r§7 That gang could not be found.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a player to kick.");
            return true;
        }

        $targetName = implode(" ", $args);
        $player = Server::getInstance()->getPlayerByPrefix($targetName);
        if($player === null){
            if(Server::getInstance()->hasOfflinePlayerData($targetName)){
                $player = Server::getInstance()->getOfflinePlayer($targetName);
            }else{
                $sender->sendMessage("§r§c§l>§r§7 That player has never connected.");
                return true;
            }
        }

        $targetUser = UserManager::getInstance()->get($player);

        if($targetUser->getGangRank()->equals(GangRank::LEADER())){
            $sender->sendMessage("§r§c§l>§r§7 You cannot kick the leader.");
            return true;
        }

        $gang->removeMember($targetUser->getName());

        $sender->sendMessage("§r§b§l> §r§7You have kicked §b" . $targetUser->getName() . "§r§7 from the §r§b{$gang->getName()}§r§7 gang.");
        if($player instanceof Player){
            $player->sendMessage("§r§b§l> §r§7You have been kicked from the §r§b{$gang->getName()}§r§7 gang.");
        }

        return true;
    }
}