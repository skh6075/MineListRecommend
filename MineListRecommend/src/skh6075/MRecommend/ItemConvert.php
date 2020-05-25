<?php



namespace skh6075\MRecommend;

use pocketmine\item\Item;

class ItemConvert{



    /**
     * @param Item $item
     * @return string
     */
    public static function getConvertItemCode (Item $item): string{
        return $item->getId () . ":" . $item->getDamage () . ":" . $item->getCount () . ":" . base64_encode ($item->getCompoundTag ());
    }
    
    /**
     * @param string $code
     * @return Item
     */
    public static function getCodeToItem (string $code): Item{
        return Item::get (explode (':', $code) [0], explode (':', $code) [1], explode (':', $code) [2], base64_decode (explode (':', $code) [3]));
    }
    
    /**
     * @param Item $item
     * @return string
     */
    public static function getCustomName (Item $item): string{
        return $item->hasCustomName () ? $item->getCustomName () . "§r" : $item->getName ();
    }
    
}