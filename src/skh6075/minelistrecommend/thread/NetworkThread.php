<?php

namespace skh6075\minelistrecommend\thread;

use pocketmine\Player;
use pocketmine\utils\Internet;
use skh6075\minelistrecommend\MinelistRecommend;

final class NetworkThread{

    /** @var ?NetworkThread */
    private static $instance = null;
    /** @var MinelistRecommend */
    protected $plugin;


    public static function getInstance(): ?NetworkThread{
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->plugin = MinelistRecommend::getInstance();
    }

    /**
     * @param Player $player
     * @return array
     */
    private function getSearchByPlayer(Player $player): array{
        $result = [];

        for ($i = 1; $i < 3; $i++) {
            $url = Internet::getURL($this->plugin->getRecommendSite() . $i);
            $url = preg_replace("/(<([^>]+)>)/", "", $url);
            $url = str_replace([PHP_EOL, " ", "아이피"], "", $url);
            $slots = explode(" ", $url);
            $j = -1;
            while ($j < count($slots)) {
                $j++;
                if (isset($slots[$j]) || $slots[$j] !== $player->getLowerCaseName()) {
                    continue;
                }
                $temp = $j;
                $result [$slots [$temp + 1]] = true;
            }
        }
        return $result;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function onRun(Player $player): bool{
        $arr = $this->getSearchByPlayer($player);
        $timeFormat = date("Y/m/d");

        if (isset($arr [$timeFormat])) {
            $this->plugin->addPlayerReport($player);
            $player->getInventory()->addItem(...$this->plugin->getRewardItems());
            return true;
        }
        return false;
    }
}
