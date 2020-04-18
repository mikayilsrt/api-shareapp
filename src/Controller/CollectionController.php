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

/**
 * @Route("/api/collection", name="api.collection")
 */
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
     * @Route("/", name="collection.index")
     * 
     * @param CollectionRepository $collectionRepository
     * 
     * @return Response
     */
    public function index(CollectionRepository $collectionRepository)
    {
        $collections = $collectionRepository->findAllCollections();
        
        return $this->respondWithSuccess($collections);
    }

    /**
     * Get collection by ID.
     * 
     * @Route("/{id}", name="collection.show", methods={"GET"})
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
     * @Route("/create", name="api.collection.create", methods={"POST"})
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function create(Request $request)
    {
        if (!$this->security->getUser()) return $this->respondWithErrors("User is not authenticate.");

        $requestData = json_decode($request->getContent());
        $user = $this->security->getUser();
        $title = $requestData->title;
        $description = $requestData->description;
        $file = $request->files->get('collection_cover');
        $fileName = 'cover_default.jpg';
        
        if (empty($title) || empty($description)) return $this->respondValidationError();

        if ($file) {
            if ($newFileName = $this->uploadImage($file, $user, "collection_upload_directory")) {
                $fileName = $newFileName;
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

    /**
     * @Route("/update/{id}", name="api.collection.update", methods={"POST"})
     * 
     * @param int $id
     * 
     * @param Request $request
     * 
     * @param CollectionRepository $collectionRepository
     * 
     * @return Response
     */
    public function update($id, Request $request, CollectionRepository $collectionRepository)
    {
        $collection = $collectionRepository->find($id);

        $title = $request->get('title');
        $description = $request->get('description');
        $file = $request->files->get('collection_cover');

        if (!$collection || !$this->security->getUser() || $collection->getUser() != $this->security->getUser())
            return $this->respondWithErrors("Collection not found or Action invalid");

        if (empty($title) && empty($description))
            return $this->respondValidationError();
        
        if ($file) {
            if ($fileName = $this->uploadImage($file, $this->security->getUser(), "collection_upload_directory")) {
                $collection->setCoverPhoto($fileName);
            } else {
                return $this->respondValidationError();
            }
        }
        
        $collection->setTitle($title);
        $collection->setDescription($description);
        $this->em->flush();
        
        return $this->respondWithSuccess("Collection successfully updated.");
    }

    /**
     * @Route("/delete/{id}", name="api.collection.delete", methods={"DELETE"})
     * 
     * @param int $id
     * 
     * @param CollectionRepository $collectionRepository
     * 
     * @return Response
     */
    public function delete($id, CollectionRepository $collectionRepository)
    {
        $collection = $collectionRepository->find($id);

        if (!$collection || !$this->security->getUser() || $collection->getUser() != $this->security->getUser())
            return $this->respondWithErrors("Collection not found or Action invalid.");

        $this->em->remove($collection);
        $this->em->flush(); 

        return $this->respondWithSuccess("Collection successfully deleted.");
    }
}
