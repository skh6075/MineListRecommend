<?php


namespace skh6075\MRecommend;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Utils;

class MineListInternet{



    /**
     * @param string $name
     * @return array
     */
    public static function getSearch (string $name): array{
        $arr = [];
        for ($i = 1; $i <= 3; $i ++) {
            $url = Utils::getURL (MRecommend::getInstance ()->getMineListSite () . "{$i}");
            $url = preg_replace ("/(<([^>]+)>)/", "", $url);
            $url = str_replace ([ PHP_EOL, " ", "아이피" ], "", $url);
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
    
    /**
     * @param Player $player
     */
    public static function recommend (Player $player): void{
        $date = date ("Y/m/d");
        $name = str_replace (" ", "", $player->getName ());
        
        if (!MRecommend::getInstance ()->isDate ($date)) {
            MRecommend::getInstance ()->addDate ($date);
        }
        
        if (!MRecommend::getInstance ()->isDatePlayer ($date, $name)) {
            $search = self::getSearch ($name);
            
            if (!isset ($search [$date])) {
                $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('not-found-recommend'));
                return;
            }
            MRecommend::getInstance ()->addDatePlayer ($date, $name);
            
            array_map (function (string $code) use ($player) {
                $item = ItemConvert::getCodeToItem ($code);
                $player->getInventory ()->addItem ($item);
            }, MRecommend::getInstance ()->getRewards ());
            
            Server::getInstance ()->broadcastMessage (MRecommend::getInstance ()->getLang ()->format ('success-recommend', [
                "%name%" => $player->getName ()
            ]));
        } else {
            $player->sendMessage (MRecommend::getInstance ()->getLang ()->format ('isset-recommend'));
        }
    }
    
}
