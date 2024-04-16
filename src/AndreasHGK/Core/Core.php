<?php

declare(strict_types=1);

namespace AndreasHGK\Core;

use AndreasHGK\AutoComplete\AutoComplete;
use AndreasHGK\Core\achievement\AchievementManager;
use AndreasHGK\Core\auctionhouse\AuctionManager;
use AndreasHGK\Core\command\AchievementsCommand;
use AndreasHGK\Core\command\AdminCommand;
use AndreasHGK\Core\command\AFKCommand;
use AndreasHGK\Core\command\AliasCommand;
use AndreasHGK\Core\command\AuctionCommand;
use AndreasHGK\Core\command\BalanceCommand;
use AndreasHGK\Core\command\BalTopCommand;
use AndreasHGK\Core\command\BanCommand;
use AndreasHGK\Core\command\BanListCommand;
use AndreasHGK\Core\command\BlockCommand;
use AndreasHGK\Core\command\BlocklistCommand;
use AndreasHGK\Core\command\BroadcastCommand;
use AndreasHGK\Core\command\CheckIPCommand;
use AndreasHGK\Core\command\ClearinventoryCommand;
use AndreasHGK\Core\command\ClearlagCommand;
use AndreasHGK\Core\command\ClearMineCommand;
use AndreasHGK\Core\command\ClearwarnCommand;
use AndreasHGK\Core\command\CommandBase;
use AndreasHGK\Core\command\CommandSpyCommand;
use AndreasHGK\Core\command\ConvertWorldCommand;
use AndreasHGK\Core\command\CrateitemsCommand;
use AndreasHGK\Core\command\CratesCommand;
use AndreasHGK\Core\command\CustomEnchantCommand;
use AndreasHGK\Core\command\DisableMineCommand;
use AndreasHGK\Core\command\DisenchantCommand;
use AndreasHGK\Core\command\EnableMineCommand;
use AndreasHGK\Core\command\EnchantCommand;
use AndreasHGK\Core\command\EnchantmentforgeCommand;
use AndreasHGK\Core\command\EnchantmentlistCommand;
use AndreasHGK\Core\command\EvalCommand;
use AndreasHGK\Core\command\Executor;
use AndreasHGK\Core\command\FeedCommand;
use AndreasHGK\Core\command\FixCommand;
use AndreasHGK\Core\command\FlyCommand;
use AndreasHGK\Core\command\ForgeCommand;
use AndreasHGK\Core\command\GangCommand;
use AndreasHGK\Core\command\GiveTagCommand;
use AndreasHGK\Core\command\GlobalmuteCommand;
use AndreasHGK\Core\command\GuideCommand;
use AndreasHGK\Core\command\HelpCommand;
use AndreasHGK\Core\command\IdCommand;
use AndreasHGK\Core\command\IgnoreAllCommand;
use AndreasHGK\Core\command\InventoryseeCommand;
use AndreasHGK\Core\command\KeyAllCommand;
use AndreasHGK\Core\command\KeyCommand;
use AndreasHGK\Core\command\KitCommand;
use AndreasHGK\Core\command\KothCommand;
use AndreasHGK\Core\command\LeaderboardCommand;
use AndreasHGK\Core\command\ListCommand;
use AndreasHGK\Core\command\LotteryCommand;
use AndreasHGK\Core\command\MeCommand;
use AndreasHGK\Core\command\MineCommand;
use AndreasHGK\Core\command\MinesCommand;
use AndreasHGK\Core\command\MuteCommand;
use AndreasHGK\Core\command\MyCoordsCommand;
use AndreasHGK\Core\command\MywarnsCommand;
use AndreasHGK\Core\command\NearCommand;
use AndreasHGK\Core\command\NewsCommand;
use AndreasHGK\Core\command\NFItemCommand;
use AndreasHGK\Core\command\NickCommand;
use AndreasHGK\Core\command\NicklistCommand;
use AndreasHGK\Core\command\NoteCommand;
use AndreasHGK\Core\command\PayCommand;
use AndreasHGK\Core\command\PerformanceCommand;
use AndreasHGK\Core\command\PingCommand;
use AndreasHGK\Core\command\PlotCommand;
use AndreasHGK\Core\command\PlotsCommand;
use AndreasHGK\Core\command\PluginsCommand;
use AndreasHGK\Core\command\PrestigeCommand;
use AndreasHGK\Core\command\RanksCommand;
use AndreasHGK\Core\command\RankUpCommand;
use AndreasHGK\Core\command\RegenerateAllCommand;
use AndreasHGK\Core\command\RegenerateCommand;
use AndreasHGK\Core\command\RenameCommand;
use AndreasHGK\Core\command\RenameWorldCommand;
use AndreasHGK\Core\command\ReplyCommand;
use AndreasHGK\Core\command\RulesCommand;
use AndreasHGK\Core\command\SayCommand;
use AndreasHGK\Core\command\SeasonresetCommand;
use AndreasHGK\Core\command\SetBalanceCommand;
use AndreasHGK\Core\command\SetBlockCommand;
use AndreasHGK\Core\command\SetMaxAucCommand;
use AndreasHGK\Core\command\SetMaxPlotsCommand;
use AndreasHGK\Core\command\SetMaxVaultCommand;
use AndreasHGK\Core\command\SetMineCommand;
use AndreasHGK\Core\command\SetPrestigeCommand;
use AndreasHGK\Core\command\SetSpawnCommand;
use AndreasHGK\Core\command\ShopCommand;
use AndreasHGK\Core\command\SizeCommand;
use AndreasHGK\Core\command\SoftrestartCommand;
use AndreasHGK\Core\command\SpawnCommand;
use AndreasHGK\Core\command\SpectatorCommand;
use AndreasHGK\Core\command\StatsCommand;
use AndreasHGK\Core\command\SudoCommand;
use AndreasHGK\Core\command\SuperlistCommand;
use AndreasHGK\Core\command\SurvivalCommand;
use AndreasHGK\Core\command\TagsCommand;
use AndreasHGK\Core\command\TeleportCommand;
use AndreasHGK\Core\command\TellCommand;
use AndreasHGK\Core\command\TempbanCommand;
use AndreasHGK\Core\command\TrashCommand;
use AndreasHGK\Core\command\UnbanCommand;
use AndreasHGK\Core\command\UnblockCommand;
use AndreasHGK\Core\command\VanishCommand;
use AndreasHGK\Core\command\VaultCommand;
use AndreasHGK\Core\command\VoteCommand;
use AndreasHGK\Core\command\WarnCommand;
use AndreasHGK\Core\command\WarningsCommand;
use AndreasHGK\Core\command\WorldCommand;
use AndreasHGK\Core\crate\CrateListener;
use AndreasHGK\Core\crate\CrateManager;
use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\forge\ForgeCategoryManager;
use AndreasHGK\Core\gang\GangListener;
use AndreasHGK\Core\gang\GangManager;
use AndreasHGK\Core\generator\PlotGenerator;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\kit\KitManager;
use AndreasHGK\Core\koth\KothManager;
use AndreasHGK\Core\leaderboard\Leaderboards;
use AndreasHGK\Core\listener\AchievementListener;
use AndreasHGK\Core\listener\AFKListener;
use AndreasHGK\Core\listener\AntispamListener;
use AndreasHGK\Core\listener\BanListener;
use AndreasHGK\Core\listener\BlockBreakListener;
use AndreasHGK\Core\listener\BlockListener;
use AndreasHGK\Core\listener\BorderListener;
use AndreasHGK\Core\listener\BroadcastListener;
use AndreasHGK\Core\listener\ChatListener;
use AndreasHGK\Core\listener\ChatsoundListener;
use AndreasHGK\Core\listener\CombatLogListener;
use AndreasHGK\Core\listener\CommandSpyListener;
use AndreasHGK\Core\listener\DelayedCommandListener;
use AndreasHGK\Core\listener\EnchantmentBookListener;
use AndreasHGK\Core\listener\GappleListener;
use AndreasHGK\Core\listener\GlobalmuteListener;
use AndreasHGK\Core\listener\HungerListener;
use AndreasHGK\Core\listener\JoinListener;
use AndreasHGK\Core\listener\MessagesListener;
use AndreasHGK\Core\listener\NewsListener;
use AndreasHGK\Core\listener\NoCapsCommandListener;
use AndreasHGK\Core\listener\NoDropListener;
use AndreasHGK\Core\listener\NoStackedItemsListener;
use AndreasHGK\Core\listener\PlayerAttackListener;
use AndreasHGK\Core\listener\ProtectionListener;
use AndreasHGK\Core\listener\PVPListener;
use AndreasHGK\Core\listener\QueryListener;
use AndreasHGK\Core\listener\StaffChatListener;
use AndreasHGK\Core\listener\VanishListener;
use AndreasHGK\Core\listener\WorldChangeListener;
use AndreasHGK\Core\lottery\Lottery;
use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\plot\PlotListener;
use AndreasHGK\Core\plot\PlotManager;
use AndreasHGK\Core\shop\ShopCategoryManager;
use AndreasHGK\Core\task\AFKTimerTask;
use AndreasHGK\Core\task\AntispamTask;
use AndreasHGK\Core\task\AuctionExpireTask;
use AndreasHGK\Core\task\AutoMineRegenerateTask;
use AndreasHGK\Core\task\AutoRestartTask;
use AndreasHGK\Core\task\AutoSaveTask;
use AndreasHGK\Core\task\AutoVoteCheckTask;
use AndreasHGK\Core\task\BroadcastTask;
use AndreasHGK\Core\task\ClearEntitiesTask;
use AndreasHGK\Core\task\CombatLogTask;
use AndreasHGK\Core\task\CustomEnchantTickTask;
use AndreasHGK\Core\task\DelayedPermissionTask;
use AndreasHGK\Core\task\MineDisplayTask;
use AndreasHGK\Core\task\MoneyDisplayTask;
use AndreasHGK\Core\task\TimedMineRegenerateTask;
use AndreasHGK\Core\task\UnMuteTask;
use AndreasHGK\Core\user\BannedUserManager;
use AndreasHGK\Core\user\UserListener;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\vault\VaultManager;
use AndreasHGK\Core\vote\VoteParty;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier as BID;
use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\block\Redstone;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Internet;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\WorldCreationOptions;

