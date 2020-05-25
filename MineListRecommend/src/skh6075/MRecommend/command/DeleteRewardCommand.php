<?php



namespace skh6075\MRecommend\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;

use pocketmine\Player;
use skh6075\MRecommend\MRecommend;

class DeleteRewardCommand extends Command{


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
        if (!isset ($args [0]) || !is_numeric ($args [0])) {
            $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('delete-reward-help'));
            return true;
        }
        if (!MRecommend::getInstance ()->isKeyReward ($args [0])) {
            $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('delete-reward-key-not-found'));
            return true;
        }
        MRecommend::getInstance ()->deleteReward ($args [0]);
        $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('delete-reward-key-success'));
        return true;
    }
    
}