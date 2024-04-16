<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\achievement\Achievement;
use AndreasHGK\Core\achievement\AchievementManager;
use AndreasHGK\Core\crate\Crate;
use AndreasHGK\Core\crate\CrateManager;
use AndreasHGK\Core\enchant\CEMineEvent;
use AndreasHGK\Core\enchant\CustomEnchantIds;
use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\item\CrateKey;
use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\manager\GlobalPrices;
use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\ItemBreakSound;
use pocketmine\world\sound\XpCollectSound;
use function array_merge;
use function ceil;
use function count;
use function max;
use function min;
use function mt_rand;

//use DenielWorld\NFAC\handlers\NukerHandler;
class BlockBreakListener implements Listener {

    public GlobalPrices $globalPrices;

    public CustomItemManager $customItemManager;

    public MineManager $mineManager;

    public CrateManager $crateManager;

    public UserManager $userManager;

    public AchievementManager $achievementManager;

    public CustomEnchantsManager $customEnchantManager;

    public function __construct(){
        $this->globalPrices = GlobalPrices::getInstance();
        $this->customItemManager = CustomItemManager::getInstance();
        $this->mineManager = MineManager::getInstance();
        $this->crateManager = CrateManager::getInstance();
        $this->userManager = UserManager::getInstance();
        $this->achievementManager = AchievementManager::getInstance();
        $this->customEnchantManager = CustomEnchantsManager::getInstance();
    }

    public function onStartMine(PlayerInteractEvent $ev) : void {
        if($ev->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK){
            $interface = ItemInterface::fromItem($ev->getItem());
            $obBreaker = $this->customEnchantManager->get(CustomEnchantIds::OBSIDIAN_BREAKER);
            $block = $ev->getBlock();
            if($interface->hasEnchantment($obBreaker) && $block->getId() === BlockLegacyIds::OBSIDIAN){
                if(mt_rand(0, 100) < 5 * $obBreaker->getLevel()){
                    $ev->getPlayer()->breakBlock($block->getPos());
                    $ev->cancel();
                }
            }
        }
    }

