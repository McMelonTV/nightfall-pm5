<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\auctionhouse\AuctionManager;
use AndreasHGK\Core\Core;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\rank\MineRankManager;
use AndreasHGK\Core\rank\RankManager;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\FileUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\Server;

class SeasonresetCommand extends Executor{

    public function __construct(){
        parent::__construct("seasonreset", "a very dangerous command", "/seasonreset", "nightfall.command.seasonreset");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "message", CustomCommandParameter::ARG_TYPE_STRING, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if($sender instanceof Player) {
            $sender->sendMessage("§r§c§l> §r§7Please execute this command from console.");
            return true;
        }

        foreach(UserManager::getInstance()->getAll() as $user){
            $user->setBalance(0);
            $user->setMineRank(MineRankManager::getInstance()->get(0));
            $user->setMinedBlocks(0);
            $user->setTotalEarnedMoney(0);
            $user->setPrestige(1);
            $user->setPrestigePoints(0);
            $user->setAchievements([]);
            $user->setFly(false);
            $user->setExpiredAuctionItems([]);
            $user->setMaxAuc(1);
            $user->setMaxPlots(1);
            //$user->setRank(RankManager::getInstance()->get("member"));
            $user->getVault()->setMaxPages(1);
            $user->setGangId("");
            $user->setKills(0);
            $user->setDeaths(0);
            $user->setShopPurchases([]);
            $user->setReceivedStartItems(false);
            $user->getVault()->setPages([]);
            $user->setKitCooldowns([]);

            if(!$user->getRank()->isStaff() && $user->getDonatorRank() !== null){
                $user->setRank($user->getDonatorRank());
            }elseif(!$user->getRank()->isStaff()){
                $user->setRank(RankManager::getInstance()->get("member"));
            }

            UserManager::getInstance()->save($user);
            //VaultManager::getInstance()->save($user->getVault());
        }

        foreach(GangManager::getInstance()->getAll() as $gang){
            GangManager::getInstance()->delete($gang);
        }

        AuctionManager::getInstance()->setAll([]);
        PlotManager::getInstance()->setAll([]);
        $wm = Server::getInstance()->getWorldManager();
        $plots = $wm->getWorldByName(PlotManager::$plotworld);
        $wm->unloadWorld($plots);
        FileUtils::deleteFolder(Server::getInstance()->getDataPath()."worlds/".$plots->getFolderName());
        //FileUtils::deleteFolder(Server::getInstance()->getDataPath()."plugin_data/nightfall/vaults");

        $players = array_diff(scandir(Server::getInstance()->getDataPath()."players"), [".", ".."]);
        foreach($players as $filename){
            $username = explode(".", $filename)[0];
            $player = Server::getInstance()->getOfflinePlayerData($username);
            if($player === null) continue;
            $player->setString("Level", "spawn");
            $player->setTag("Inventory", new ListTag([], NBT::TAG_List));
            $player->setTag("EnderChestInventory", new ListTag([], NBT::TAG_List));
            $player->setInt("XpTotal", 0);
            $player->setInt("XpLevel", 0);
            $player->setFloat("Health", 20);
            Server::getInstance()->saveOfflinePlayerData($username, $player);
        }

        Core::getInstance()->clearItemEntities();
        Core::save();
        Server::getInstance()->shutdown();
        return true;
    }

}