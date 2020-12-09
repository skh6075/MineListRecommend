<?php

namespace skh6075\minelistrecommend\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use skh6075\minelistrecommend\MinelistRecommend;
use skh6075\minelistrecommend\thread\NetworkThread;

class RecommendCommand extends Command{

    /** @var MinelistRecommend */
    protected $plugin;


    public function __construct(MinelistRecommend $plugin) {
        parent::__construct("추천", "추천 명령어 입니다.");
        $this->setPermission("minelist.recommend.permission");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $player, string $label, array $args): bool{
        if ($player instanceof Player) {
            if (!$player->hasPermission($this->getPermission())) {
                $player->sendMessage(MinelistRecommend::$prefix . "당신은 이 명령어를 사용할 권한이 없습니다.");
                return false;
            }
            if ($this->plugin->canPlayerRecommend($player)) {
                if (NetworkThread::getInstance()->onRun($player)) {
                    $player->sendMessage(MinelistRecommend::$prefix . "추천을 하셔서 보상을 수령하였습니다.");
                } else {
                    $player->sendMessage(MinelistRecommend::$prefix . "당신의 추천 기록을 찾을 수 없습니다.");
                }
            } else {
                $player->sendMessage(MinelistRecommend::$prefix . "하루가 지나지 않아 추천 명령어를 사용할 수 없습니다.");
            }
        }
        return true;
    }
}