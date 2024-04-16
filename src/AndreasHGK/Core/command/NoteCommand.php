<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\player\Player;

class NoteCommand extends Executor{

    public function __construct(){
        parent::__construct("note", "note", "/note <instrument> <note>", "nightfall.command.note");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof  Player) return false;

        try{
            $sound = LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_NOTE, $sender->getPosition(), ((int)$args[0] << 8) | (int)$args[1]);

            $sender->getNetworkSession()->sendDataPacket($sound);

            $sender->sendMessage("§r§c> §r§7Executed successfully.");
        }catch (\Throwable $e){
            $sender->sendMessage("§r§c> §r§7An error has occured.");
        }
        return true;
    }
}