final class Core extends PluginBase {

    public const GLOW_ID = 240;

    public const PERM_MAIN = "nightfall.";

    public const PVPMINEWORLD = "PvPMine";

    private static $instance;

    public static bool $devserver = false;

    public static $restart = 24*3601;

    private bool $globalMute = false;

    public static function getInstance() : self {
        return self::$instance;
    }

    public static function isDevServer() : bool {
        return self::$devserver;
    }

    public function getGlobalMute() : bool {
        return $this->globalMute;
    }

    public function setGlobalMute(bool $mute) : void {
        $this->globalMute = $mute;
    }

    public function restart() : void {
        $ip = Internet::getIP();
        $port = $this->getServer()->getPort();
        foreach($this->getServer()->getOnlinePlayers() as $player){
            $player->transfer((string)$ip, (int)$port, "The server is restarting...");
        }

        if(function_exists("pcntl_exec")){
            register_shutdown_function(static function () {
                pcntl_exec("./start.sh");
            });
        }

        $this->getServer()->shutdown();
    }

    public function clearItemEntities() : int{
        $entitiesCleared = 0;
        foreach($this->getServer()->getWorldManager()->getWorlds() as $world){
            foreach($world->getEntities() as $entity){
                if($entity instanceof ItemEntity){
                    $entity->flagForDespawn();
                    ++$entitiesCleared;
                }
            }
        }

        return $entitiesCleared;
    }

