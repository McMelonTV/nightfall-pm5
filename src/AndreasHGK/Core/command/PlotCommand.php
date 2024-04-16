<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\command\plot\AddmemberSubcommand;
use AndreasHGK\Core\command\plot\AutoclaimCommand;
use AndreasHGK\Core\command\plot\BlockplayerSubcommand;
use AndreasHGK\Core\command\plot\ClaimSubcommand;
use AndreasHGK\Core\command\plot\ClearSubcommand;
use AndreasHGK\Core\command\plot\InfoSubcommand;
use AndreasHGK\Core\command\plot\ListSubcommand;
use AndreasHGK\Core\command\plot\RemovememberSubcommand;
use AndreasHGK\Core\command\plot\RenameSubcommand;
use AndreasHGK\Core\command\plot\TeleportSubcommand;
use AndreasHGK\Core\command\plot\TransferownershipSubcommand;
use AndreasHGK\Core\command\plot\UnblockplayerSubcommand;
use AndreasHGK\Core\command\plot\UnclaimSubcommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class PlotCommand extends MultiExecutor{

    public function __construct(){
        parent::__construct("plot", "the main plot command", "/plot [subcommand]", "nightfall.command.plot", ["p"]);
        $this->setSubcommands([
            new AutoclaimCommand(),
            new BlockplayerSubcommand(),
            new ClaimSubcommand(),
            new UnclaimSubcommand(),
            new ClearSubcommand(),
            new AddmemberSubcommand(),
            new RemovememberSubcommand(),
            new TeleportSubcommand(),
            new InfoSubcommand(),
            new ListSubcommand(),
            new RenameSubcommand(),
            new TransferownershipSubcommand(),
            new UnblockplayerSubcommand()
            ]);
        $this->enableHelp(true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $return = parent::onCommand($sender, $command, $label, $args);
        if($return) {
            return true;
        }

        $sender->sendMessage("§r§c§l> §r§7That subcommand was not found.");
        return true;
    }

}