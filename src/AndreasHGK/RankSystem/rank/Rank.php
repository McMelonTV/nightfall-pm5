<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\rank;

use AndreasHGK\RankSystem\RankSystem;
use AndreasHGK\RankSystem\utils\InvalidArgumentException;

class Rank {

    public static function fromData(array $data) : Rank {
        if(!isset($data["id"])) throw new InvalidArgumentException("rank data must contain rank ID");
        return new Rank(
            $data["id"],
            $data["name"] ?? $data["id"],
            $data["permissions"] ?? [],
            $data["prefix"] ?? null,
            $data["isStaff"] ?? false,
            $data["isDonator"] ?? false,
            $data["inherit"] ?? [],
            $data["isDefault"] ?? false,
            $data["priority"] ?? 1,
            $data["vaults"] ?? 0,
            $data["plots"] ?? 0,
        );
    }

    private string $id;

    private string $name;
    /** @var string[] */
    private array $permissions = [];

    private string $prefix;
    /** @var string[] */
    private array $inherit = [];

    private bool $isStaff = false;

    private bool $isDonator = false;

    private bool $isDefault = false;

    private int $priority = 1;

    private int $vaults = 0;

    private int $plots = 0;

    public function __construct(
        string $id,
        string $name,
        array $permissions = [],
        string $prefix = null,
        bool $isStaff = false,
        bool $isDonator = false,
        array $inherit = [],
        bool $isDefault = false,
        int $priority = 1,
        int $vaults = 0,
        int $plots = 0
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->permissions = $permissions;
        $this->prefix = $prefix ?? $name;
        $this->inherit = $inherit;
        $this->isStaff = $isStaff;
        $this->isDonator = $isDonator;
        $this->isDefault = $isDefault;
        $this->priority = $priority;
        $this->vaults = $vaults;
        $this->plots = $plots;
    }

    /**
     * Get the identifier of the rank
     *
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * Get the name of the rank
     *
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Get the list of permissions that this rank has
     *
     * @return string[]
     */
    public function getPermissions() : array {
        return $this->permissions;
    }

    /**
     * Get the list of permissions that members with this rank will have access to
     * This includes the permissions of inherited ranks
     *
     * @return string[]
     */
    public function getAllPermissions() : array {
        $perm = $this->getPermissions();
        foreach($this->getInherit() as $inherit) {
            $perm = array_merge($perm, $inherit->getAllPermissions());
        }
        return $perm;
    }

    /**
     * Get the prefix that will appear before player's names
     *
     * @return string
     */
    public function getPrefix() : string {
        return $this->prefix;
    }

    /**
     * Check if the rank is a staff rank
     *
     * @return bool
     */
    public function isStaff() : bool {
        return $this->isStaff;
    }

    /**
     * Check if the rank is a donator rank
     *
     * @return bool
     */
    public function isDonator() : bool {
        return $this->isDonator;
    }

    /**
     * Check if the rank is a default rank
     *
     * @return bool
     */
    public function isDefault() : bool {
        return $this->isDefault;
    }

    /**
     * Get all the ranks of which this rank inherits permissions
     *
     * @return Rank[]
     */
    public function getInherit() : array {
        static $names = [];
        static $return = [];
        if($names !== $this->getInheritIds()) {
            $names = $this->getInheritIds();
            $return = [];
            $rm = RankSystem::getInstance()->getRankManager();
            foreach($this->getInheritIds() as $inheritId) {
                $return[] = $rm->get($inheritId);
            }
        }
        return $return;
    }

    /**
     * Get all the ranks of which this rank inherits permissions
     *
     * @return string[]
     */
    public function getInheritIds() : array {
        return $this->inherit;
    }

    /**
     * Get the priority of the rank prefix
     *
     * @return int
     */
    public function getPriority() : int {
        return $this->priority;
    }

    /**
     * @return int
     */
    public function getPlots() : int {
        return $this->plots;
    }

    /**
     * @return int
     */
    public function getVaults() : int {
        return $this->vaults;
    }

}