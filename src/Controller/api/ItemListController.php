<?php

namespace App\Controller\api;

use App\Entity\ItemList;
use App\Entity\User;
use App\Exception\JsonHttpException;
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
     * @Route("/api/lists", methods={"POST"}, name="api_lists_add")
     */
    public function createAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($request->headers->get(ApiAuthenticator::X_API_KEY));

        /* @var ItemList $itemList */
        $itemList = $this->serializer->deserialize($request->getContent(), ItemList::class, 'json');
        $itemList->setUser($user);
        $this->validateService->validate($itemList);

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
            $this->getDoctrine()->getManager()->flush();

            return $this->json('ok');
        } else {
            throw new JsonHttpException(400, "Bad request");
        }
    }
}
