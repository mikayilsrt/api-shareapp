<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{

    /**
     * @var integer HTTP status code
     */
    protected $statusCode = 200;

    /**
     * Gets the value of status code.
     * 
     * @return Int
     */
    public function getStatusCode(): ?Int
    {
        return $this->statusCode;
    }

    /**
     * Sets the value of status code.
     * 
     * @param Int $statusCode
     * 
     * @return self
     */
    protected function setStatusCode(Int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Return a new JsonResponse
     * 
     * @param array $data
     * 
     * @param array $header
     * 
     * @return JsonResponse
     */
    public function response($data, $header = [])
    {
        return new JsonResponse($data, $this->getStatusCode(), $header);
    }

    /**
     * Sets an errors message and return JSON response.
     * 
     * @param String $errors
     * 
     * @param array $header
     * 
     * @return JsonResponse
     */
    public function respondWithErrors($errors, $header = [])
    {
        $data = [
            'status' => $this->getStatusCode(),
            'errors' => $errors,
        ];

        return new JsonResponse($data, $this->getStatusCode(), $header);
    }

    /**
     * Sets an message and return JSON response.
     * 
     * @param String $success
     * 
     * @param array $header
     * 
     * @return JsonResponse
     */
    public function respondWithSuccess($success, $header = [])
    {
        $data = [
            'status' => $this->getStatusCode(),
            'success' => $success
        ];

        return new JsonResponse($data, $this->getStatusCode(), $header);
    }

    /**
     * Return a 201 created.
     * 
     * @param array $data
     * 
     * @return JsonResponse
     */
    public function responseCreated($data = [])
    {
        return $this->setStatusCode(201)->response($data);
    }

    /**
     * Return a 422 Unprocessable Entity.
     * 
     * @param String $message
     * 
     * @return JsonResponse
     */
    public function respondValidationError($message = 'Validation errors')
    {
        return $this->setStatusCode(422)->respondWithErrors($message);
    }

}
