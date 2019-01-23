<?php

namespace App\Controller\api;

use App\Entity\Item;
use App\Entity\ItemList;
use App\Entity\User;
use App\Normalizer\ItemListNormalizer;
use App\Security\ApiAuthenticator;
use App\Services\ItemService;
use App\Services\ValidateService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/api/list/{id}/task/{item}", methods={"PUT"}, name="api_task_edit")
     */
    public function taskEditAction(Request $request, ItemList $itemList, Item $item, ItemService $itemService)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));
        /* @var ItemList $itemList */
        $itemList = $this->getDoctrine()->getRepository(ItemList::class)->findOneById($itemList->getId(), $user);
        $item = $itemService->updateItemFromJson(
            $this->getDoctrine()->getRepository(Item::class)->findOneByParams($item->getId(), $itemList),
            $this->serializer->deserialize($request->getContent(), Item::class, 'json')
        );

        $this->getDoctrine()->getManager()->flush();

        return $this->json($item);
    }
}
