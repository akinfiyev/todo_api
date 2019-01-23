<?php

namespace App\Controller\api;

use App\Entity\Item;
use App\Entity\ItemList;
use App\Entity\User;
use App\Normalizer\ItemListNormalizer;
use App\Security\ApiAuthenticator;
use App\Services\ValidateService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ItemListController extends AbstractController
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
     * @Route("/api/lists", methods={"POST"}, name="api_lists_create")
     */
    public function listCreateAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));

        /* @var ItemList $itemList */
        $itemList = $this->serializer->deserialize($request->getContent(), ItemList::class, 'json');
        $itemList->setUser($user);
        $this->validateService->validate($itemList);
        $this->validateService->validate($itemList->getLabels());

        $this->getDoctrine()->getManager()->persist($itemList);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($itemList);
    }

    /**
     * @Route("/api/lists", methods={"GET"}, name="api_lists_show")
     */
    public function listsShowAction(Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));
        $startId = $request->query->has('startId') ? $request->query->get('startId') : 0;

        if ($request->query->has('labelId')) {
            return $this->json($paginator->paginate(
                $this->getDoctrine()->getRepository(ItemList::class)->findAllByLabel($request->query->get('labelId'), $user, $startId),
                1,
                5));
        } else {
            /* @var ItemList $itemList */
            return $this->json($paginator->paginate(
                $this->getDoctrine()->getRepository(ItemList::class)->findAllByUser($user, $startId),
                1,
                5));
        }
    }

    /**
     * @Route("/api/list/{id}", methods={"POST"}, name="api_list_task_add")
     */
    public function listTaskAddAction(Request $request, ItemList $itemList, ItemListNormalizer $itemListNormalizer)
    {
        /* @var Item $item */
        $item = $this->serializer->deserialize($request->getContent(), Item::class, 'json');
        $this->validateService->validate($item);

        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));
        /* @var ItemList $itemList */
        $itemList = $this->getDoctrine()->getRepository(ItemList::class)->findOneById($itemList->getId(), $user);
        $itemList->addItem($item);

        $this->getDoctrine()->getManager()->flush();

        return $this->json($itemListNormalizer->normalize($itemList, ItemListNormalizer::FORMAT_DETAILED));
    }

    /**
     * @Route("/api/list/{id}", methods={"GET"}, name="api_list_show")
     */
    public function listShowAction(Request $request, ItemList $itemList, ItemListNormalizer $itemListNormalizer)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));
        /* @var ItemList $itemList */
        $itemList = $this->getDoctrine()->getRepository(ItemList::class)->findOneById($itemList->getId(), $user);

        return $this->json($itemListNormalizer->normalize($itemList, ItemListNormalizer::FORMAT_DETAILED));
    }
}
