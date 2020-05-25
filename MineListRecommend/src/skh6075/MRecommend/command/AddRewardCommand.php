<?php



namespace skh6075\MRecommend\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;

use pocketmine\Player;
use skh6075\MRecommend\MRecommend;

class AddRewardCommand extends Command{


    public function __construct (string $name, string $description) {
        parent::__construct ($name, $description);
        $this->setPermission (Permission::DEFAULT_OP);
    }
    
    public function execute (CommandSender $player, string $label, array $args): bool{
        if (!$player instanceof Player) {
            $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('command-use-only-ingame'));
            return true;
        }
        if (!$player->hasPermission ($this->getPermission ())) {
            $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('command-use-not-permission'));
            return true;
        }
        $item = $player->getInventory ()->getItemInHand ();
        if ($item->isNull ()) {
            $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('add-reward-item-not-air'));
            return true;
        }
        MRecommend::getInstance ()->addReward ($item);
        $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('add-reward-item-success'));
        return true;
    }
    
}