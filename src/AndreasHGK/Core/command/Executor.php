<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\ArrayParameter;
use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\AutoComplete\parameter\MagicParameter;
use AndreasHGK\AutoComplete\parameter\SingleParameter;
use AndreasHGK\AutoComplete\ParameterMap;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

abstract class Executor implements CommandExecutor{

    protected $name;
    protected $desc;
    protected $usage;
    protected $permission;
    protected $aliases = [];
    protected $parameters = [];

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

    protected function __construct(string $name, string $desc, string $usage, string $permission, array $aliases = []){
        $this->name = $name;
        $this->desc = $desc;
        $this->usage = $usage;
        $this->permission = $permission;
        $this->aliases = $aliases;
    }

    abstract public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool;

    //easy parameter addition for executors

    /**
     * @param int $x
     * @param string $permission
     * @return ParameterMap
     */
    public function addParameterMap(int $x, string $permission = "") : ParameterMap {
        $map = new ParameterMap($permission);
        $this->parameters[$x] = $map;
        return $map;
    }

    /**
     * @param int $x
     * @param int $y
     * @param string $name
     * @param int $type
     * @param bool $optional
     * @param bool $force
     * @return CustomCommandParameter|null
     */
    public function addNormalParameter(int $x, int $y, string $name, $type = CustomCommandParameter::ARG_TYPE_INT, bool $optional = false, bool $force = false) : ?CustomCommandParameter {
        if($this->parameterExists($x, $y) && !$force) return null;
        $param = new CustomCommandParameter($name, $type, $optional);
        $this->setParameter($x, $y, $param);
        return $param;
    }

    /**
     * @param int $x
     * @param int $y
     * @param string $name
     * @param string $typeName
     * @param bool $optional
     * @param bool $force
     * @return CustomCommandParameter|null
     */
    public function addMagicParameter(int $x, int $y, string $name, $typeName = "", bool $optional = false, bool $force = false) : ?CustomCommandParameter {
        if($this->parameterExists($x, $y) && !$force) return null;
        $param = new MagicParameter($name, $typeName, $optional);
        $this->setParameter($x, $y, $param);
        return $param;
    }

    /**
     * @param int $x
     * @param int $y
     * @param string $name
     * @param string $typeName
     * @param array $contents
     * @param bool $optional
     * @param bool $softEnum
     * @param bool $force
     * @return CustomCommandParameter|null
     */
    public function addArrayParameter(int $x, int $y, string $name, string $typeName, array $contents, bool $optional = false, bool $softEnum = false, bool $force = false) : ?CustomCommandParameter {
        if($this->parameterExists($x, $y) && !$force) return null;
        $param = new ArrayParameter($name, $typeName, $contents, $optional, $softEnum);
        $this->setParameter($x, $y, $param);
        return $param;
    }

    /**
     * @param int $x
     * @param int $y
     * @param string $name
     * @param string $typeName
     * @param string $string
     * @param bool $optional
     * @param bool $force
     * @return CustomCommandParameter|null
     */
    public function addSingleParameter(int $x, int $y, string $name, string $typeName, string $string, bool $optional = false, bool $force = false) : ?CustomCommandParameter {
        if($this->parameterExists($x, $y) && !$force) return null;
        $param = new SingleParameter($name, $typeName, $string, $optional);
        $this->setParameter($x, $y, $param);
        return $param;
    }

    /**
     * @param int $x
     * @param int $y
     * @return bool
     */
    public function parameterExists(int $x, int $y) : bool {
        return $this->parameters[$x]->getParameter($y) !== null;
    }

    /**
     * @param int $x
     * @param int $y
     * @return CustomCommandParameter|null
     */
    public function getParameter(int $x, int $y) : ?CustomCommandParameter {
        if(!isset($this->parameters[$x])) return null;
        return $this->parameters[$x]->getParameter($y) ?? null;
    }

    /**
     * @param int $x
     * @param int $y
     * @param CustomCommandParameter $param
     */
    public function setParameter(int $x, int $y, CustomCommandParameter $param) : void {
        if(!isset($this->parameters[$x])) $this->parameters[$x] = new ParameterMap();
        $this->parameters[$x]->setParameter($y, $param);
    }

    /**
     * @param int $x
     * @return ParameterMap
     */
    public function getParameterMap(int $x) : ParameterMap {
        return $this->parameters[$x];
    }

    /**
     * @param int $x
     * @param ParameterMap $map
     */
    public function setParameterMap(int $x, ParameterMap $map) : void {
        $this->parameters[$x] = $map;
    }

    /**
     * @return array|ParameterMap[]
     */
    public function getParameters() : array {
        return $this->parameters;
    }

    /**
     * @param array|ParameterMap[] $parameters
     */
    public function setParameters(array $parameters) : void {
        $this->parameters = $parameters;
    }
}