<?php

namespace App\Controller\api;

use App\Entity\User;
use App\Exception\JsonHttpException;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
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
     * @Route("/api/user/registration", methods={"POST"}, name="api_user_registration")
     */
    public function registrationAction(Request $request)
    {
        /* @var User $user */
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setApiToken(Uuid::uuid4());

        $errors = $this->validator->validate($user);
        if (count($errors))
            throw new JsonHttpException(400, $errors->get(0)->getMessage());

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($user);
    }

    /**
     * @Route("/api/user/login", methods={"POST"}, name="api_user_login")
     */
    public function loginAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        /* @var User $user */
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $plainPassword = $user->getPlainPassword();

        $user = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($user->getEmail());
        if (!$user)
            throw new JsonHttpException(400, JsonHttpException::AUTH_ERROR);

        if($passwordEncoder->isPasswordValid($user,$plainPassword)){
            $user->setApiToken(Uuid::uuid4());
            $this->getDoctrine()->getManager()->flush();

            return ($this->json($user));
        };

        throw new JsonHttpException(400, JsonHttpException::AUTH_ERROR);
    }
}
