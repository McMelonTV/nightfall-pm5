<?php

declare(strict_types=1);

namespace AndreasHGK\Core\utils;

final class StringSet{

    private array $strings = [];

    public function add(string ...$strings) : void{
        foreach($strings as $string){
            $this->strings[$string] = true;
        }
    }

    public function remove(string ...$strings) : void{
        foreach($strings as $string){
            unset($this->strings[$string]);
        }
    }

    public function clear() : void{
        $this->strings = [];
    }

    public function contains(string $string) : bool{
        return array_key_exists($string, $this->strings);
    }

    /**
     * @return string[]
     */
    public function toArray() : array{
        return array_keys($this->strings);
    }

    public static function fromArray(array $array) : StringSet{
        $stringSet = new StringSet();
        foreach($array as $k => $value){
            $array[$k] = (string)$value;
        }

        $stringSet->add(...$array);

        return $stringSet;
    }
}