    public function onLoad(): void {
        DataManager::$dataFolder = $this->getDataFolder();
        EnchantmentIdMap::getInstance()->register(self::GLOW_ID, new Enchantment(self::GLOW_ID, "", Rarity::COMMON, ItemFlags::ALL, ItemFlags::NONE, 1));
        self::$instance = $this;
        self::$devserver = DataManager::getKey(DataManager::CONFIG, "devserver");
        DataManager::loadDefault();
        AchievementManager::getInstance()->registerDefaults();
        CustomEnchantsManager::getInstance()->registerDefaults();
        ShopCategoryManager::getInstance()->registerDefaults();
        CustomItemManager::getInstance()->registerDefaults();
        ForgeCategoryManager::getInstance()->registerDefaults();

        VoteParty::getInstance()->load();

        KitManager::getInstance()->registerDefaults();
        CrateManager::getInstance()->registerDefaults();

        if(self::isDevServer()){
            Server::getInstance()->getNetwork()->setName("§8[§bNightfall§8] §7Development server");
        }else{
            //Server::getInstance()->getNetwork()->setName("§8[§bNightfall§8] §7Being Developed...");
            Server::getInstance()->getNetwork()->setName("§8[§bNightfall§8] §7".ServerInfo::$season);
        }
    }

    public function onEnable() : void{
        BlockFactory::getInstance()->register(new Opaque(new BID(Ids::DIAMOND_BLOCK, 0), "Diamond Block", new BlockBreakInfo(4.25, BlockToolType::PICKAXE, ToolTier::IRON()->getHarvestLevel(), 30.0)), true);
        BlockFactory::getInstance()->register(new Opaque(new BID(Ids::IRON_BLOCK, 0), "Iron Block", new BlockBreakInfo(3.5, BlockToolType::PICKAXE, ToolTier::STONE()->getHarvestLevel(), 30.0)), true);
        BlockFactory::getInstance()->register(new Redstone(new BID(Ids::REDSTONE_BLOCK, 0), "Redstone Block", new BlockBreakInfo(3.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 30.0)), true);

        AutoComplete::getInstance()->registerOwner($this);

        GeneratorManager::getInstance()->addGenerator(PlotGenerator::class, "plot", true);
        $wm = $this->getServer()->getWorldManager();
        $wm->generateWorld(PlotManager::$plotworld, WorldCreationOptions::create()->setGeneratorClass(PlotGenerator::class));
        $wm->loadWorld(PlotManager::$plotworld);
        $wm->getWorldByName(PlotManager::$plotworld)->setSpawnLocation(new Vector3(0.5, 65, 0.5));

        MineManager::getInstance()->loadAll();

        KothManager::getInstance()->setup();

        InvMenuHandler::register($this);
        
        $listeners = [
            new AchievementListener(),
            new ProtectionListener(),
            new UserListener(),
            new JoinListener(),
            new ChatListener(),
            new MessagesListener(),
            new BlockBreakListener(),
            new BroadcastListener(),
            new QueryListener(),
            new CommandSpyListener(),
            new AFKListener(),
            new StaffChatListener(),
            new HungerListener(),
            //new CustomEnchantListener(),
            new PVPListener(),
            new NoCapsCommandListener(),
            new DelayedCommandListener(),
            new CombatLogListener(),
            new BanListener(),
            new PlotListener(),
            new BlockListener(),
            new GlobalmuteListener(),
            new ChatsoundListener(),
            new EnchantmentBookListener(),
            new PlayerAttackListener(),
            new CrateListener(),
            new BorderListener(),
            new NewsListener(),
            new AntispamListener(),
            new NoStackedItemsListener(),
            new GangListener(),
            new GappleListener(),
            new NoDropListener(),
            new VanishListener(),
            //new WorldChangeListener(),
        ];
        $this->getLogger()->info("registering ".count($listeners)." listeners...");
        foreach($listeners as $listener){
            $this->getServer()->getPluginManager()->registerEvents($listener, $this);
        }

        $tasks = [
            new MoneyDisplayTask(),
            new AutoSaveTask(),
            new BroadcastTask(),
            new AFKTimerTask(),
            new MineDisplayTask(),
            new TimedMineRegenerateTask(),
            new UnMuteTask(),
            new AutoMineRegenerateTask(),
            new CombatLogTask(),
            new ClearEntitiesTask(),
            new AutoRestartTask(),
            new AuctionExpireTask(),
            new AutoVoteCheckTask(),
            new AntispamTask(),
            new CustomEnchantTickTask(),
        ];

        $this->getLogger()->info("registering ".count($tasks)." tasks...");
        foreach($tasks as $task){
            $this->getScheduler()->scheduleRepeatingTask($task, $task->getInterval());
        }

        $this->getScheduler()->scheduleDelayedRepeatingTask(new AutoMineRegenerateTask(), 100, 11);

        $unregisters = [
            "pocketmine:plugins",
            "pocketmine:help",
            "pocketmine:tell",
            "pocketmine:list",
            "pocketmine:me",
            "pocketmine:tp",
            "pocketmine:say",
            "pocketmine:ban",
            "pocketmine:pardon",
            "pocketmine:enchant",
            "pocketmine:banlist",
            "pocketmine:clear",
        ];

        foreach ($unregisters as $unregister){
            Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand($unregister));
        }

