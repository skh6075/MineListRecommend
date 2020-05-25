<?php


namespace skh6075\MRecommend;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Internet;
use pocketmine\utils\Config;

use pocketmine\item\Item;

use skh6075\MRecommend\lang\PluginLang;
use skh6075\MRecommend\command\AddRewardCommand;
use skh6075\MRecommend\command\DeleteRewardCommand;
use skh6075\MRecommend\command\ListRewardCommand;
use skh6075\MRecommend\command\RecommendCommand;

class MRecommend extends PluginBase{

    /** @var MRecommend */
    private static $instance = null;
    
    /** @var array */
    protected $data = [];
    
    /** @var PluginLang */
    private $lang = null;
    
    
    
    public static function getInstance (): ?MRecommend{
        return self::$instance;
    }
    
    public function onLoad (): void{
        self::$instance = $this;
        date_default_timezone_set ('Asia/Seoul');
        
        if (!file_exists ($this->getDataFolder () . "setting.json")) {
            file_put_contents ($this->getDataFolder () . "setting.json", json_encode ($this->getDataArray (), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        
        $this->settingResources ();
        $this->data = json_decode (file_get_contents ($this->getDataFolder () . "setting.json"), true);
        $this->lang = new PluginLang ($this, $this->data ['lang']);
    }
    
    public function onEnable (): void{
    
        (new Config (\pocketmine\DATA . "server.properties", Config::PROPERTIES))->set ("enable-query", "true");
        
        if ($this->data ['minelist-url'] === '') {
            $this->getLogger ()->error ($this->getLang ()->format ('not-found-minelist-address'));
            $this->getServer ()->getPluginManager ()->disablePlugin ($this);
            return;
        }
        if (Internet::getURL ($this->data ['minelist-url']) === null) {
            $this->getLogger ()->error ($this->getLang ()->format ('site-not-access'));
            $this->getServer ()->getPluginManager ()->disablePlugin ($this);
            return;
        }
        
        $this->getServer ()->getCommandMap ()->registerAll ("avas", [
            new AddRewardCommand ($this->getLang ()->format ('add-reward-command-name', [], false), $this->getLang ()->format ('add-reward-command-description', [], false)),
            new DeleteRewardCommand ($this->getLang ()->format ('delete-reward-command-name', [], false), $this->getLang ()->format ('delete-reward-command-description', [], false)),
            new ListRewardCommand ($this->getLang ()->format ('list-reward-command-name', [], false), $this->getLang ()->format ('list-reward-command-description', [], false)),
            new RecommendCommand ($this->getLang ()->format ('recommned-command-name', [], false), $this->getLang ()->format ('recommend-command-description', [], false))
        ]);
    }
    
    public function onDisable (): void{
        file_put_contents ($this->getDataFolder () . "setting.json", json_encode ($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * @return array 
     */
    public function getDataArray (): array{
        return [
            "lang" => (new Config (\pocketmine\DATA . "server.properties", Config::PROPERTIES))->get ("language"),
            "minelist-url" => "",
            "reward" => [],
            "date" => []
        ];
    }
    
    public function settingResources (): void{
        array_map (function (string $json): void{
            $this->saveResource ($json, true);
        }, [ "kor.yml", "eng.yml" ]);
    }
    
    /**
     * @param Item $item
     */
    public function addReward (Item $item): void{
        $this->data ["reward"] [] = ItemConvert::getConvertItemCode ($item);
    }
    
    /**
     * @param int $key
     * @return bool
     */
    public function isKeyReward (int $key = 0): bool{
        return isset ($this->data ["reward"] [$key]);
    }
    
    /**
     * @param int $key
     */
    public function deleteReward (int $key = 0): void{
        unset ($this->data ["reward"] [$key]);
    }
    
    /**
     * @return array
     */
    public function getRewards (): array{
        return $this->data ["reward"];
    }
    
    /**
     * @return string
     */
    public function getMineListSite (): string{
        return $this->data ["minelist-url"];
    }
    
    /**
     * @param string $date
     * @return bool
     */
    public function isDate (string $date): bool{
        return isset ($this->data ["date"] [$date]);
    }
    
    /**
     * @param string $date
     */
    public function addDate (string $date): void{
        $this->data ["date"] [$date] = [];
    }
    
    /**
     * @param string $date
     * @param string $name
     * @return bool
     */
    public function isDatePlayer (string $date, string $name): bool{
        return isset ($this->data ["date"] [$date] [$name]);
    }
    
    /**
     * @param string $date
     * @param string $name
     */
    public function addDatePlayer (string $date, string $name): void{
        $this->data ["date"] [$date] [$name] = date ("Y-m-d h:i:s");
    }
    
    /**
     * @return PluginLang
     */
    public function getLang (): PluginLang{
        return $this->lang;
    }
    
}