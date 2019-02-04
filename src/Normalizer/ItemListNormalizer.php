<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 27.01.19
 * Time: 22:20
 */

namespace App\Normalizer;

use App\Entity\ItemList;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ItemListNormalizer implements NormalizerInterface
{
    const GROUP_DETAILS = 'Details';

    /**
     * @param ItemList $itemList
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($itemList, $format = null, array $context = [])
    {
        $data = [
            "id" => $itemList->getId(),
            "title" => $itemList->getTitle()
        ];

        if (isset($context[AbstractNormalizer::GROUPS]) && in_array($this::GROUP_DETAILS, $context[AbstractNormalizer::GROUPS])) {
//            if (!empty($itemList->getItems())) {
//                $data['items'] = [];
//                foreach ($itemList->getItems() as $item) {
//                    $data['items'][] = $item->jsonSerialize();
//                }
//            }
        }

        return $data;
    }

    public function supportsNormalization($itemList, $format = null)
    {
        return $itemList instanceof ItemList;
    }

}