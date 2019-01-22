<?php

namespace App\Controller\api;

use App\Entity\ItemList;
use App\Entity\User;
use App\Exception\JsonHttpException;
use App\Repository\UserRepository;
use App\Security\ApiAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ItemListController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/lists", methods={"POST"}, name="api_lists_create")
     */
    public function listCreateAction(Request $request)
    {
        $apiToken = $request->headers->get(ApiAuthenticator::X_API_KEY);

        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByApiToken($apiToken);
        if (!$user)
            throw new JsonHttpException(400, JsonHttpException::AUTH_ERROR);

        /* @var ItemList $itemList */
        $itemList = $this->serializer->deserialize($request->getContent(), ItemList::class, 'json');
        $itemList->setUser($user);

        $errors = $this->validator->validate($itemList);
        $errors->addAll($this->validator->validate($itemList->getLabels()));
        if (count($errors))
            throw new JsonHttpException(400, $errors->get(0)->getMessage());

        $this->getDoctrine()->getManager()->persist($itemList);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($itemList);
    }

    /**
     * @Route("/api/lists", methods={"GET"}, name="api_lists_show")
     */
    public function listsShowAction(Request $request)
    {

        $query = $this->getDoctrine()
            ->getRepository(Article::class)
            ->createQueryBuilder('article')
            ->where('article.isApproved = true')
            ->andWhere('article.isDeleted = false')
            ->orderBy('article.id', 'DESC')
            ->getQuery();
        $articles = $paginator->paginate(
            $query,
            $request->query->getInt('page', $page),
            5
        );

        return $this->json($articles);

        return ($this->json($request->headers->get('x-api-key')));

        return ($this->json($request->headers->get('x-api-key')));
    }
}
