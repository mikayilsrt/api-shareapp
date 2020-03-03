<?php

namespace App\Controller;

use App\Controller\ApiController;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends ApiController
{

    /**
     * @Route("/user/{id}", name="api.user.show")
     * 
     * @param int $id
     * 
     * @param UserRepository $userRepository
     * 
     * @return Response
     */
    public function show($id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);

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

}