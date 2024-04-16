<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\command\auction\SellSubcommand;
use AndreasHGK\Core\ui\AuctionInventory;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AuctionCommand extends MultiExecutor{

    public function __construct(){
        parent::__construct("auction", "open the auction", "/auction [sell] [price] [count]", "nightfall.command.auction", ["auc", "ah"]);
        $this->setSubcommands([new SellSubcommand(), new DefaultHelpSubcommand($this)]);
        $this->enableHelp(false);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        $return = parent::onCommand($sender, $command, $label, $args);
        if($return) {
            return true;
        }

        AuctionInventory::sendTo($sender);
        return true;
    }
}