<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\enchant\CustomEnchantIds;
use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\ItemInterface;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class CustomEnchantTickTask extends Task {

    public function getInterval() : int {
        return 30;
    }

    public function onRun() : void{
        $customEnchantsManager = CustomEnchantsManager::getInstance();
        $healthEnch = $customEnchantsManager->get(CustomEnchantIds::HEALTH);
        $runnerEnch = $customEnchantsManager->get(CustomEnchantIds::RUNNER);
        $leaperEnch = $customEnchantsManager->get(CustomEnchantIds::LEAPER);
        $nigvisEnch = $customEnchantsManager->get(CustomEnchantIds::NIGHT_VISION);
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $health = 0;
            $effects = $player->getEffects();

            foreach($player->getArmorInventory()->getContents(false) as $armor){
                $interface = ItemInterface::fromItem($armor);
                $customEnchants = $interface->getCustomEnchants();
                if($interface->hasEnchantment($healthEnch)){
                    $health += $customEnchants[$healthEnch->getId()]->getLevel()*2;
                }

                if($interface->hasEnchantment($runnerEnch)){
                    $effects->add(new EffectInstance(VanillaEffects::SPEED(), 100, $customEnchants[$runnerEnch->getId()]->getLevel()-1, false));
                }

                if($interface->hasEnchantment($leaperEnch)){
                    $effects->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 100, $customEnchants[$leaperEnch->getId()]->getLevel()-1, false));
                }

                if($interface->hasEnchantment($nigvisEnch)){
                    $effects->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 400, 0, false));
                }
            }

            $player->setMaxHealth(20+$health);
        }
    }
}