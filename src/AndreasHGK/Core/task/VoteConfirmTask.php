<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;

class VoteConfirmTask extends AsyncTask {

    protected $serverKey;

    protected $player;

    public function __construct(string $serverKey, string $player){
        $this->serverKey = $serverKey;
        $this->player = $player;
    }

    public function onRun(): void{
        $player = $this->player;
        $key = $this->serverKey;
        Internet::getURL("https://minecraftpocket-servers.com/api/?action=post&object=votes&element=claim&key=$key&username=".str_replace(" ", "+", $player));
    }
}