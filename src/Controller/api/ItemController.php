<?php

namespace App\Controller\api;

use App\Services\UploadService;
use App\Voter\ItemListVoter;
use App\Voter\ItemVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Item;
use App\Entity\ItemList;
use App\Normalizer\ItemNormalizer;
use App\Services\ValidateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ItemController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidateService
     */
    private $validateService;

    public function __construct(SerializerInterface $serializer, ValidateService $validateService)
    {
        $this->serializer = $serializer;
        $this->validateService = $validateService;
    }

    /**
     * @Route("/api/lists/{id}", methods={"POST"}, name="api_item_add")
     */
    public function addAction(Request $request, ItemList $itemList)
    {
        $this->denyAccessUnlessGranted(ItemListVoter::ADD_ITEM, $itemList);

        /* @var Item $item */
        $item = $this->serializer->deserialize($request->getContent(), Item::class, 'json');
        $this->validateService->validate($item);

        $itemList->addItem($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($item, 200, [], [AbstractNormalizer::GROUPS => [ItemNormalizer::GROUP_DETAILS]]);
    }

    /**
     * @Route("/api/item/{item}", methods={"GET"}, name="api_item_show")
     */
    public function showAction(Item $item)
    {
        $this->denyAccessUnlessGranted(ItemVoter::VIEW, $item);

        return $this->json($item, 200, [], [AbstractNormalizer::GROUPS => [ItemNormalizer::GROUP_DETAILS]]);
    }

    /**
     * @Route("/api/item/{item}", methods={"DELETE"}, name="api_item_delete")
     */
    public function deleteAction(Item $item)
    {
        $this->denyAccessUnlessGranted(ItemVoter::DELETE, $item);

        $this->getDoctrine()->getManager()->remove($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->json('ok');
    }

    /**
     * @Route("/api/item/{item}", methods={"PUT"}, name="api_item_edit")
     */
    public function editAction(Request $request, Item $item)
    {
        $this->denyAccessUnlessGranted(ItemVoter::EDIT, $item);

        if ($request->query->has('isChecked')) {
            $item->setIsChecked($request->query->get('isChecked'));
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->json('ok');
    }

    /**
     * @Route("/api/item/{item}/attachment", methods={"POST"}, name="api_item_attachment_add")
     */
    public function setAttachmentAction(Request $request, Item $item, UploadService $uploadService)
    {
        $this->denyAccessUnlessGranted(ItemVoter::EDIT, $item);

        if ($request->files->count()) {
            $attachment = $uploadService->uploadAttachment($request->files->get("attachment"));
            $item->setAttachment($attachment);
        } else {
            $item->setAttachment(null);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->json("ok");
    }
}
