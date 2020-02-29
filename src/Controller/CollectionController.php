<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Collection;
use App\Controller\ApiController;
use App\Repository\CollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CollectionController extends ApiController
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
     * Get collection by ID.
     * 
     * @Route("/collection/{id}", name="collection.show")
     * 
     * @param CollectionRepository $collectionRepository
     * 
     * @param int $id
     * 
     * @return Response
     */
    public function show($id, CollectionRepository $collectionRepository)
    {
        $collection = $collectionRepository->findCollectionById($id);

        return $this->respondWithSuccess($collection[0], ['Content-Type' => 'application/json']);
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
        $fileName = 'cover_default.jpg';
        
        if (empty($title) || empty($description)) return $this->respondValidationError();

        if ($file) {
            if (in_array(strtoupper($file->guessExtension()), $fileExtensionValid)) {
                $fileName = md5(\uniqid()) . '-' . $user->getId() . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_directory'), $fileName);
            } else {
                return $this->respondValidationError();
            }
        }

        $collection = new Collection();
        $collection->setTitle($title);
        $collection->setDescription($description);
        $collection->setCoverPhoto($fileName);
        $collection->setUser($user);
        $this->em->persist($collection);
        $this->em->flush();

        return $this->respondWithSuccess("Collection successfully created.");
    }
}
