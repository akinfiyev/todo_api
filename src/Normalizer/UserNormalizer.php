<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 27.01.19
 * Time: 22:20
 */

namespace App\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    const GROUP_LOGIN = 'Login';
    const GROUP_REGISTRATION = 'Registration';
    const GROUP_PROFILE = 'Profile';

    /**
     * @param User $user
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($user, $format = null, array $context = [])
    {
        if (isset($context[AbstractNormalizer::GROUPS]) && in_array($this::GROUP_LOGIN, $context[AbstractNormalizer::GROUPS]))
            return ["apiToken" => $user->getApiToken()];

        $data = [
            "id" => $user->getId(),
            "email" => $user->getEmail()
        ];

        if (isset($context[AbstractNormalizer::GROUPS]) && in_array($this::GROUP_REGISTRATION, $context[AbstractNormalizer::GROUPS]))
            $data["apiToken"] = $user->getApiToken();

        return $data;
    }

    public function supportsNormalization($user, $format = null)
    {
        return $user instanceof User;
    }

}