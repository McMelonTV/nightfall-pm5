<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\provider;

use AndreasHGK\Core\Core;
use AndreasHGK\RankSystem\rank\Rank;
use AndreasHGK\RankSystem\RankSystem;
use AndreasHGK\RankSystem\utils\InvalidArgumentException;
use pocketmine\utils\Config;

class RankProvider {

    /** @var Config */
    private Config $ranks;

    private $plugin;

    public function __construct() {
        $this->plugin = Core::getInstance();
        $this->ranks = new Config($this->plugin->getDataFolder()."ranks.yml", Config::YAML);
    }

    public function exists(string $id) : bool {
        return $this->ranks->getNested("ranks.{$id}") !== null;
    }

    /**
     * Load every rank
     *
     * @return Rank[]
     */
    public function loadRanks() : array {
        $return = [];
        $ranks = $this->ranks->get("ranks", []);
        foreach($ranks as $id => $data) {
            $return[$id] = $this->loadRank($id);
        }
        return $return;
    }

    /**
     * Load a rank with the given ID
     *
     * @param string $id
     * @return Rank
     */
    public function loadRank(string $id) : Rank {
        $rank = $this->ranks->getNested("ranks.{$id}");
        if($rank === null) throw new InvalidArgumentException("there is no rank with the provided ID");

        $rank = Rank::fromData($rank);
        return $rank;
    }

}