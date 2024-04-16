<?php

declare(strict_types=1);

namespace AndreasHGK\AutoComplete\parameter;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

class ArrayParameter extends MagicParameter {

    /**
     * @var array
     */
    protected $contents = [];

    /**
     * @var bool
     */
    protected $softEnum = false;

    /**
     * @return bool
     */
    public function isSoftEnum() : bool {
        return $this->softEnum;
    }

    /**
     * @param bool $softEnum
     */
    public function setSoftEnum(bool $softEnum) : void {
        $this->softEnum = $softEnum;
    }

    /**
     * @return array
     */
    public function getContents() : array {
        return $this->contents;
    }

    /**
     * @param array $contents
     */
    public function setContents(array $contents) : void {
        $this->contents = $contents;
    }

    /**
     * @return CommandParameter
     */
    public function toPMParameter(): CommandParameter
    {
        $param = parent::toPMParameter();
        $param->enum = new CommandEnum($this->getTypeName(), $this->getContents());
        return $param;
    }

    /**
     * ArrayParameter constructor.
     * @param string $name
     * @param string $typeName
     * @param array $contents
     * @param bool $optional
     * @param bool $softEnum
     */
    public function __construct(string $name, string $typeName, array $contents, bool $optional = false, bool $softEnum = false)
    {
        parent::__construct($name, $typeName, $optional);
        $this->contents = $contents;
        $this->softEnum = $softEnum;
    }

}