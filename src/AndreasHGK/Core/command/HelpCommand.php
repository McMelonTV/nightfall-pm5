<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class HelpCommand extends Executor{

    public function __construct(){
        parent::__construct("Help", "Provides a list of commands", "/help [page]", "nightfall.command.help", ["h", "?", "commands", "help"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "page", CustomCommandParameter::ARG_TYPE_INT, true, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(count($args) === 0){
            $command = "";
            $pageNumber = 1;
        }elseif(is_numeric($args[count($args) - 1])){
            $pageNumber = (int) array_pop($args);
            if($pageNumber <= 0){
                $pageNumber = 1;
            }

            $command = implode(" ", $args);
        }else{
            $command = implode(" ", $args);
            $pageNumber = 1;
        }

        $pageHeight = $sender->getScreenLineHeight();
        if($command === ""){
            /** @var Command[][] $commands */
            $commands = [];
            foreach($sender->getServer()->getCommandMap()->getCommands() as $command){
                if($command->testPermissionSilent($sender)){
                    $commands[$command->getName()] = $command;
                }
            }

            ksort($commands, SORT_NATURAL | SORT_FLAG_CASE);
            $commands = array_chunk($commands, $pageHeight);
            $pageNumber = (int) min(count($commands), $pageNumber);
            if($pageNumber < 1){
                $pageNumber = 1;
            }
            $string = "§8§l<--§bNF§8--> "."\n§r§7§7 Nightfall help page §r§8(".$pageNumber." out of ".count($commands).")§r";
            if(isset($commands[$pageNumber - 1])){
                foreach($commands[$pageNumber - 1] as $command){
                    $string .= "\n §r§8§l>§r§b /".$command->getName()."§r§8 - §r§7".$command->getDescription();
                }
            }

            $sender->sendMessage($string. "\n§r§8§l<--++-->⛏");

            return true;
        }else{
            if(($cmd = $sender->getServer()->getCommandMap()->getCommand(strtolower($command))) instanceof Command){
                if($cmd->testPermissionSilent($sender)){
                    $message = TextFormat::YELLOW . "--------- " . TextFormat::WHITE . " Help: /" . $cmd->getName() . TextFormat::YELLOW . " ---------\n";
                    $message .= TextFormat::GOLD . "Description: " . TextFormat::WHITE . $cmd->getDescription() . "\n";
                    $message .= TextFormat::GOLD . "Usage: " . TextFormat::WHITE . implode("\n" . TextFormat::WHITE, explode("\n", $cmd->getUsage())) . "\n";
                    $sender->sendMessage($message);

                    return true;
                }
            }

            $sender->sendMessage(TextFormat::RED . "No help for " . strtolower($command));

            return true;
        }
    }

}