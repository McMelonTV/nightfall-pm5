<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class PluginsCommand extends Executor{

    public function __construct(){
        parent::__construct("plugins", "view a list of the plugins in the server", "/plugins", "nightfall.command.plugins", ["pl"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $plugins = Server::getInstance()->getPluginManager()->getPlugins();
        $string = "§8§l<--§bNF§8--> "."\n§r§7 Nightfall plugin list §r§8(".count($plugins).")§r";
        foreach ($plugins as $plugin){
            $string .= "\n§r §8§l> §r§b".$plugin->getName()." §7v".$plugin->getDescription()->getVersion()."§7 by §b".implode("§r§7, §b", $plugin->getDescription()->getAuthors())."§r§7";
        }

        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");
        return true;
    }

}