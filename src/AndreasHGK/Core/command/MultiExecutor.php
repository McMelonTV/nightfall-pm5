<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

abstract class MultiExecutor extends Executor{

    /** @var Subcommand[] */
    protected $subcommands = [];

    protected $enableHelp = true;

    /**
     * @param Subcommand[] $subcommands
     */
    public function setSubcommands(array $subcommands) : void {
        $this->subcommands = $subcommands;
        foreach($subcommands as $key => $subcommand){
            $this->addParameterMap($key);
            $name = $subcommand->getName();
            $this->addSingleParameter($key, 0, $name, $name, $name, false, true);
        }
    }

    public function getSubcommands() : array {
        return $this->subcommands;
    }

    public function getEnableHelp() : bool {
        return $this->enableHelp;
    }

    public function enableHelp(bool $help = true) : void {
        $this->enableHelp = $help;
    }

    public function sendHelp(CommandSender $sender, Command $command, string $label, array $args) : bool {
        $help = new DefaultHelpSubcommand($this);
        return $help->onCommand($sender, $command, $label, $args);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if(!isset($args[0]) && $this->enableHelp){
            return $this->sendHelp($sender, $command, $label, []);
        }elseif(!isset($args[0]) && !$this->enableHelp){
            return false;
        }

        $subcommandName = array_shift($args);
        foreach($this->subcommands as $subcommand){
            if($subcommand->getName() === $subcommandName || in_array($subcommandName, $subcommand->getAliases())){
                if(!$subcommand->testPermission($sender)) {
                    $sender->sendMessage("§r§c§l> §r§7You don't have permission to execute that subcommand!");
                    return true;
                }
                return $subcommand->onCommand($sender, $command, $label, $args);
            }
        }

        if($this->enableHelp && ($subcommandName === "help" || $subcommandName === "?")){
            return $this->sendHelp($sender, $command, $label, $args);
        }

        return false;
    }

}