<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem;

use AndreasHGK\RankSystem\provider\RankProvider;
use AndreasHGK\RankSystem\rank\RankManager;

class RankSystem{

    /** @var RankSystem */
    private static $instance;

	private function __construct(){
		self::$instance = $this;
		$this->rankProvider = new RankProvider();
		$this->rankManager = new RankManager();
		$this->rankManager->load();
	}

    /**
     * @return RankSystem
     */
    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /** @var RankManager */
    private RankManager $rankManager;
    /** @var RankProvider */
    private RankProvider $rankProvider;

    /**
     * @return RankManager
     */
    public function getRankManager() : RankManager {
        return $this->rankManager;
    }

    /**
     * @return RankProvider
     */
    public function getRankProvider() : RankProvider {
        return $this->rankProvider;
    }
}
