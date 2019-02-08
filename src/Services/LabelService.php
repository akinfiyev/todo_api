<?php

namespace App\Services;

use App\Entity\ItemList;
use App\Entity\Label;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;

class LabelService
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * LabelService constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param Collection $labels
     * @param ItemList $itemList
     * @return ArrayCollection
     */
    public function getSyncLabelsArray(Collection $labels, ItemList $itemList): ArrayCollection
    {
        $labels =  array_unique($labels->toArray());

        /** @var Label $label */
        foreach ($labels as $label) {
            $savedLabel = $this->om->getRepository(Label::class)->findOneByTitle($label->getTitle());
            if (isset($savedLabel)) {
                if (($key = array_search($label, $labels)) !== false)
                    unset($labels[$key]);

                $labels[] = $savedLabel;
                $savedLabel->addItemList($itemList);
            }
        }

        return new ArrayCollection($labels);
    }
}