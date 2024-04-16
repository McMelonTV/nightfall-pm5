<?php

declare(strict_types=1);

namespace AndreasHGK\AutoComplete\parameter;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

class CustomCommandParameter {

    public const ARG_TYPE_INT          = 1;
    public const ARG_TYPE_FLOAT        = 3;
    public const ARG_TYPE_VALUE        = 4;
    public const ARG_TYPE_WILDCARD_INT = 5;
    public const ARG_TYPE_OPERATOR     = 6;
    public const ARG_TYPE_TARGET       = 7;

    public const ARG_TYPE_FILEPATH = 16;

    public const ARG_TYPE_STRING   = 32;

    public const ARG_TYPE_POSITION = 40;

    public const ARG_TYPE_MESSAGE  = 44;

    public const ARG_TYPE_RAWTEXT  = 46;

    public const ARG_TYPE_JSON     = 50;

    public const ARG_TYPE_COMMAND  = 63;

    public const ARG_TYPE_ARRAY = 0x200000;

    //magic types
    public const MAGIC_TYPE_ITEM = "Item";
    public const MAGIC_TYPE_BLOCK = "Block";
    public const MAGIC_TYPE_ENCHANT = "Enchant";

    /** @var string */
    protected $name;

    /** @var int */
    protected $type = 0;

    /** @var bool */
    protected $optional = false;

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getType() : int {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type) : void {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isOptional() : bool {
        return $this->optional;
    }

    /**
     * @param bool $optional
     */
    public function setOptional(bool $optional) : void {
        $this->optional = $optional;
    }

    /**
     * @return CommandParameter
     */
    public function toPMParameter() : CommandParameter {
        $param = new CommandParameter();
        $param->paramName = $this->getName();
        $param->isOptional = $this->isOptional();
        $param->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | $this->getType();
        return $param;
    }

    /**
     * AbstractCommandParameter constructor.
     * @param string $name
     * @param int $type
     * @param bool $optional
     */
    public function __construct(string $name, $type = self::ARG_TYPE_INT, bool $optional = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->optional = $optional;
    }

}