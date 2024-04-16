<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\Core;
use pocketmine\event\Listener;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\network\query\QueryInfo;

class QueryListener implements Listener {

    public function onQueryRegenerate(QueryRegenerateEvent $ev) : void {
        $info = $ev->getQueryInfo();
        if(Core::isDevServer()){
            $info->setWorld("Nightfall");
            $info->setPlayerCount(-1);
            $info->setMaxPlayerCount(0);
            $info->setListPlugins(false);
            $info->setServerName("Nightfall devserver");
            \Closure::bind(function (){
                $this->server_engine = "Nightfall";
                $this->version = "v2.0.0";
            }, $info, QueryInfo::class)();
        }else{
            $info->setWorld("Nightfall");
            $info->setListPlugins(false);
            $info->setMaxPlayerCount(420);
            $info->setServerName("Nightfall");
            \Closure::bind(function (){
                $this->server_engine = "Nightfall";
            }, $info, QueryInfo::class)();
        }
    }
}