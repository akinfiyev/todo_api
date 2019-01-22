<?php

namespace App\Controller\api;

use App\Entity\ItemList;
use App\Entity\User;
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
        $page = $request->query->has('page') ? $request->query->get('page') : 1;

        return $this->json($paginator->paginate(
            $this->getDoctrine()->getRepository(ItemList::class)->findAllByUser($user),
            $page,
            5));
    }
}
