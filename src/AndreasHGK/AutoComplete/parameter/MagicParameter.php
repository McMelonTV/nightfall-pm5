<?php

declare(strict_types=1);

namespace AndreasHGK\AutoComplete\parameter;

use AndreasHGK\AutoComplete\AutoComplete;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

/**
 * Class MagicParameter
 * MCBE can do some magic with these parameters and a hardcoded typeName
 * @package AndreasHGK\AutoComplete
 */
class MagicParameter extends CustomCommandParameter {

    /**
     * @var string
     */
    protected $typeName;

    /**
     * @return string
     */
    public function getTypeName() : string {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     */
    public function setTypeName(string $typeName) : void {
        $this->typeName = $typeName;
    }

    /**
     * @return CommandParameter
     */
    public function toPMParameter(): CommandParameter
    {
        $param = parent::toPMParameter();
        $param->paramType = AvailableCommandsPacket::ARG_FLAG_ENUM | AvailableCommandsPacket::ARG_FLAG_VALID | AutoComplete::$enumIndex;
        ++AutoComplete::$enumIndex;
        $param->enum = new CommandEnum($this->getTypeName(), []);
        return $param;
    }

    /**
     * MagicParameter constructor.
     * @param string $name
     * @param string $typeName
     * @param bool $optional
     */
    public function __construct(string $name, string $typeName, bool $optional = false)
    {
        parent::__construct($name, CustomCommandParameter::ARG_TYPE_ARRAY, $optional);
        $this->typeName = $typeName;
    }

}