<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\user\UserManager;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class CheckVoteTask extends AsyncTask {

    protected $serverKey;

    protected $players;

    public function __construct(string $serverKey, array $players){
        $this->serverKey = $serverKey;
        $this->players = serialize($players);
    }

    public function onRun(): void{
        $players = unserialize($this->players);
        $key = $this->serverKey;
        $voted = [];
        foreach($players as $player){
            $name = str_replace(" ", "+", $player);
            $vote = Internet::getURL("https://minecraftpocket-servers.com/api/?object=votes&element=claim&key=$key&username=".$name);
            if($vote !== null && (int) $vote->getBody() === 1){
                $voted[] = $player;
                Internet::getURL("https://minecraftpocket-servers.com/api/?action=post&object=votes&element=claim&key=$key&username=".$name);
            }
        }
        $this->setResult($voted);
    }

    public function onCompletion(): void{
        $players = $this->getResult();
        foreach($players as $playerName){
            $player = Server::getInstance()->getPlayerExact($playerName);
            if($player === null) {
                continue;
            }

            $user = UserManager::getInstance()->getOnline($player);
            if($user === null) {
                continue;
            }

            if($user->isOnline() && $player->isOnline()){
                Server::getInstance()->broadcastMessage("§r§8[§bNF§8] §r§b".$playerName." §r§7voted for the server with §b/vote §r§7and received special rewards.");
                $user->castVote();
                //Server::getInstance()->getAsyncPool()->submitTask(new VoteConfirmTask("fE6vi81D4FQVY7Qx4cnWnM0ZI0MrWGP95", $playerName));
            }
        }
    }
}