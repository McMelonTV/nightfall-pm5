<?php

declare(strict_types=1);

namespace AndreasHGK\Core\forge;

class ForgeCategory {

    private $name;

    private $displayTag;

    /** @var array|ForgeItem[] */
    private $items = [];

    public function getName() : string {
        return $this->name;
    }

    public function getDisplayTag() : string {
        return $this->displayTag;
    }

    /**
     * @return array|ForgeItem[]
     */
    public function getItems() : array {
        return $this->items;
    }

    public function setItems(array $items) : void {
        $this->items = $items;
    }

    public function __construct(string $name, array $items, string $displayTag = null){
        $this->name = $name;
        $this->items = $items;
        $this->displayTag = $displayTag;
        if($displayTag === null){
            $this->displayTag = $name;
        }
    }
}