<?php

declare(strict_types=1);

namespace AndreasHGK\AutoComplete\parameter;

class SingleParameter extends ArrayParameter {

    /**
     * SingleParameter constructor.
     * @param string $name
     * @param string $typeName
     * @param string $text
     * @param bool $optional
     */
    public function __construct(string $name, string $typeName, string $text, bool $optional = false)
    {
        parent::__construct($name, $typeName, [$text], $optional, false);
    }

}