        $commands = [
            new SetPrestigeCommand(),
            new SetMineCommand(),
            new BalanceCommand(),
            new RanksCommand(),
            new SpawnCommand(),
            new SetBalanceCommand(),
            new PayCommand(),
            new RankUpCommand(),
            new NickCommand(),
            new FlyCommand(),
            new BalTopCommand(),
            new LeaderboardCommand(),
            new CustomEnchantCommand(),
            new VaultCommand(),
            new SizeCommand(),
            new StatsCommand(),
            new FeedCommand(),
            new CommandSpyCommand(),
            new ShopCommand(),
            new AFKCommand(),
            new CheckIPCommand(),
            new PluginsCommand(),
            new HelpCommand(),
            new TellCommand(),
            new ReplyCommand(),
            new ListCommand(),
            new WorldCommand(),
            new SetSpawnCommand(),
            new RegenerateCommand(),
            new MyCoordsCommand(),
            new RegenerateAllCommand(),
            new AdminCommand(),
            new AchievementsCommand(),
            new MeCommand(),
            new MuteCommand(),
            new SudoCommand(),
            new ConvertWorldCommand(),
            new IdCommand(),
            new RenameWorldCommand(),
            new MineCommand(),
            new MinesCommand(),
            new DisableMineCommand(),
            new EnableMineCommand(),
            new ClearMineCommand(),
            new TagsCommand(),
            new GiveTagCommand(),
            new TeleportCommand(),
            new SayCommand(),
            new AuctionCommand(),
            new BanCommand(),
            new UnbanCommand(),
            new TempbanCommand(),
            new PlotCommand(),
            new BlockCommand(),
            new UnblockCommand(),
            new BlocklistCommand(),
            new GlobalmuteCommand(),
            //new ClearinventoryCommand(),
            new PlotsCommand(),
            new NFItemCommand(),
            new EnchantCommand(),
            new GangCommand(),
            new ForgeCommand(),
            new PrestigeCommand(),
            new FixCommand(),
            new PerformanceCommand(),
            new EnchantmentforgeCommand(),
            new PingCommand(),
            new GuideCommand(),
            new SetMaxPlotsCommand(),
            new SetMaxAucCommand(),
            new SetMaxVaultCommand(),
            new SeasonresetCommand(),
            new KitCommand(),
            new EnchantmentlistCommand(),
            new CratesCommand(),
            new CrateitemsCommand(),
            new VoteCommand(),
            new TrashCommand(),
            new KeyAllCommand(),
            new RenameCommand(),
            new DisenchantCommand(),
            new NewsCommand(),
            new IgnoreAllCommand(),
            new BanListCommand(),
            new SetBlockCommand(),
            new RulesCommand(),
            new SpectatorCommand(),
            new SurvivalCommand(),
            new InventoryseeCommand(),
            new ClearlagCommand(),
            new NicklistCommand(),
            new AliasCommand(),
            //new ReforgeCommand(),
            new BroadcastCommand(),
            new KeyCommand(),
            new SoftrestartCommand(),
            new KeyCommand(),
            new LotteryCommand(),
            new NoteCommand(),
            new WarnCommand(),
            new WarningsCommand(),
            new MywarnsCommand(),
            new ClearwarnCommand(),
            new VanishCommand(),
            new SuperlistCommand(),
            new NearCommand(),
            new KothCommand(),
        ];

