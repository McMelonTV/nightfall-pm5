<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\tag\TagManager;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\TagUtils;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class TagsForm {
    public static function sendTo(Player $sender) : void {
        $ui = new SimpleForm(static function (Player $player, $data){
            if($data === null){
                return;
            }

            $user = UserManager::getInstance()->getOnline($player);
            if($data === "__clear"){
                $user->setAppliedTag("");
                $user->setTagColor("");
                $player->sendMessage("§r§b§l> §r§7You have removed your tag.");
                return;
            }

            if($data === "__color"){
                if(!$player->hasPermission("nightfall.tag.colored")){
                    $player->sendMessage("§r§c§l>§r§7 You don't have permission to a custom color.");
                    return;
                }

                TagColorForm::sendTo($player);
                return;
            }

            $tag = TagManager::getInstance()->get($data);
            if($tag === null){
                $player->sendMessage("§r§c§l>§r§7 That tag could not be found.");
                return;
            }

            if(!$tag->hasPermission($user)){
                $player->sendMessage("§r§c§l>§r§7 You don't have permission to apply this tag.");
                return;
            }

            $user->setTagColor("");
            $user->setAppliedTag($tag->getTag());
            $player->sendMessage("§r§b§l>§r§7 You have changed your tag to §r".$tag->getTag()."§r§7.");
            return;
        });
        $ui->setTitle("§bTag selector");
        $ui->setContent("§r§7Click on a tag to apply it. It will be shown before your name in chat.\n§7Donators can also choose a color to their own liking for the tag.");

        $user = UserManager::getInstance()->getOnline($sender);

        $ui->addButton("§0> §8Clear tag §0<", -1, "", "__clear");
        if($user->getPlayer()->hasPermission("nightfall.tag.colored")){
            $ui->addButton("§0> §8Change tag color §0<", -1, "", "__color");
        }

        foreach(TagManager::getInstance()->getAll() as $tag){
            if($tag->hasPermission($user)){
                $ui->addButton($tag->getTag()."§r§8 tag\n§r§8[".TagUtils::rarityColor($tag->getRarity()).$tag->getRarityName()."§r§8]", -1, "", $tag->getId());
            }
        }

        $sender->sendForm($ui);
    }
}