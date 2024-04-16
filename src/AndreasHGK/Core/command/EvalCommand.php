<?php

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\Core;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class EvalCommand extends Executor{

    public function __construct(){
        parent::__construct("eval", "evaluate php code", "/eval <code>", "nightfall.command.eval");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "code", CustomCommandParameter::ARG_TYPE_STRING, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(count($args) === 0){
            $sender->sendMessage("§c§l> §r§7Please input code to execute");
            return true;
        }

        // pre-defined variables
        $server = $sender->getServer();
        $core = Core::getInstance();
        try{
            $sender->sendMessage("§b§l> §r§7Executing...");
            eval(implode(" ", $args));
        }catch(\Throwable $e){
            $sender->sendMessage("§c§l> §r§7Error: §b" . $e->getMessage());
        }

        return true;
    }
}