<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 27.01.19
 * Time: 22:20
 */

namespace App\Normalizer;

use App\Entity\Item;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ItemNormalizer implements NormalizerInterface
{
    /**
     * @param Item $item
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($item, $format = null, array $context = [])
    {
        $data = [
            "id" => $item->getId(),
            "title" => $item->getTitle()
        ];

        if (!empty($item->getExpirationDate()))
            $data["expirationDate"] = $item->getExpirationDate()->format('H:i d-m-Y');

        return $data;
    }

    public function supportsNormalization($item, $format = null)
    {
        return $item instanceof Item;
    }

}