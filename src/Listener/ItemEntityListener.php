<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 27.01.19
 * Time: 14:21
 */

namespace App\Listener;

use App\Entity\Item;
use App\Services\EncodeService;
use Doctrine\ORM\Mapping\PreFlush;

class ItemEntityListener
{
    /**
     * @var EncodeService
     */
    private $encodeService;

    /**
     * UserListener constructor
     *
     * @param $encodeService
     */
    public function __construct(EncodeService $encodeService)
    {
        $this->encodeService = $encodeService;
    }

    /** @PreFlush */
    public function preFlushHandler(Item $item)
    {
        if ($item->getIsChecked() == null)
            $item->setIsChecked(false);
    }
}