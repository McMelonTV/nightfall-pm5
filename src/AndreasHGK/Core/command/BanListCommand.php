<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\user\BannedUserManager;
use AndreasHGK\Core\utils\TimeUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class BanListCommand extends Executor{

    public function __construct(){
        parent::__construct("banlist", "view all banned players", "/banlist", "nightfall.command.banlist");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $string = "§8§l<--§bNF§8-->§r".
            "\n§b Nightfall§r§7 banlist§r";

        foreach(BannedUserManager::getInstance()->getAll() as $ban) {
            $t = $ban->getBanExpire()-time();
            $string .= "\n§b > §r§b".$ban->getName().": §r§7".($ban->isTempBan() && $t > 0 ? TimeUtils::intToShortTimeString($t) : "permanent");
        }

        $string .= "\n§r§8§l<--++-->⛏";
        $sender->sendMessage($string);
        return true;
    }

}