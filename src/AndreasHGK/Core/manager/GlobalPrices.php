<?php

namespace AndreasHGK\Core\manager;

use AndreasHGK\Core\tag\Tag;

class GlobalPrices {

    private static $instance;

    /**
     * @var array|int[]
     */
    private $prices = [];

    /**
     * @return array|Tag[]
     */
    public function getAll() : array {
        return $this->prices;
    }

    public function get(string $id) : ?int {
        return $this->prices[$id] ?? null;
    }

    public function loadAll() : void {
        $prices = DataManager::get(DataManager::PRICES,  false);
        foreach($prices->getAll() as $id => $price){
            $this->prices[(string)$id] = $price;
        }
    }

    public function exist(string $tag) : bool {
        return isset($this->prices[$tag]);
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}