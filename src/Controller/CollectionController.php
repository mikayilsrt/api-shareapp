<?php

namespace App\Controller;

use App\Entity\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CollectionController extends AbstractController
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
     * Create a new collection.
     * 
     * @Route("/api/collection/create", name="api.collection.create", methods={"POST"})
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function create(Request $request)
    {
        $user = $this->security->getUser();
        $title = $request->get('title');
        $description = $request->get('description');
        $file = $request->files->get('collection_cover');
        $fileExtensionValid = array('PNG', 'JPG', 'JPEG');
        
        if (empty($title) || empty($description)) {
            return new JsonResponse([
                'status' => 422,
                'success' => "Invalid informations."
            ]);
        }

        if ($file) {
            if (in_array(strtoupper($file->guessExtension()), $fileExtensionValid)) {
                $fileName = md5(\uniqid()) . '-' . $user->getId() . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_directory'), $fileName);
            } else {
                return new JsonResponse([
                    'status' => 406,
                    'success' => "Invalid File."
                ]);
            }
        } else {
            $fileName = 'cover_default.jpg';
        }

        $collection = new Collection();
        $collection->setTitle($title);
        $collection->setDescription($description);
        $collection->setCoverPhoto($fileName);
        $collection->setUser($user);
        $this->em->persist($collection);
        $this->em->flush();

        return new JsonResponse([
            'status' => 200,
            'success' => "Collection successfully created."
        ]);
    }
}
