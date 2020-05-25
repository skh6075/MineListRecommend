<?php

namespace skh6075\MRecommend\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;

use pocketmine\Player;
use skh6075\MRecommend\MRecommend;
use skh6075\MRecommend\ItemConvert;

class ListRewardCommand extends Command{


    public function __construct (string $name, string $description) {
        parent::__construct ($name, $description);
        $this->setPermission (Permission::DEFAULT_OP);
    }
    
    public function execute (CommandSender $player, string $label, array $args): bool{
        if (!$player->hasPermission ($this->getPermission ())) {
            $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('command-use-not-permission'));
            return true;
        }
        if (count (MRecommend::getInstance ()->getRewards ()) <= 0) {
            $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('list-reward-insufficient-count'));
            return true;
        }
        $page = isset ($args [0]) ? $args [0] : 1;
        $page = is_numeric ($page) ? (int) $page : 1;
        $this->sendRewardList ($player, $page);
        return true;
    }
    
    /**
     * @param Player $player
     * @param int $page
     */
    public function sendRewardList (Player $player, int $page = 1): void{
        $rewards = MRecommend::getInstance ()->getRewards ();
        $max_page = ceil (count ($rewards) / 5);
        $page = $page < 1 ? 1 : ($page > $max_page ? $max_page : $page);
        
        $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('list-reward-boundary-1'), [
            "%page%" => $page,
            "%max_page%" => $max_page
        ]);
        
        $index = 0;
        foreach ($rewards as $code) {
            $arr = ceil ($index / 5);
            if ($arr === $page) {
                $item = ItemConvert::getCodeToItem ($code);
                $item_name = ItemConvert::getCustomName ($item);
                $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('list-reward-boundary-2', [
                    "%index%" => $index,
                    "%item_name%" => $item_name,
                    "%item_count%" => $item->getCount ()
                ]));
            }
            $index ++;
        }
    }
    
}