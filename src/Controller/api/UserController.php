<?php

namespace App\Controller\api;

use App\Entity\User;
use App\Services\ValidateService;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
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
     * @Route("/api/users", methods={"POST"}, name="api_users_registration")
     */
    public function registrationAction(Request $request)
    {
        /* @var User $user */
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $this->validateService->validate($user);
        $user->setApiToken(Uuid::uuid4());
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->json($user);
    }

    /**
     * @Route("/api/users", methods={"GET"}, name="api_users_list")
     */
    public function listAction()
    {
        return null;
    }

    /**
     * @Route("/api/users/login", methods={"POST"}, name="api_users_login")
     */
    public function loginAction()
    {
        return null;
    }
}
