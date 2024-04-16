<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\user\UserManager;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class NickForm {

    public static function sendTo(Player $sender) : void {
        $ui = new CustomForm(static function (Player $player, ?array $data){
            if($data === null){
                return;
            }

            $nick = (string)$data["nick"];

            $user = UserManager::getInstance()->get($player);
            if($nick === "clear"){
                $user->setNick("");
                $player->sendMessage("§r§b§l>§r§7 Your nickname has been cleared.");
                return;
            }

            if(strlen(TextFormat::clean($nick)) > 20){
                $player->sendMessage("§r§c§l>§r§7 Please enter a shorter nickname.");
            }

            try{
                $user->setNick($nick);
            }catch(\Throwable $e){

            }

            $player->sendMessage("§r§b§l>§r§7 Your nickname has been set to §b".$nick."§r§7.");
        });

        $ui->setTitle("§b/nick");
        $ui->addLabel("§7Please enter the nickname you want to give yourself. Enter 'clear' or nothing to remove your current nickname.");
        $ui->addInput("§b§l> §r§7Nickname", "", null, "nick");

        $sender->sendForm($ui);
    }
}