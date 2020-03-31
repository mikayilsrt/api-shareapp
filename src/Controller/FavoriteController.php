<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Controller\ApiController;
use App\Repository\UserRepository;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/favorite", name="api.favorite")
 */
class FavoriteController extends ApiController
{

    /**
     * @var ObjectManager
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
     * @Route("/favorite", name="api.favorite.add_remove", methods={"POST"})
     *  
     * @param Request $request
     * 
     * @param PhotoRepository $photoRepository
     * 
     * @param UserRepository $userRepository
     * 
     * @return Response
     */
    public function favorite(Request $request, PhotoRepository $photoRepository, UserRepository $userRepository)
    {
        if (!$this->security->getUser()) return $this->respondWithErrors("User is not authenticate.");

        $photoId = $request->get('photo_id');
        $photo = $photoRepository->find($photoId);
        $user = $userRepository->find($this->security->getUser()->getId());
        
        if (empty($photoId)) return $this->respondValidationError();

        if ($photo == null) return $this->respondWithErrors("Picture not found.");

        $photoAlreadyLiked = $this->em->getRepository(Favorite::class)->findOneBy(array('user' => $user, 'photo' => $photo));

        if ($photoAlreadyLiked) {
            $this->em->remove($photoAlreadyLiked);
            $this->em->flush();
            
            return $this->respondWithSuccess("Post successfully unliked.");
        } else {
            $favorite = new Favorite();
            $favorite->setUser($user);
            $favorite->setPhoto($photo);
            
            $this->em->persist($favorite);
            $this->em->flush();
            
            return $this->respondWithSuccess("Post successfully liked.");
        }
    }
}
