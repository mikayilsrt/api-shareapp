<?php

namespace App\Controller;

use App\Controller\ApiController;
use App\Repository\CollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/search", name="api.search")
 */
class SearchController extends ApiController
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
     * @Route("/", name="api.search")
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function search(Request $request, CollectionRepository $collectionRepository)
    {
        return $this->respondWithSuccess($collectionRepository->searchCollections($request->get('args')));
    }
}
