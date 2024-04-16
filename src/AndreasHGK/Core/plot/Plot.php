<?php

declare(strict_types=1);

namespace AndreasHGK\Core\plot;

use AndreasHGK\Core\task\PlotClearTask;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\utils\StringSet;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\Position;
use pocketmine\world\World;

final class Plot {

    public const PLOT_SIZE = 48;
    public const ROAD_SIZE = 7;

    private string $owner;

    private int $plotX, $plotZ;

    private StringSet $members;

    private StringSet $blockedUsers;

    private bool $isClearing = false;

    private string $name;

    public function __construct(int $plotX, int $plotZ, string $name = "", string $owner = "", array $members = [], array $blockedUsers = []){
        $this->name = $name;
        $this->owner = $owner;
        $this->plotX = $plotX;
        $this->plotZ = $plotZ;
        $this->members = StringSet::fromArray($members);
        $this->blockedUsers = StringSet::fromArray($blockedUsers);
    }

    public function isNamed() : bool {
        return $this->name !== "";
    }

    public function getName() : string {
        return $this->name;
    }

    public function setName(string $name) : void {
        $this->name = $name;
    }

    public function isBlocked(string $name) : bool{
        return $this->blockedUsers->contains(strtolower($name));
    }

    public function getBlockedUsers(): array{
        return $this->blockedUsers->toArray();
    }

    public function setBlockedUsers(array $blockedUsers): void{
        $this->blockedUsers = StringSet::fromArray($blockedUsers);
    }

    public function blockUser(string $name){
        $this->blockedUsers->add(strtolower($name));
    }

    public function unblockUser(string $name){
        $this->blockedUsers->remove(strtolower($name));
    }

    public function isClearing() : bool {
        return $this->isClearing;
    }

    public function setClearing(bool $clearing) : void {
        $this->isClearing = $clearing;
    }

    public function clear() : void {
        $pos1 = $this->getVectorA();
        $pos2 = $this->getVectorB();
        $chunks = [];

        $world = PlotManager::getInstance()->getWorld();

        $xMin = min($pos1->getX(), $pos2->getX());
        $xMax = max($pos1->getX(), $pos2->getX());
        $zMin = min($pos1->getZ(), $pos2->getZ());
        $zMax = max($pos1->getZ(), $pos2->getZ());
        for($x = $xMin; $x - 16 <= $xMax; $x += 16){
            for($z = $zMin; $z - 16 <= $zMax; $z += 16){
                $chunks[World::chunkHash($x >> 4, $z >> 4)] = FastChunkSerializer::serialize($world->loadChunk($x >> 4, $z >> 4), false);
            }
        }

        $this->isClearing = true;

        $task = new PlotClearTask($this->getId(), serialize($this->getVectorA()), serialize($this->getVectorB()), $chunks);
        Server::getInstance()->getAsyncPool()->submitTask($task);
    }

    public function hasAccess(User $user) : bool {
        if($user->getAdminMode()) {
            return true;
        }

        return $user->getName() === $this->owner || $this->isMember($user->getName());
    }

    public function isInPlot(Position $pos) : bool {
        $pos1 = $this->getVectorA();
        $pos2 = $this->getVectorB();
        return ($pos->getWorld() === null || $pos->getWorld()->getDisplayName() === PlotManager::$plotworld)
            && ($pos->getX() < max($pos1->getX(), $pos2->getX()) && $pos->getX() > min($pos1->getX(), $pos2->getX()))
            && ($pos->getY() < max($pos1->getY(), $pos2->getY()) && $pos->getY() > min($pos1->getY(), $pos2->getY()))
            && ($pos->getZ() < max($pos1->getZ(), $pos2->getZ()) && $pos->getZ() > min($pos1->getZ(), $pos2->getZ()));
    }

    public function isClaimed() : bool {
        return $this->owner !== "";
    }

    public function getVectorA() : Vector3 {
        $x = -4;
        $y = 256; //coords of plot 0 0
        $z = -4;
        return new Vector3($x + (self::PLOT_SIZE+self::ROAD_SIZE)*$this->plotX, $y, $z + (self::PLOT_SIZE+self::ROAD_SIZE)*$this->plotZ);
    }

    public function getVectorB() : Vector3 {
        $x = -51;
        $y = 1; //coords of plot 0 0
        $z = -51;
        return new Vector3($x + (self::PLOT_SIZE+self::ROAD_SIZE)*$this->plotX, $y, $z + (self::PLOT_SIZE+self::ROAD_SIZE)*$this->plotZ);
    }

    public function getId() : string {
        return $this->plotX.":".$this->plotZ;
    }

    public function getPlotX() : int {
        return $this->plotX;
    }

    public function getPlotZ() : int {
        return $this->plotZ;
    }

    public function getOwner() : string {
        return $this->owner;
    }

    public function setOwner(string $owner) : void {
        $this->owner = $owner;
    }

    public function getMembers() : array {
        return $this->members->toArray();
    }

    public function setMembers(array $members) : void {
        $this->members = StringSet::fromArray($members);
    }

    public function isMember(string $name) : bool{
        return $this->members->contains(strtolower($name));
    }

    public function addMember(string $name) : void{
        $this->members->add(strtolower($name));
    }

    public function removeMember(string $name) : void{
        $this->members->remove(strtolower($name));
    }
}