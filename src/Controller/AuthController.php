<?php

namespace App\Controller;

use App\Entity\User;
use App\Controller\ApiController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends ApiController
{

    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Register a new user.
     * 
     * @Route("/register", name="auth.register", methods={"POST"})
     * 
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, UserRepository $userRepository): Response
    {
        $name = $request->get('username');
        $username = str_replace(' ', '', $request->get('username'));
        $email = $request->get('mail');
        $password = $request->get('password');

        if (empty($name) || empty($username) || empty($email) || empty($password)) {
            return $this->respondValidationError();
        }

        if ($userRepository->isExist($email, $username)) {
            return $this->respondValidationError();
        }

        $user = new User($request);
        $user->setUsername($username);
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword($encoder->encodePassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();

        return $this->respondWithSuccess('User successfully created.');
    }
}
