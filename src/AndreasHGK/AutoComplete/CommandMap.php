<?php

declare(strict_types=1);

namespace AndreasHGK\AutoComplete;

use pocketmine\command\Command;

class CommandMap {

    /**
     * @var self
     */
    public static $instance;

    /**
     * @return self
     */
    public static function getInstance() : self {
        if(!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    /**
     * @var array|CustomCommandData[]
     */
    public $commands = [];

    /**
     * @param Command $command
     * @return CustomCommandData
     */
    public function register(Command $command) : CustomCommandData {
        $data = new CustomCommandData($command);
        $this->commands[$data->getName()] = $data;
        return $data;
    }

    /**
     * @param string $name
     * @return CustomCommandData|null
     */
    public function getCommand(string $name) : ?CustomCommandData {
        return $this->commands[$name] ?? null;
    }

    /**
     * @return array|CustomCommandData[]
     */
    public function getAll() : array {
        return $this->commands;
    }

    /**
     * @param array|CustomCommandData[] $array
     */
    public function setAll(array $array) : void {
        $this->commands = $array;
    }

    /**
     * @param CustomCommandData $data
     */
    public function addCommand(CustomCommandData $data) : void {
        $this->commands[$data->getName()] = $data;
    }

    /**
     * @param CustomCommandData $data
     */
    public function removeCommand(CustomCommandData $data) : void {
        unset($this->commands[$data->getName()]);
    }

}