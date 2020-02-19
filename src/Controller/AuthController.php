<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
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
     * @Route("/api/register", name="api.auth.register", methods={"POST"})
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
            return new JsonResponse([
                'status' => 422,
                'success' => 'Invalid informations.',
            ], 422);
        }

        if ($userRepository->isExist($email, $username)) {
            return new JsonResponse([
                'status' => 422,
                'success' => 'Email address or username already exists.'
            ], 422);
        }

        $user = new User($request);
        $user->setUsername($username);
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword($encoder->encodePassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse([
            'status' => 200,
            'success' => "User successfully created.",
        ], 200);
    }
}
