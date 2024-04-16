<?php

declare(strict_types=1);

namespace AndreasHGK\AutoComplete;

use AndreasHGK\AutoComplete\parameter\ArrayParameter;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandData;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

/**
 * Class AutoComplete
 * @package AndreasHGK\AutoComplete
 */
class AutoComplete {

    public static $enumIndex = 0;

    /**
     * @var self
     */
    protected static $instance;

    /**
     * @return self
     */
    public static function getInstance() : self {
        if(!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    /**
     * @return CommandMap
     */
    public static function getCommandMap() : CommandMap {
        return CommandMap::getInstance();
    }

    /** @var PluginBase */
    public $owner;

    /**
     * @return PluginBase
     */
    public function getOwner() : PluginBase {
        return $this->owner;
    }

    /**
     * @param PluginBase $plugin
     */
    public function registerOwner(PluginBase $plugin) : void {
        $this->owner = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents(new PacketListener(), $plugin);
    }


    public function updatePacketFor(Player $player, AvailableCommandsPacket &$packet) : AvailableCommandsPacket {
        foreach(self::getCommandMap()->getAll() as $id => $customCommandData){
            if(!isset($packet->commandData[$customCommandData->getCommand()->getName()])) continue;
            if(!$customCommandData->getCommand()->testPermissionSilent($player)) continue;

            $command = $customCommandData->getCommand();
            $name = strtolower($command->getName());
            $aliases = $command->getAliases();
            $aliasObj = null;

            if(!empty($aliases)){
                if(!in_array($name, $aliases, true)){
                    //work around a client bug which makes the original name not show when aliases are used
                    $aliases[] = $name;
                }
                $aliasObj = new CommandEnum(ucfirst($command->getName()) . "Aliases", $aliases);
            }
            //$commandData = new CommandData($name, $command->getDescription(), ($customCommandData->isDebugCommand() ? 1 : 0), (int)$command->testPermissionSilent($player), $aliasObj, []);
            $commandData = new CommandData($name, $command->getDescription(), ($customCommandData->isDebugCommand() ? 1 : 0), 0, $aliasObj, []);

            foreach($customCommandData->getParameters() as $x => $paramMap){
                if(!$player->hasPermission($paramMap->getPermission()) && $paramMap->getPermission() !== "") continue;

                foreach($paramMap->getParameters() as $y => $customParameter){

                    $vanillaParam = $customParameter->toPMParameter();

                    if(isset($vanillaParam->enum)){
                        if($customParameter instanceof ArrayParameter && $customParameter->isSoftEnum()){
                            array_push($packet->softEnums, $vanillaParam->enum);
                        }else{
                            array_push($packet->hardcodedEnums, $vanillaParam->enum);
                        }
                    }

                    $commandData->overloads[$x][$y] = $vanillaParam;
                }
            }

            $packet->commandData[$command->getName()] = $commandData;
        }
        return $packet;
    }

}