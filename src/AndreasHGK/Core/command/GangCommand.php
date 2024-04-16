<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\command\gang\AcceptSubcommand;
use AndreasHGK\Core\command\gang\AllySubcommand;
use AndreasHGK\Core\command\gang\CreateSubcommand;
use AndreasHGK\Core\command\gang\DemoteSubcommand;
use AndreasHGK\Core\command\gang\DisbandSubcommand;
use AndreasHGK\Core\command\gang\EnemySubcommand;
use AndreasHGK\Core\command\gang\ForcekickSubcommand;
use AndreasHGK\Core\command\gang\InfoSubcommand;
use AndreasHGK\Core\command\gang\InviteSubcommand;
use AndreasHGK\Core\command\gang\KickSubcommand;
use AndreasHGK\Core\command\gang\LeaveSubcommand;
use AndreasHGK\Core\command\gang\ListSubcommand;
use AndreasHGK\Core\command\gang\PromoteSubcommand;
use AndreasHGK\Core\command\gang\SetdescriptionSubcommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class GangCommand extends MultiExecutor{

    public function __construct(){
        parent::__construct("gang", "the main gang command", "/gang [subcommand]", "nightfall.command.gang", ["g", "clan"]);
        $this->setSubcommands([
        	new AcceptSubcommand(),
            new AllySubcommand(),
            new CreateSubcommand(),
            new DemoteSubcommand(),
            new DisbandSubcommand(),
            new EnemySubcommand(),
            new InfoSubcommand(),
            new InviteSubcommand(),
            new KickSubcommand(),
            new LeaveSubcommand(),
            new ListSubcommand(),
            new PromoteSubcommand(),
            new SetdescriptionSubcommand(),
            new ForcekickSubcommand()
        ]);
        $this->enableHelp(true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $return = parent::onCommand($sender, $command, $label, $args);
        if($return) return true;
        $sender->sendMessage("§r§c§l> §r§7That subcommand was not found.");
        return true;
    }

}