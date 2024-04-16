<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class TeleportCommand extends Executor{
    public const MAX_COORD = 30000000;
    public const MIN_COORD = -30000000;

    public function __construct(){
        parent::__construct("teleport", "teleport to someone", "/tp [target player] <destination player> OR /tp [target player] <x> <y> <z> [<y-rot> <x-rot>]", "nightfall.command.teleport", ["tp"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addNormalParameter(0, 1, "destination", CustomCommandParameter::ARG_TYPE_TARGET, true, true);
        $this->addParameterMap(1);
        $this->addNormalParameter(1, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addNormalParameter(1, 1, "position", CustomCommandParameter::ARG_TYPE_POSITION, false, true);
        $this->addParameterMap(2);
        $this->addNormalParameter(2, 0, "position", CustomCommandParameter::ARG_TYPE_POSITION, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $args = array_values(array_filter($args, function($arg){
            return $arg !== "";
        }));

        if(count($args) < 1 or count($args) > 6){
            $sender->sendMessage("§r§c§l> §r§7You have given incorrect arguments for this command.");
            return true;
        }

        $target = null;
        $origin = $sender;
        if(count($args) === 1 or count($args) === 3){
            if($sender instanceof Player){
                $target = $sender;
            }else{
                $sender->sendMessage("§r§c§l> §r§7Please enter a player to teleport to.");
                return true;
            }

            if(count($args) === 1){
                $target = $sender->getServer()->getPlayerByPrefix($args[0]);
                if($target === null){
                    $sender->sendMessage("§r§c§l> §r§7Could not find player §c".$args[0]."§r§7.");

                    return true;
                }
            }
        }else{
            $target = $sender->getServer()->getPlayerByPrefix($args[0]);
            if($target === null){
                $sender->sendMessage("§r§c§l> §r§7Could not find player §c".$args[0]."§r§7.");

                return true;
            }

            if(count($args) === 2){
                $origin = $target;
                $target = $sender->getServer()->getPlayerByPrefix($args[1]);
                if($target === null){
                    $sender->sendMessage("§r§c§l> §r§7Could not find player §c".$args[1]."§r§7.");

                    return true;
                }
            }
        }

        $targetLocation = $target->getLocation();
        if(count($args) < 3){
            $origin->teleport($targetLocation);
            $sender->sendMessage("§r§b§l> §r§7Teleported §b".$origin->getName()."§r§7 to §b".$target->getName()."§r§7.");

            return true;
        }else{
            if(count($args) === 4 or count($args) === 6){
                $pos = 1;
            }else{
                $pos = 0;
            }

            $x = $this->getRelativeDouble($targetLocation->x, $args[$pos++]);
            $y = $this->getRelativeDouble($targetLocation->y, $args[$pos++], 0, 256);
            $z = $this->getRelativeDouble($targetLocation->z, $args[$pos++]);
            $yaw = $targetLocation->getYaw();
            $pitch = $targetLocation->getPitch();

            if(count($args) === 6 or (count($args) === 5 and $pos === 3)){
                $yaw = (float) $args[$pos++];
                $pitch = (float) $args[$pos];
            }

            $target->teleport(new Vector3($x, $y, $z), $yaw, $pitch);
            $sender->sendMessage("§r§b§l> §r§7Teleported §b".$target->getName()."§r§7 to §b".round($x, 2)."§7, §b".round($y, 2)."§7, §b".round($z, 2)."§r§7.");
            return true;
        }
    }

    /**
     * @param float         $original
     * @param string        $input
     * @param float         $min
     * @param float         $max
     *
     * @return float
     */
    protected function getRelativeDouble(float $original, string $input, float $min = self::MIN_COORD, float $max = self::MAX_COORD) : float{
        if($input[0] === "~"){
            $value = $this->getDouble(substr($input, 1));

            return $original + $value;
        }

        return $this->getDouble($input, $min, $max);
    }

    /**
     * @param mixed         $value
     * @param float         $min
     * @param float         $max
     *
     * @return float
     */
    protected function getDouble($value, float $min = self::MIN_COORD, float $max = self::MAX_COORD) : float{
        $i = (double) $value;

        if($i < $min){
            $i = $min;
        }elseif($i > $max){
            $i = $max;
        }

        return $i;
    }
}