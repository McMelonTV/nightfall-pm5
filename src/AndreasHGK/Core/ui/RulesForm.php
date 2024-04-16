<?php
declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\user\UserManager;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;

class RulesForm {

    public static function sendTo(Player $sender) : void {
        $user = UserManager::getInstance()->getOnline($sender);

        $user->setSeenRules(true);

        $ui = new CustomForm(null);
        $ui->setTitle("§bServer rules");

        $textA = [
            "§r§81 - §r§l§bBe respectful and kind\n§r§7Everyone should be respected, other players and staff members alike. Toxicity in chat is not acceptable and will be punished appropriately.",
            "§r§82 - §r§l§bListen to the staff\n§r§7The staff team is here to help. Feel free to ask them anything and they will offer their assistance! If you are instructed to stop doing something, listen to them. If you refuse it will result in a ban/kick.",
            "§r§83 - §r§l§bNo exploiting, or hacking\n§r§7Hacks, exploiting bug or glitches are prohibited on the server. If you come across someone hacking or abusing something that can be exploited, please let the staff know by doing /tell and their ign! \n§r§8(ANY KIND OF CLIENTS, INCLUDING NON-HACKED, ARE NOT ALLOWED! exploits could include: block glitching, item duping, very small or large custom skins in pvp, money boosting, ect.)",
            "§r§84 - §r§l§bNo griefing\n§r§7Destroying or stealing from someone else’s property is prohibited and will be punished.",
            "§r§85 - §r§l§bNo advertising\n§r§7Do not advertise on the server, this is not appreciated and can result in punishment. If you would like to apply for the YouTuber rank, make a ticket on the NF discord with the details of your channel!",
            "§r§86 - §r§l§bDo not beg for unbans/ban evade\n§r§7Do not ask the staff to unban a player (a friend etc.) or ban evade using an alt account. If you’re banned, you can however create a ticket on the NF discord. In the ticket, explain why you were banned and the staff team can assist you.",
            "§r§87 - §r§l§bDon't cause drama\n§r§7If you are to have an issue between you and someone else please keep it out of the server or report your issue to a staff member. You can report it in a ticket on the discord or DM a staff in game. That way it's a pleasant experience for all of us!",
            "§r§88 - §r§l§bNo scamming or stealing\n§r§7Trading items is allowed on the server, but do not scam or steal items. If you do, it will result in a punishment.",
            "§r§89 - §r§l§bProvide proof\n§r§7If another player has been hacking or wronged you in any way (scammed etc.), please create a ticket and provide proof. This way our staff team can take a look at it and give proper punishment.",
            "§r§810 - §r§l§bNo inappropriate comments, usernames or skins\n§r§7Inappropriate comments, usernames or skins are prohibited. If we deem one of these things inappropriate we will ask you to change or stop it. If you don't, it will result in punishment.",
            "§r§811 - §r§l§bMoney Boosting is prohibited\n§r§7Donating money to players or an alt account in anyway is not allowed. If you are caught taking part in this, you will be punished appropriately. This includes prestige boosting (also known as self boosting).",
        ];
        $ui->addLabel(implode("\n\n", $textA));

        $sender->sendForm($ui);
    }
}