    /**
     * @param BlockBreakEvent $ev
     *
     * @priority High
     */
    public function onMine(BlockBreakEvent $ev) : void {
        $interface = ItemInterface::fromItem($ev->getItem());
        $mineEvent = new CEMineEvent($ev);
        foreach($interface->getCustomEnchants() as $customEnchant){
            $customEnchant->onMine($mineEvent);
        }

        $player = $ev->getPlayer();
        $playerPos = $player->getPosition();
        $playerInventory = $player->getInventory();

        $user = $this->userManager->getOnline($player);

        $originalBlock = $ev->getBlock();
        $originalPos = $originalBlock->getPos();

        $world = $player->getWorld();

        $hardness = $originalBlock->getBreakInfo()->getHardness();

        $mine = $this->mineManager->getMineAt($originalPos->x, $originalPos->y, $originalPos->z, $world);
        if($mine === null) {
            if($interface->isDurable() && $interface->getMaxDamage() <= $interface->getDamage() + 1){
                $user->sendTip("§r§8[§bNF§8]\n§r§7Your pickaxe has no durability left.\n§r§7Repair it using §b/forge§r§7.");
                $user->playSound(new ItemBreakSound());
                $ev->cancel();
                return;
            }

            if($interface->isDurable() && $interface->doUseDurability()){
                $interface->applyDamage(1);
                if($mineEvent->getAutoRepair() && $interface->getDamage()/$interface->getMaxDamage() > 0.5){
                    $obsidianShard = $this->customItemManager->get(CustomItem::OBSIDIANSHARD)->getItem();
                    if($playerInventory->contains($obsidianShard)){
                        $playerInventory->removeItem($obsidianShard);
                        $interface->applyDamage(min(-400, $interface->getDamage()));
                    }
                }

                $interface->recalculateDamage();
                $interface->recalculateLore();
                $interface->saveStats();
                $playerInventory->setItemInHand($interface->getItem());
            }

            $playerInventory->addItem(...$ev->getDrops());
            $ev->setDrops([]);
            $ev->setXpDropAmount(0);
            return;
        }

        $ev->cancel();
        $blocks = $mineEvent->getMinedBlocks();

        if($interface->isDurable() && $interface->getMaxDamage() <= $interface->getDamage() + count($blocks) - 1){
            $user->sendTip("§r§8[§bNF§8]\n§r§7Your pickaxe has no durability left.\n§r§7Repair it using §b/forge§r§7.");
            $user->playSound(new ItemBreakSound());
            return;
        }

        $totalPrice = 0;
        $prices = $mine->getPrices();
        $globalPrices = $this->globalPrices;

        $xp = 0;
        $xpmanager = $player->getXpManager();
        foreach($blocks as $block){
            $pos = $block->getPos();
            if(!$user->canDestroyAt($pos)) {
                continue;
            }

            $driller = true;
            if($pos->equals($originalPos)) {
                $driller = false;
            }

            $mine1 = $this->mineManager->getMineAt($pos->x, $pos->y, $pos->z, $world);
            if($mine1 === null || $mine1->getId() !== $mine->getId()) {
                continue;
            }

            $world->setBlock($pos, VanillaBlocks::AIR());

            //$world->addParticle($pos->add(0.5, 0.5, 0.5), new DestroyBlockParticle($block));
            $drop = $block->asItem();
            $newDrop = $drop;

            if($mineEvent->getFusion()){
                switch ($newDrop->getId()){
                    case ItemIds::COAL:
                        $newDrop = VanillaItems::IRON_INGOT();
                        break;
                    case ItemIds::IRON_INGOT:
                        $newDrop = VanillaItems::GOLD_INGOT();
                        break;
                    case ItemIds::GOLD_INGOT:
                        $newDrop = VanillaItems::REDSTONE_DUST();
                        break;
                    case ItemIds::REDSTONE_DUST:
                        $newDrop = VanillaItems::LAPIS_LAZULI();
                        break;
                    case ItemIds::DYE:
                        $newDrop = VanillaItems::DIAMOND();
                        break;
                    case ItemIds::DIAMOND:
                        $newDrop = VanillaItems::EMERALD();
                        break;
                    case ItemIds::COAL_BLOCK:
                        $newDrop = VanillaBlocks::IRON()->asItem();
                        break;
                    case ItemIds::IRON_BLOCK:
                        $newDrop = VanillaBlocks::GOLD()->asItem();
                        break;
                    case ItemIds::GOLD_BLOCK:
                        $newDrop = VanillaBlocks::REDSTONE()->asItem();
                        break;
                    case ItemIds::REDSTONE_BLOCK:
                        $newDrop = VanillaBlocks::LAPIS_LAZULI()->asItem();
                        break;
                    case ItemIds::LAPIS_BLOCK:
                        $newDrop = VanillaBlocks::DIAMOND()->asItem();
                        break;
                    case ItemIds::DIAMOND_BLOCK:
                        $newDrop = VanillaBlocks::EMERALD()->asItem();
                        break;
                }
            }

            $drop = $newDrop ?? $drop;
            //$player->getXpManager()->addXp($block->getXpDropForTool($ev->getItem())+1);
            //$xpmanager->setCurrentTotalXp($xpmanager->getCurrentTotalXp() + $block->getXpDropForTool($ev->getItem())+1);
            $world->addSound($playerPos, new XpCollectSound(), [$player]);

            $xp += 1;
            if(lcg_value() < 0.33) {
                $xp += $mineEvent->getXPBoost();
            }

            $user->addMinedBlock();

            $id = (string)$drop->getId();
            $meta = (string)$drop->getMeta();
            $count = $drop->getCount();
            if(isset($prices[$id.":".$meta])){
                $price = $prices[$id.":".$meta]*$count;
            }elseif(isset($prices[$id])){ //isset($mine->getPrices()[(string)$drop->getId().":".$drop->getMeta()])
                $price = $prices[$id]*$count;
            }elseif($globalPrices->exist($id.":".$meta)){
                $price = $globalPrices->get($id.":".$meta)*$count;
            }elseif($globalPrices->exist($id)){
                $price = $globalPrices->get($id)*$count;
            }else{
                $playerInventory->addItem($drop);
            }

            if($driller){
                $price = 0.1*$price;
            }

            $totalPrice += $price ?? 0;
        }

        $xpmanager->setCurrentTotalXp((int)($xpmanager->getCurrentTotalXp() + $xp));

        $mine->reduceBlockCount(count($blocks));

        $v = (int)ceil($totalPrice*$mineEvent->getPriceModifier()*$user->getPrestigeBoost());

        $user->addMoney($v);
        $user->setTotalEarnedMoney($user->getTotalEarnedMoney()+$v);
        if($interface->isDurable() && $interface->doUseDurability()){
            $interface->applyDamage(count($blocks));
            if($mineEvent->getAutoRepair() && $interface->getDamage()/$interface->getMaxDamage() > 0.5){
                $obsidianShard = $this->customItemManager->get(CustomItem::OBSIDIANSHARD)->getItem();
                if($playerInventory->contains($obsidianShard)){
                    $playerInventory->removeItem($obsidianShard);
                    $interface->applyDamage(min(-400, $interface->getDamage()));
                }
            }

            $interface->recalculateDamage();
            $interface->recalculateLore();
            $interface->saveStats();
            $playerInventory->setItemInHand($interface->getItem());
        }

        if($ev->getInstaBreak()){
            $hardness = 1.1;
        }

        $extra = 400 * (3 - max(min($hardness, 5), 0.5));
        if(mt_rand(0, (int) (1000 + $mineEvent->getResourceBoost() + $extra)) > (int) (950 + $extra)){
            $item = $this->customItemManager->get($this->getRandomMaterial($mineEvent->doMineStardust()));
            if($item !== null){
                $playerInventory->addItem($item->getItem());
            }
        }

        $time = time();
        $lastRelic = max(min(120 - ($time - $user->getLastRelic()), 120), 1);
        if(mt_rand(0, 400 * ((int) ($lastRelic ** 1.2))) <= 1){
            $chanceArray = [];
            foreach ($this->crateManager->getAll() as $crate){
                $chanceArray = array_merge($chanceArray, array_fill(0, $crate->getDropChance(), $crate));
            }

            if(!empty($chanceArray)){
                $crate = $chanceArray[array_rand($chanceArray)];
                if($crate instanceof Crate){
                    /** @var CrateKey $cItem */
                    $cItem = $this->customItemManager->get(CustomItem::CRATEKEY);
                    $playerInventory->addItem($cItem->getVariant($crate->getId()));
                    $user->setLastRelic($time);
                }
            }
        }

        $minedBlocks = $user->getMinedBlocks();
        if($minedBlocks >= 10000){
            $this->achievementManager->tryAchieve($user, Achievement::GRINDER_1);
        }else{
            $this->achievementManager->tryAchieve($user, Achievement::TIME_TO_MINE);
        }

        if($minedBlocks >= 50000){
            $this->achievementManager->tryAchieve($user, Achievement::GRINDER_2);
        }

        if($minedBlocks >= 100000){
            $this->achievementManager->tryAchieve($user, Achievement::GRINDER_3);
        }

        if($minedBlocks >= 250000){
            $this->achievementManager->tryAchieve($user, Achievement::GRINDER_4);
        }

        if($minedBlocks >= 1000000){
            $this->achievementManager->tryAchieve($user, Achievement::GRINDER_5);
        }

        $totalEarnedMoney = $user->getTotalEarnedMoney();
        if($totalEarnedMoney >= 10000){
            $this->achievementManager->tryAchieve($user, Achievement::BIG_BUCKS_1);
        }

        if($totalEarnedMoney >= 100000){
            $this->achievementManager->tryAchieve($user, Achievement::BIG_BUCKS_2);
        }

        if($totalEarnedMoney >= 1000000){
            $this->achievementManager->tryAchieve($user, Achievement::BIG_BUCKS_3);
        }

        if($totalEarnedMoney >= 10000000){
            $this->achievementManager->tryAchieve($user, Achievement::BIG_BUCKS_4);
        }

        if($totalEarnedMoney >= 100000000){
            $this->achievementManager->tryAchieve($user, Achievement::BIG_BUCKS_5);
        }
    }

    public function getRandomMaterial(bool $stardust = false) : int {
        $f = lcg_value();
        if($stardust && $f < 0.07){
            return CustomItem::STARDUST;
        }

        if($f < 0.26){
            return CustomItem::MAGICDUST;
        }

        if($f < 0.38){
            return CustomItem::OBSIDIANSHARD;
        }

        if(!$stardust){
            return CustomItem::STEELDUST;
        }

        return 0;
    }
}