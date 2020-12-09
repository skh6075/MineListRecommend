<?php

namespace skh6075\minelistrecommend\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use skh6075\minelistrecommend\MinelistRecommend;

class DeleteRewardCommand extends Command{

    /** @var MinelistRecommend */
    protected $plugin;


    public function __construct(MinelistRecommend $plugin) {
        parent::__construct("추천 보상삭제", "추천 보상삭제 명령어 입니다.", "/추천 보상삭제 [보상번호]");
        $this->setPermission("minelist.deletereward.permission");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $player, string $label, array $args): bool{
        if ($player instanceof Player) {
            if (!$player->hasPermission($this->getPermission())) {
                $player->sendMessage(MinelistRecommend::$prefix . "당신은 이 명령어를 사용할 권한이 없습니다.");
                return false;
            }
            $index = array_shift($args) ?? '';
            if (trim($index) === '' and !is_numeric($index)) {
                $player->sendMessage(MinelistRecommend::$prefix . $this->getUsage());
                return false;
            }
            if ($this->plugin->isExistsRewardIndex($index)) {
                $this->plugin->deleteReward($index);
                $player->sendMessage(MinelistRecommend::$prefix . "해당 번호에 해당하는 보상을 삭제하였습니다.");
            } else {
                $player->sendMessage(MinelistRecommend::$prefix . "해당 번호의 보상을 찾을 수 없습니다.");
            }
        }
        return true;
    }
}