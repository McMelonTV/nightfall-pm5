<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\manager\DataManager;
use AndreasHGK\Core\user\BannedUserManager;
use AndreasHGK\Core\user\UserData;
use AndreasHGK\Core\utils\FileUtils;
use AndreasHGK\Core\utils\StringSet;
use Generator;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\AsyncTask;
use function explode;
use function implode;
use function serialize;
use function strtolower;
use function unserialize;

class AliasTask extends AsyncTask{
    public const USER_FOLDER = "users".DIRECTORY_SEPARATOR;

    private $target;

    private $dataFolder;

    public function __construct(CommandSender $sender, array $target, string $dataFolder){
        self::storeLocal("sender", $sender);
        $this->target = serialize($target);
        $this->dataFolder = $dataFolder;
    }

    public function onRun() : void{
        DataManager::$dataFolder = $this->dataFolder;
        $target = unserialize($this->target);

        $targetName = strtolower($target["name"]);
        $targetIps = $target["iplist"];
        $targetCids = $target["cidlist"];
        $targetDids = $target["didlist"];

        $alts = new StringSet();
        foreach($this->getAllUsers() as $name => $user){
            /** @var UserData $user */
            if($name === $targetName){
                continue;
            }

            $ipList = $user->getIPList();
            foreach($targetIps as $ip) {
                if($ipList->contains($ip)){
                    $alts->add($name);
                    goto next;
                }
            }

            $clientIdList = $user->getClientIdList();
            foreach($targetCids as $ip) {
                if($clientIdList->contains((string) $ip)){
                    $alts->add($name);
                    goto next;
                }
            }

            $deviceIdList = $user->getDeviceIdList();
            foreach($targetDids as $ip) {
                if($deviceIdList->contains($ip)){
                    $alts->add($name);
                    goto next;
                }
            }

            next:
        }

        $this->setResult($alts);
    }

    /**
     * @return Generator<string, UserData>
     */
    public function getAllUsers() : Generator{
        $scan = DataManager::getFilesIn(self::USER_FOLDER);
        foreach($scan as $filename){
            /** @var string $name */
            $name = explode(".", $filename)[0];
            yield $name => $this->getUser($name);
        }
    }

    public function getUser(string $name) : UserData{
        $file = DataManager::get(self::USER_FOLDER.FileUtils::MakeJSON(strtolower($name)));

        $user = new UserData($name);
        $user->setIPList(StringSet::fromArray($file->get("iplist", [])));
        $user->setDeviceIdList(StringSet::fromArray($file->get("deviceIdList", [])));
        $user->setClientIdList(StringSet::fromArray($file->get("clientIdList", [])));

        return $user;
    }

    public function onCompletion() : void{
        $alts = $this->getResult()->toArray();

        $sender = self::fetchLocal("sender");

        /**@var CommandSender $sender */
        foreach($alts as $k => $alt){
            if(BannedUserManager::getInstance()->isBanned($alt)){
                $alts[$k] = "§c".$alt."§r";
            }
        }

        $sender->sendMessage("Alias for " . (unserialize($this->target))["name"] . "\n" . implode("\n", $alts));
    }
}