<?php
declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\ServerInfo;
use AndreasHGK\Core\user\UserManager;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;

class NewsForm {

    public static function sendTo(Player $sender) : void {
        $user = UserManager::getInstance()->getOnline($sender);
        $user->setLastPatchNotes(ServerInfo::getVersion());
        $ui = new CustomForm(null);

        $ui->setTitle("§bPatch notes - ".ServerInfo::getVersion());

        $str = ServerInfo::getPatchNotes();

        $ui->addLabel($str);

        $sender->sendForm($ui);
    }
}