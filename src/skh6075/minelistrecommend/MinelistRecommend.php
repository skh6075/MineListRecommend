<?php

namespace skh6075\minelistrecommend;

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use skh6075\minelistrecommend\command\AddRewardCommand;
use skh6075\minelistrecommend\command\DeleteRewardCommand;
use skh6075\minelistrecommend\command\RecommendCommand;

class MinelistRecommend extends PluginBase{

    /** @var ?MinelistRecommend */
    private static $instance = null;
    /** @var string */
    public static $prefix = "§l§b[알림]§r§7 ";
    /** @var array */
    protected $db = [];


    public static function getInstance(): ?MinelistRecommend{
        return self::$instance;
    }

    public function onLoad(): void{
        if (self::$instance === null) {
            self::$instance = $this;
        }
    }

    public function onEnable(): void{
        $this->saveResource("config.json");
        $this->db = json_decode(file_get_contents($this->getDataFolder() . "config.json"), true);

        if ($this->db["recommend-site"] === null) {
            $this->getLogger()->critical("마인리스트 사이트 링크를 config.json에 작성하고 다시 서버를 활성화 해주새요.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        $this->getServer()->getCommandMap()->registerAll(strtolower($this->getName()), [
            new AddRewardCommand($this),
            new DeleteRewardCommand($this),
            new RecommendCommand($this)
        ]);
    }

    public function onDisable(): void{
        file_put_contents($this->getDataFolder() . "config.json", json_encode($this->db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function getRecommendSite(): string{
        return $this->db["recommend-site"];
    }

    public function isExistsRewardIndex(int $index): bool{
        return isset($this->db["rewards"][$index]);
    }

    public function addReward(Item $item): void{
        $this->db["rewards"] [] = $item->jsonSerialize();
    }

    public function deleteReward(int $index): void{
        if (isset($this->db["rewards"][$index])) {
            unset($this->db["rewards"][$index]);
        }
    }

    /**
     * @return Item[]
     */
    public function getRewardItems(): array{
        return array_map(function (array $json): Item{
            return Item::jsonDeserialize($json);
        }, $this->db["rewards"]);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function canPlayerRecommend(Player $player): bool{
        if (!isset($this->db["reports"][$player->getLowerCaseName()])) {
            return true;
        }
        return $this->db["reports"][$player->getLowerCaseName()] <= time();
    }

    public function addPlayerReport(Player $player): void{
        $this->db["reports"][$player->getLowerCaseName()] = strtotime("tomorrow");
    }
}