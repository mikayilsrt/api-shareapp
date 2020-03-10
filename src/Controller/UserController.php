<?php

namespace App\Controller;

use App\Controller\ApiController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api/user", name="api.user")
 */
class UserController extends ApiController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @Route("/{id}", name="api.user.show", methods={"GET"})
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
    
    /**
     * @Route("/update/{id}", name="api.user.update", methods={"POST"})
     * 
     * @param int $id
     * 
     * @param Request $request
     * 
     * @param UserRepository $userRepository
     * 
     * @return Response
     */
    public function update($id, Request $request, UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);

        $name = $request->get('name');
        $username = str_replace(' ', '', $request->get('username'));
        $email = $request->get('email');
        $password = $request->get('password');
        $biographie = $request->get('biographie');
        $portfolioUrl = $request->get('portfolio_url');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $file = $request->files->get('profile_image');

        if (!$user || !$this->security->getUser() || $this->security->getUser() != $user)
            return $this->respondWithErrors("User not found or Action invalid.");

        if (empty($name) || empty($username) || empty($email) || empty($password))
            return $this->respondValidationError();

        if ($file) {
            if ($fileName = $this->uploadImage($file, $user, "profile_upload_directory")) {
                $user->setProfileImage($fileName);
            } else {
                return $this->respondValidationError();
            }
        }

        $user->setName($name);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setBiographie($biographie);
        $user->setPortfolioUrl($portfolioUrl);
        $user->setLatitude($latitude);
        $user->setLongitude($longitude);
        $this->em->flush();
        
        return $this->respondWithSuccess("User successfully updated.");
    }

}