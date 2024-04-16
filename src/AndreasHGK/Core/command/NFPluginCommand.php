<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

abstract class NFPluginCommand extends Command implements PluginOwned {
    use PluginOwnedTrait;

    public function __construct(string $name, Plugin $owner){
        parent::__construct($name);
        $this->owningPlugin = $owner;
        $this->usageMessage = "";
    }

    public function setPermission(?string $permission) : void{
        if($permission !== null){
            foreach(explode(";", $permission) as $perm){
                if(PermissionManager::getInstance()->getPermission($perm) === null){
                    $perm = new Permission($perm, "");
                    PermissionManager::getInstance()->addPermission($perm);
                }
            }
        }
        parent::setPermission($permission);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){

        if(!$this->owningPlugin->isEnabled()){
            return false;
        }

        if(!$this->testPermission($sender)){
            return false;
        }

        $success = $this->onCommand($sender, $this, $commandLabel, $args);

        if(!$success and $this->usageMessage !== ""){
            throw new InvalidCommandSyntaxException();
        }

        return $success;
    }

    /**
     * @param string[]      $args
     */
    abstract public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool;

}