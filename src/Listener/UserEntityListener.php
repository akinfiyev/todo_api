<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 27.01.19
 * Time: 14:21
 */

namespace App\Listener;

use App\Entity\User;
use App\Services\EncodeService;
use Doctrine\ORM\Mapping\PreFlush;

class UserEntityListener
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
    public function preFlushHandler(User $user)
    {
        if ($user->getPlainPassword() != null)
            $user->setPassword($this->encodeService->encodeUserPassword($user->getPlainPassword(), $user));
    }
}