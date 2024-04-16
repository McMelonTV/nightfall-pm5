<?php

declare(strict_types=1);

namespace AndreasHGK\Core\koth;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\utils\FileUtils;
use pocketmine\math\Vector3;
use pocketmine\Server;
use function mt_rand;

final class KothManager{

    private static $instance;

    /** @var Koth[] */
    private array $koths = [];

    private int $nextKoth;
    private ?Koth $running = null;
    private int $spawnNext = 0;

    /**
     * @return array|Koth[]
     */
    public function getAll() : array{
        return $this->koths;
    }

    public function get(int $id) : ?Koth{
        return $this->koths[$id] ?? null;
    }

    public function startRandom() : void{
        if($this->running !== null){
            return;
        }

        //Server::getInstance()->broadcastMessage($koth->getName() . " KOTH has started in Mine PvP");
        if($this->spawnNext === 0) {
            $koth = $this->koths[mt_rand(0, 1) === 0 ? "Cave" : "Forest"];
            Server::getInstance()->broadcastMessage("§8§l<-----§aKOTH§8----->§r" .
                "\n §a§l> §r§a" . $koth->getName() . " §7KOTH has started in Mine PvP!§r" .
                "\n §a§l> §r§7Follow the §a" . $koth->getColor() . " §7path to get there!§r" .
                "\n §a§8§l<-----++----->");
        }else{
            $koth = $this->koths["Spawn"];
            Server::getInstance()->broadcastMessage("§8§l<-----§aKOTH§8----->§r" .
                "\n §a§l> §r§a" . $koth->getName() . " §7KOTH has started in Spawn PvP!§r" .
                "\n §a§l> §r§7Go to the tree in the middle of spawn pvp to get there!§r" .
                "\n §a§8§l<-----++----->");
        }

        $this->running = $koth;
    }

    public function stop() : void{
        if(++$this->spawnNext > 3){
            $this->spawnNext = 0;
        }

        $this->running = null;
        $this->nextKoth = $this->nextKoth + 60*60*6;
    }

    public function getRunning() : ?Koth{
        return $this->running;
    }

    public function getNextKoth() : int{
        return $this->nextKoth;
    }

    public function spawnNext() : bool{
        return $this->spawnNext > 0;
    }

    public function register(Koth $koth) : void{
        $this->koths[$koth->getName()] = $koth;
    }

    public function setup() : void{
        $this->nextKoth = (int) DataManager::getKey(FileUtils::MakeYAML("koth"), "next-koth");
        $this->spawnNext = (int) DataManager::getKey(FileUtils::MakeYAML("koth"), "spawn-next");

        $worldManager = Server::getInstance()->getWorldManager();
        $minePvP = $worldManager->getWorldByName(Core::PVPMINEWORLD);
        $spawn = $worldManager->getWorldByName("spawn");
        $koths = [
            new Koth("Cave", "red", 60*15*2, 0, new Vector3(-62, 24, -27), new Vector3(-67, 32, -32), $minePvP),
            new Koth("Forest", "blue", 60*15*2, 0, new Vector3(-73, 62, 77), new Vector3(-66, 69, 84), $minePvP),
            new Koth("Spawn", "", 60*10*2, 1, new Vector3(1575, 11, 696), new Vector3(1580, 17, 701), $spawn)
        ];

        foreach($koths as $koth) {
            $this->register($koth);
        }

        Core::getInstance()->getScheduler()->scheduleRepeatingTask(new KothTask(), 10);
    }

    public function save() : void{
        $file = DataManager::get(FileUtils::MakeYAML("koth"), false);
        $file->set("next-koth", $this->nextKoth);
        $file->set("spawn-next", $this->spawnNext);
        $file->save();
    }

    public static function getInstance() : self{
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}