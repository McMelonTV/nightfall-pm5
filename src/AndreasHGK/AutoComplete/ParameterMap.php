<?php

declare(strict_types=1);

namespace AndreasHGK\AutoComplete;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;

class ParameterMap {

    /** @var array|CustomCommandParameter[] */
    protected $parameters = [];

    /**
     * @var string
     */
    protected $permission = "";

    /**
     * @return string
     */
    public function getPermission() : string {
        return $this->permission;
    }

    /**
     * @param string $permission
     */
    public function setPermission(string $permission) : void {
        $this->permission = $permission;
    }

    /**
     * @param $y
     * @return bool
     */
    public function parameterExists($y) : bool {
        return $this->parameters[$y] !== null;
    }

    /**
     * @param int $y
     * @return CustomCommandParameter|null
     */
    public function getParameter(int $y) : ?CustomCommandParameter {
        return $this->parameters[$y] ?? null;
    }

    public function setParameter(int $y, CustomCommandParameter $param) : void {
        $this->parameters[$y] = $param;
    }

    /**
     * @return array|CustomCommandParameter[]
     */
    public function getParameters() : array {
        return $this->parameters;
    }

    /**
     * @param array|CustomCommandParameter[] $parameters
     */
    public function setParameters(array $parameters) : void {
        $this->parameters = $parameters;
    }

    /**
     * ParameterMap constructor.
     * @param string $permission
     * @param array $parameters
     */
    public function __construct(string $permission = "", array $parameters = [])
    {
        $this->permission = $permission;
        $this->parameters = $parameters;
    }

    /**
     * @param int $y
     * @return CustomCommandParameter|null
     */
    public function __get($y)
    {
        return $this->getParameter($y);
    }

    /**
     * @param int $y
     * @param CustomCommandParameter $value
     */
    public function __set($y, CustomCommandParameter $value)
    {
        $this->setParameter($y, $value);
    }

    /**
     * @param int $y
     * @return bool
     */
    public function __isset($y)
    {
        return $this->parameterExists($y);
    }

}