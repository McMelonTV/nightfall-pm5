<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use pocketmine\permission\PermissionManager;
use pocketmine\scheduler\Task;

class DelayedPermissionTask extends Task {

    public function onRun() : void {
        $perms = [
            "pocketmine.command.kill",
            "pocketmine.command.kill.other",
            "pocketmine.command.kill.self",
        ];

        foreach($perms as $perm){
            $permClass = PermissionManager::getInstance()->getPermission($perm);
            if($permClass === null) continue;
            $permClass->addChild("op", true); // wtf am i doing
        }
    }
}