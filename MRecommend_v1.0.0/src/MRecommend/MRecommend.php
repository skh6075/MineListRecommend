<?php


namespace MRecommend;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\Utils;

use pocketmine\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\item\Item;

class MRecommend extends PluginBase
{
	
	private static $instance = null;
	
	public static $prefix = "§l§6[마인리스트]§r§7 ";
	
	public static $config, $db;
	
	private $url = "";
	
	
	public static function runFunction (): MRecommend
	{
		return self::$instance;
	}
	
	public function onLoad (): void
	{
		if (self::$instance === null) {
			self::$instance = $this;
		}
		date_default_timezone_set('Asia/Seoul');
		if (!file_exists ($this->getDataFolder ())) {
			@mkdir ($this->getDataFolder ());
		}
		self::$config = new Config ($this->getDataFolder () . "config.yml", Config::YAML, [
		   "url" => "https://minelist.kr/servers/4149/votes?page=",
			"date" => []
		]);
		self::$db = self::$config->getAll ();
		$this->url = self::$db ["url"];
	}
	
	public function onEnable (): void
	{
		$this->getServer ()->getCommandMap ()->register ("avas", new class ($this) extends Command{
			
			protected $plugin = null;
			
			
			public function __construct (MRecommend $plugin)
			{
				$this->plugin = $plugin;
				parent::__construct ("마인리스트", "마인리스트 명령어 입니다.");
			}
			
			public function execute (CommandSender $player, string $label, array $args): bool
			{
				if ($player instanceof Player) {
					$this->plugin->recommend ($player);
				}
				return true;
			}
		});
	}
	
	public function onDisable (): void
	{
		self::$config->setAll (self::$db);
		self::$config->save ();
	}
	
	public function reload (string $name): array
	{
		$arr = [];
		for ($i=1; $i<=3; $i++) {
			$url = Utils::getURL ($this->url . "{$i}");
			$url = preg_replace ("/(<([^>]+)>)/", "", $url);
			$url = str_replace ([ PHP_EOL, "   ", "아이피" ], "", $url);
			$slots = explode (" ", $url);
			$j = -1;
			while ($j < count ($slots)) {
				$j ++;
				if (isset ($slots [$j])) {
					if ($slots [$j] === $name) {
						$num = $j;
						$arr [$slots [$num + 1]] = true;
					}
				}
			}
		}
		return $arr;
	}
	
	public function recommend (Player $player): void
	{
		$date = date ("Y/m/d");
		if (!isset (self::$db ["date"] [$date])) {
			self::$db ["date"] [$date] = [];
		}
		$name = $player->getName ();
		if (!isset (self::$db ["date"] [$date] [$name])) {
			$list = $this->reload ($name);
			if (isset ($list [$date])) {
				self::$db ["date"] [$date] [$name] = date ("Y년 m월 d일 h시 i분 s초");
				$player->getInventory ()->addItem (Item::get (399, 1, 1)->setCustomName ("§l§b추천 코인"));
				$this->getServer ()->broadcastMessage (self::$prefix . "§a{$name}님§7께서 §a마인리스트 추천 보상§7 을(를) 수령하셨습니다.");
			} else {
				$player->sendMessage (self::$prefix . "추천 기록을 찾을 수 없습니다. 추천시 바로바로 명령어를 입력해주셔야 합니다.");
			}
		} else {
			$player->sendMessage (self::$prefix . "이미 오늘은 추천을 하셨습니다.");
		}
	}
}