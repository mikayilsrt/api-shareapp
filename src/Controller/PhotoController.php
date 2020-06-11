<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use App\Repository\CollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/photo", name="api.photo")
 */
class PhotoController extends ApiController
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
     * @Route("/", name="photo.index")
     * 
     * @return Response
     */
    public function index(PhotoRepository $photoRepository)
    {
        $photos = $photoRepository->findAllPhotos();

        return $this->respondWithSuccess($photos);
    }

    /**
     * @Route("/{id}", name="photo.show", methods={"GET"})
     * 
     * @param int $id
     * 
     * @param PhotoRepository $photoRepository
     * 
     * @return Response
     */
    public function show($id, PhotoRepository $photoRepository)
    {
        $photo = $photoRepository->findById($id);

        return $this->respondWithSuccess($photo);
    }

    /**
     * @Route("/create", name="photo.create", methods={"POST"})
     * 
     * @param Request $request
     * 
     * @param CollectionRepository $collectionRepository
     * 
     * @return Response
     */
    public function create(Request $request, CollectionRepository $collectionRepository)
    {
        if (!$this->security->getUser()) return $this->respondWithErrors("User is not authenticate.");

        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $collectionId = $request->request->get('collection_id');
        $longitude = $request->request->get('longitude');
        $latitude = $request->request->get('latitude');
        $file = $request->files->get('image_file');
        
        if (empty($title) || empty($description) || !$file) return $this->respondValidationError();

        $photo = new Photo();
        $photo->setTitle($title);
        $photo->setDescription($description);
        $photo->setCollection($collectionRepository->find($collectionId));
        $photo->setUser($this->security->getUser());
        
        if (!empty($longitude) && !empty($latitude) && $longitude !== "null" && $latitude !== "null") {
            $photo->setLatitude($latitude);
            $photo->setLongitude($longitude);
        }

        if ($fileName = $this->uploadImage($file, $this->security->getUser(), 'photo_upload_directory')) {
            $photo->setImageName($fileName);
        } else {
            $this->respondValidationError();
        }

        $this->em->persist($photo);
        $this->em->flush();

        return $this->respondWithSuccess("Photo successfully created.");
    }

    /**
     * @Route("/delete/{id}", name="api.photo.delete", methods={"DELETE"})
     * 
     * @param int $id
     * 
     * @param PhotoRepository $photoRepository
     * 
     * @return Response
     */
    public function delete($id, PhotoRepository $photoRepository)
    {
        $photo = $photoRepository->find($id);

        if (!$this->security->getUser() || $this->security->getUser()->getId() != $photo->getUser()->getId()) return $this->respondWithErrors("User is not authenticate.");

        $this->em->remove($photo);
        $this->em->flush();

        return $this->respondWithSuccess("Photo successfully deleted.");
    }

}