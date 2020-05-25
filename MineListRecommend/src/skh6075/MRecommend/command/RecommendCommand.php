<?php

namespace skh6075\MRecommend\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;

use pocketmine\Player;
use skh6075\MRecommend\MRecommend;
use skh6075\MRecommend\MineListInternet;

class RecommendCommand extends Command{


    public function __construct (string $name, string $description) {
        parent::__construct ($name, $description);
    }
    
    public function execute (CommandSender $player, string $label, array $args): bool{
        if (!$player instanceof Player) {
            $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('command-use-only-ingame'));
            return true;
        }
        MineListInternet::recommend ($player);
        return true;
    }
}