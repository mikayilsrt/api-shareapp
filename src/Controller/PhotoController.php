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
     * @Route("/create", name="api.photo.create", methods={"POST"})
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function create(Request $request, CollectionRepository $collectionRepository)
    {
        if (!$this->security->getUser()) return $this->respondWithErrors("User is not authenticate.");

        $title = $request->get('title');
        $description = $request->get('description');
        $collectionId = $request->get('collection_id');
        $longitude = $request->get('longitude');
        $latitude = $request->get('latitude');
        $file = $request->files->get('image_file');
        
        if (empty($title) || empty($description) || !$file) return $this->respondValidationError();

        $photo = new Photo();
        $photo->setTitle($title);
        $photo->setDescription($description);
        $photo->setCollection($collectionRepository->find($collectionId));
        $photo->setUser($this->security->getUser());
        
        if (!empty($longitude) && !empty($latitude)) {
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

}