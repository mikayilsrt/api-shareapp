<?php

namespace App\Controller;

use App\Entity\User;
use App\Controller\ApiController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
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

    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * Check if user is authenticated.
     * 
     * @Route("/api/auth_valid", name="api.auth.valid", methods={"GET"})
     * 
     * @return Response
     */
    public function checkAuth(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            $this->setStatusCode(403);
            return $this->respondWithSuccess("Unauthenticated user");
        }
        $this->setStatusCode(200);
        return $this->respondWithSuccess([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'biographie' => $user->getBiographie(),
            'latitude' => $user->getLatitude(),
            'longitude' => $user->getLongitude(),
            'portfolio_url' => $user->getPortfolioUrl(),
            'profile_image' => $user->getProfileImage()
        ]);
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
        $requestData = \json_decode($request->getContent());
        $name = $requestData->username;
        $username = str_replace(' ', '', $requestData->username);
        $email = $requestData->email;
        $password = $requestData->password;

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
