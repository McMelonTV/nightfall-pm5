<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command\gang;

use AndreasHGK\Core\command\Subcommand;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class InviteSubcommand extends Subcommand{

    public function __construct(){
        parent::__construct("invite", "invite someone to your gang", "invite <player>", "nightfall.command.gang.invite");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        if(!$user->isInGang()){
            $sender->sendMessage("§r§c§l>§r§7 You are not in a gang.");
            return true;
        }

        $gang = $user->getGang();
        if(!$user->getGangRank()->equals(GangRank::OFFICER()) and !$user->getGangRank()->equals(GangRank::LEADER())){
            $sender->sendMessage("§r§c§l>§r§7 You must be a officer or above to invite a player.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Please enter a player to invite.");
            return true;
        }

        $name = $args[0];
        if(($player = Server::getInstance()->getPlayerByPrefix($name)) === null){
            $sender->sendMessage("§r§c§l>§r§7 That player is not online.");
            return true;
        }

        if($gang->getMember($player->getName()) !== null){
            $sender->sendMessage("§r§c§l>§r§7 That player is already in your gang.");
            return true;
        }

        if($gang->hasInvite($player)){
            $sender->sendMessage("§r§c§l>§r§7 That player has already been invited.");
            return true;
        }

        $gang->invitePlayer($player);

        $sender->sendMessage("§r§b§l> §r§7You have invited §b" . $player->getName() . "§r§7 to your gang.");
        $player->sendMessage("§r§b§l> §r§b" . $gang->getName() . "§r§7 has invited you, do §b/gang accept " . $gang->getName() . "§7 to accept the invite.");
        return true;
    }
}