<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\rank\MineRankManager;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\IntUtils;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class MinesForm {

    public static function sendTo(Player $sender) : void {

        $ui = new SimpleForm(static function (Player $player, $data){
            if($data === null){
                return;
            }

            $mine = MineManager::getInstance()->get((int)$data);

            if($mine === null){
                $player->sendMessage("§r§c§l> §r§7That mine was not found.");
                return;
            }

            $user = UserManager::getInstance()->getOnline($player);

            if(!$mine->hasAccessTo($user)){
                if($mine->getId() === $user->getMineRankId() + 1){
                    if(!$user->tryRankUp()) return;
                }else{
                    $player->sendMessage("§r§c§l> §r§7You don't have access to this mine.");
                    return;
                }
            }

            $player->teleport($mine->getSpawnPosition());
            $player->sendMessage("§r§b§l> §r§7You have been teleported to mine §b".$mine->getName()."§r§7.");
        });

        $ui->setTitle("§bMine teleporter");

        $user = UserManager::getInstance()->getOnline($sender);

        $mines = MineManager::getInstance()->getAll();
        ksort($mines);
        foreach($mines as $mine){
            if($mine->hasAccessTo($user)){
                $ui->addButton("§b".$mine->getName()." §8mine\n§8[§aUnlocked§8]", -1, "", (string)$mine->getId());
            }elseif($mine->isDisabled()){
                $ui->addButton("§b".$mine->getName()." §8mine\n§8[§7Disabled§8]", -1, "", (string)$mine->getId());
            }elseif(MineRankManager::getInstance()->get($mine->getId()) !== null){
                $mr = MineRankManager::getInstance()->get($mine->getId());
                $price = (int)($mr->getPrice() + ($mr->getPrice() * 0.6 * ($user->getPrestige() - 1 )));
                $ui->addButton("§b".$mine->getName()." §8mine\n§8[§c$".IntUtils::shortNumberRounded($price)."§8]", -1, "", (string)$mine->getId());
            }else{
                $ui->addButton("§b".$mine->getName()." §8mine\n§8[§cLocked§8]", -1, "", (string)$mine->getId());
            }
        }

        $sender->sendForm($ui);
    }
}