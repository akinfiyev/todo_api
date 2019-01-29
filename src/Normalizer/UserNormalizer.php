<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 27.01.19
 * Time: 22:20
 */

namespace App\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    /**
     * @param User $user
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($user, $format = null, array $context = [])
    {
        return [
            "apiToken" => $user->getApiToken()
        ];
    }

    public function supportsNormalization($user, $format = null)
    {
        return $user instanceof User;
    }

}