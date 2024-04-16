<?php

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

abstract class Subcommand{

    protected $name;
    protected $desc;
    protected $usage;
    protected $permission;
    protected $aliases = [];

    public function getName() : string {
        return $this->name;
    }

    public function getDesc() : string {
        return $this->desc;
    }

    public function getUsage() : string {
        return $this->usage;
    }

    public function getPermission() : string {
        return $this->permission;
    }

    public function getAliases() : array {
        return $this->aliases;
    }

    public function testPermission(CommandSender $sender) : bool {
        return $sender->hasPermission($this->getPermission());
    }

    protected function __construct(string $name, string $desc, string $usage, string $permission, array $aliases = []){
        $this->name = $name;
        $this->desc = $desc;
        $this->usage = $usage;
        $this->permission = $permission;
        $this->aliases = $aliases;
    }

    abstract public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool;

}