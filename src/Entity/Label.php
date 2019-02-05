<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LabelRepository")
 */
class Label
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ItemList", inversedBy="labels")
     */
    private $itemLists;

    public function __construct()
    {
        $this->itemLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|ItemList[]
     */
    public function getItemLists(): Collection
    {
        return $this->itemLists;
    }

    public function addItemList(ItemList $itemList): self
    {
        if (!$this->itemLists->contains($itemList)) {
            $this->itemLists[] = $itemList;
        }

        return $this;
    }

    public function removeItemList(ItemList $itemList): self
    {
        if ($this->itemLists->contains($itemList)) {
            $this->itemLists->removeElement($itemList);
        }

        return $this;
    }
}
