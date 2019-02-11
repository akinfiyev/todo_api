<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 27.01.19
 * Time: 14:21
 */

namespace App\Listener;

use App\Entity\Item;
use Doctrine\ORM\Mapping\PreFlush;

class ItemEntityListener
{
    /** @PreFlush */
    public function preFlushHandler(Item $item)
    {
        if ($item->getIsChecked() == null)
            $item->setIsChecked(false);
    }
}