<?php

namespace skh6075\minelistrecommend\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use skh6075\minelistrecommend\MinelistRecommend;

class AddRewardCommand extends Command{

    /** @var MinelistRecommend */
    protected $plugin;


    public function __construct(MinelistRecommend $plugin) {
        parent::__construct("추천 보상추가", "추천 보상추가 명령어 입니다.");
        $this->setPermission("minelist.addreward.permission");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $player, string $label, array $args): bool{
        if ($player instanceof Player) {
            if (!$player->hasPermission($this->getPermission())) {
                $player->sendMessage(MinelistRecommend::$prefix . "당신은 이 명령어를 사용할 권한이 없습니다.");
                return false;
            }
            $item = $player->getInventory()->getItemInHand();
            if (!$item->isNull()) {
                $count = array_shift($args) ?? '';
                if (trim($count) !== '' and is_numeric($count)) {
                    $item->setCount($count);
                }
                $this->plugin->addReward($item);
                $player->sendMessage(MinelistRecommend::$prefix . "손에든 아이템을 보상에 추가하였습니다");
            } else {
                $player->sendMessage(MinelistRecommend::$prefix . "공기는 추가할 수 없습니다.");
            }
        }
        return true;
    }
}