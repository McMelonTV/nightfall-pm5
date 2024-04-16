<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class DefaultHelpSubcommand extends Subcommand {

    public $command;

    public function __construct(MultiExecutor $exec){
        $this->command = $exec;
        parent::__construct("help", "get help for a command", "help", "", ["?"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if(count($args) === 0){
            $pageNumber = 1;
        }elseif(is_numeric($args[count($args) - 1])){
            $pageNumber = (int) array_pop($args);
            if($pageNumber <= 0){
                $pageNumber = 1;
            }
        }else{
            $sender->sendMessage("§r§c§l>§r§7 Please enter a valid page number.");
            return true;
        }

        $pageHeight = $sender->getScreenLineHeight();

        /** @var Subcommand[][] $commands */
        $subcommands = [];
        $subcommands["help"] = $this;
        foreach($this->command->getSubcommands() as $subcommand){
            /** @var $subcommand Subcommand */
            if($subcommand->testPermission($sender)){
                $subcommands[$subcommand->getName()] = $subcommand;
            }
        }

        ksort($subcommands, SORT_NATURAL | SORT_FLAG_CASE);
        $subcommands = array_chunk($subcommands, $pageHeight);
        $pageNumber = (int) min(count($subcommands), $pageNumber);
        if($pageNumber < 1){
            $pageNumber = 1;
        }

        $string = "§8§l<--§bNF§8--> "."\n§r§7§7 /".$this->command->getName()."§r§7 help page §r§8(".$pageNumber." out of ".count($subcommands).")§r";
        if(isset($subcommands[$pageNumber - 1])){
            foreach($subcommands[$pageNumber - 1] as $subcommand){
                $string .= "\n §r§8§l>§r§b /".$this->command->getName()." ".$subcommand->getName()."§r§8 - §r§7".$subcommand->getDesc();
            }
        }

        $sender->sendMessage($string. "\n§r§8§l<--++-->⛏");

        return true;
    }

}