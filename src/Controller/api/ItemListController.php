<?php

namespace App\Controller\api;

use App\Entity\Item;
use App\Entity\ItemList;
use App\Entity\User;
use App\Exception\JsonHttpException;
use App\Normalizer\ItemListNormalizer;
use App\Normalizer\ItemNormalizer;
use App\Security\ApiAuthenticator;
use App\Services\ValidateService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
     * @Route("/api/lists", methods={"POST"}, name="api_lists_add")
     */
    public function createAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));

        /* @var ItemList $itemList */
        $itemList = $this->serializer->deserialize($request->getContent(), ItemList::class, 'json');
        $this->validateService->validate($itemList);
        $itemList->setUser($user);

        $this->getDoctrine()->getManager()->persist($itemList);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($itemList);
    }

    /**
     * @Route("/api/lists", methods={"GET"}, name="api_lists_show")
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));

        $startId = $request->query->has('startId') && $request->query->get('startId') > 0 ? $request->query->get('startId') : 1;
        $listsNumber = $request->query->has('listsNumber') && $request->query->get('listsNumber') > 0 ? $request->query->get('listsNumber') : 5;

        $lists = $this->getDoctrine()->getRepository(ItemList::class)->findAllByUser($user, $startId);

        return $this->json($paginator->paginate($lists, 1, $listsNumber));
    }

    /**
     * @Route("/api/lists/{id}", methods={"DELETE"}, name="api_lists_delete")
     */
    public function deleteAction(Request $request, ItemList $itemList)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));
        if ($itemList->getUser() !== $user)
            throw new JsonHttpException(400, "Bad request");

        $this->getDoctrine()->getManager()->remove($itemList);
        $this->getDoctrine()->getManager()->flush();

        return $this->json('ok');
    }

    /**
     * @Route("/api/lists/{id}", methods={"PUT"}, name="api_lists_edit")
     */
    public function editAction(Request $request, ItemList $itemList)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));

        if ($itemList->getUser() === $user && $request->query->has('title')) {
            $itemList->setTitle($request->query->get('title'));
            $this->validateService->validate($itemList);
            $this->getDoctrine()->getManager()->flush();

            return $this->json('ok');
        } else {
            throw new JsonHttpException(400, "Bad request");
        }
    }

    /**
     * @Route("/api/lists/{id}", methods={"GET"}, name="api_lists_list_show")
     */
    public function listShowAction(Request $request, ItemList $itemList)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));
        if (!($itemList->getUser() === $user))
            throw new JsonHttpException(400, "Bad request");

        return $this->json($itemList, 200, [], [AbstractNormalizer::GROUPS => [ItemListNormalizer::GROUP_DETAILS]]);
    }

    /**
     * @Route("/api/lists/{id}", methods={"POST"}, name="api_lists_item_add")
     */
    public function itemAddAction(Request $request, ItemList $itemList)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));
        if (!($itemList->getUser() === $user))
            throw new JsonHttpException(400, "Bad request");

        /* @var Item $item */
        $item = $this->serializer->deserialize($request->getContent(), Item::class, 'json');
        $this->validateService->validate($item);

        $itemList->addItem($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($item, 200, [], [AbstractNormalizer::GROUPS => [ItemNormalizer::GROUP_DETAILS]]);
    }

    /**
     * @Route("/api/lists/{id}/item/{item}", methods={"GET"}, name="api_lists_item_show")
     */
    public function itemShowAction(Request $request, ItemList $itemList, Item $item)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));
        if (!($itemList->getUser() === $user) || !($item->getItemList() === $itemList))
            throw new JsonHttpException(400, "Bad request");

        return $this->json($item, 200, [], [AbstractNormalizer::GROUPS => [ItemNormalizer::GROUP_DETAILS]]);
    }

    /**
     * @Route("/api/lists/{id}/item/{item}", methods={"DELETE"}, name="api_lists_item_delete")
     */
    public function itemDeleteAction(Request $request, ItemList $itemList, Item $item)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));
        if (!($itemList->getUser() === $user) || !($item->getItemList() === $itemList))
            throw new JsonHttpException(400, "Bad request");

        $this->getDoctrine()->getManager()->remove($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->json('ok');
    }

    /**
     * @Route("/api/lists/{id}/item/{item}", methods={"PUT"}, name="api_lists_item_edit")
     */
    public function itemEditAction(Request $request, ItemList $itemList, Item $item)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));
        if (!($itemList->getUser() === $user) || !($item->getItemList() === $itemList))
            throw new JsonHttpException(400, "Bad request");

        if ($request->query->has('isChecked')) {
            $item->setIsChecked($request->query->get('isChecked'));
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->json('ok');
    }
}
