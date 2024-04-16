<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\gang\GangRank;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\IntUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat;

class ChatListener implements Listener {

    public function onChat(PlayerChatEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->get($player);
        if(!$user->hasSeenRules()){
            $player->sendMessage("§c§l>§r§7 You cannot chat without having read the rules! Do §c/rules §r§7to read them and to gain access to chat.");
            $ev->cancel();
            return;
        }

        $msg = $ev->getMessage();
        if($user->isMuted()){
            $ev->cancel();
            $player->sendMessage("§c§l>§r§7 You can't talk while muted!");
            Core::getInstance()->getLogger()->info("§4§l[MUTED]§r§7 ".$player->getName()."§r§7: §f".$msg);
            return;
        }

        $format = "• ";
        if($user->isInGang()){
            $gang = $user->getGang();
            $gangRank = $user->getGangRank();
            if($gangRank === null){
                $gangRank = GangRank::RECRUIT();
            }

            $gr = "";
            if($gangRank->equals(GangRank::RECRUIT())){
                $gr = "-";
            }elseif($gangRank->equals(GangRank::OFFICER())){
                $gr = "*";
            }elseif($gangRank->equals(GangRank::LEADER())){
                $gr = "**";
            }

            $format .= "§r§8[§c".$gr."§7".$gang->getName()."§r§8] ";
        }

        $format .= "§r§7".IntUtils::toRomanNumerals($user->getPrestige())."§r§8§l-§r§7".TextFormat::colorize($user->getMineRank()->getTag())." §r§8|";
        if($user->hasAppliedTag()){
            if($user->getTagColor() !== ""){
                $format .= "§r §".$user->getTagColor().TextFormat::clean($user->getAppliedTag())."§r§8 |";
            }else{
                $format .= "§r ".TextFormat::colorize($user->getAppliedTag())."§r§8 |";
            }
        }

        $format .= " §r".$user->getRank()->getRank()->getPrefix()." §r§7".$player->getDisplayName()."§r§8:§r §f";
        $format = TextFormat::colorize($format);
        if($player->hasPermission("nightfall.chat.colored")) {
            $msg = TextFormat::colorize($msg."");
        }

        $format .= $msg;
        $format .= "§r⛏";
        $ev->setFormat($format);
    }
}