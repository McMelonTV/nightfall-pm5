<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class CommandBase extends NFPluginCommand {

    /** @var PluginBase */
    private $owningPlugin;

    /** @var CommandExecutor */
    private $executor;

    /**
     * @param string $name
     * @param PluginBase $owner
     */
    public function __construct(string $name, PluginBase $owner){
        parent::__construct($name, $owner);
        $this->owningPlugin = $owner;
        $this->executor = $owner;
        $this->usageMessage = "";
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$this->owningPlugin->isEnabled()){
            return false;
        }

        if(!$this->testPermission($sender)){
            return false;
        }

        try{
            $success = $this->executor->onCommand($sender, $this, $commandLabel, $args);
        }catch (\Throwable $e){
            $sender->sendMessage("§4§l> §r§cAn internal error occurred while trying to execute this command.");
            Server::getInstance()->getLogger()->logException($e);
        }

        if(!isset($success)) {
            $success = true;
        }

        if(!$success and $this->usageMessage !== ""){
            throw new InvalidCommandSyntaxException();
        }

        return $success;
    }

    public function getExecutor() : CommandExecutor{
        return $this->executor;
    }

    /**
     * @param CommandExecutor $executor
     */
    public function setExecutor(CommandExecutor $executor) : void{
        $this->executor = $executor;
    }

    /**
     * @return Plugin
     */
    public function getPlugin() : Plugin{
        return $this->owningPlugin;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        return true;
    }
}