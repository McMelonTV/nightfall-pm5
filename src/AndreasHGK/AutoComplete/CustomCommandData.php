<?php

declare(strict_types=1);

namespace AndreasHGK\AutoComplete;

use AndreasHGK\AutoComplete\parameter\ArrayParameter;
use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\AutoComplete\parameter\MagicParameter;
use AndreasHGK\AutoComplete\parameter\SingleParameter;
use pocketmine\command\Command;

class CustomCommandData {

    /** @var Command */
    protected $command;

    /** @var array|ParameterMap[] */
    protected $parameters = [];

    /** @var bool */
    protected $debugCommand = false;

    /**
     * @return string
     */
    public function getName() : string {
        return $this->command->getLabel();
    }

    /**
     * @return Command
     */
    public function getCommand() : Command {
        return $this->command;
    }

    /**
     * @return bool
     */
    public function isDebugCommand() : bool {
        return $this->debugCommand;
    }

    /**
     * this will make the command blue in the player's command list
     *
     * @param bool $debug
     */
    public function setDebugCommand(bool $debug = true) : void {
        $this->debugCommand = $debug;
    }

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

    //More internal stuff

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

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

}