        if(self::isDevServer()){
            $commands[] = new EvalCommand();
        }

        $this->getLogger()->info("registering ".count($commands)." commands...");
        foreach ($commands as $command){
            if(!$command instanceof Executor) {
                continue;
            }

            $cmd = new CommandBase($command->getName(), $this);
            $cmd->setExecutor($command);
            $cmd->setDescription($command->getDesc());
            $cmd->setPermission($command->getPermission());
            $cmd->setAliases($command->getAliases());
            $cmd->setUsage($command->getUsage());
            $this->getServer()->getCommandMap()->register("nightfall", $cmd);

            if(!empty($command->getParameters())){
                $ac = AutoComplete::getCommandMap()->register($cmd);
                $ac->setParameters($command->getParameters());
            }
        }

        $this->getScheduler()->scheduleDelayedTask(new DelayedPermissionTask(), 20);

        foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
            $world->setTime(6000);
            $world->stopTime();
        }

        $this->clearItemEntities();

        Lottery::getInstance()->setup();

        Leaderboards::getInstance()->setup();

        /*foreach(UserManager::getInstance()->getAll() as $user){
            $user->setExpiredAuctionItems([]);

            UserManager::getInstance()->save($user);
            //VaultManager::getInstance()->save($user->getVault());
        }*/
	}

	public function onDisable() : void{
        Lottery::getInstance()->refundAll();
        self::save();
	}

	public static function save() : void {
        VaultManager::getInstance()->saveAll();
        UserManager::getInstance()->saveAll();
        KothManager::getInstance()->save();
        VoteParty::getInstance()->save();
        MineManager::getInstance()->saveAll();
        AuctionManager::getInstance()->saveAll();
        BannedUserManager::getInstance()->saveAll();
        PlotManager::getInstance()->saveAll();
        GangManager::getInstance()->saveAll();
    }
}
