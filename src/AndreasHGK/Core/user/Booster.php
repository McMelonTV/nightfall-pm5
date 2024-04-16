<?php

declare(strict_types=1);

namespace AndreasHGK\Core\user;

class Booster {

    private $applyDate;

    private $duration;

    private $name = "booster";

    private $id;

    public function getName() : string {
        return $this->name;
    }

    public function setName(string $name) : void {
        $this->name = $name;
    }

    public function getId() : int {
        return $this->id;
    }

    public function __construct(int $id, string $name, int $duration, int $applyDate = null){
        $this->id = $id;
        $this->name = $name;
        $this->duration = $duration;
        $this->applyDate = $applyDate;
    }
}