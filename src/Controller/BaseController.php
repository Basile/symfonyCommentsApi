<?php
namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class BaseController
 * @package App\Controller
 */
abstract class BaseController extends FOSRestController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return \FOS\RestBundle\View\View
     */
    protected function sendOptionsResponse()
    {
        return $this->view(null, JsonResponse::HTTP_OK, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, PUT, DELETE, OPTIONS',
        ]);
    }

    protected function sendResponse($data, $status = JsonResponse::HTTP_OK)
    {
        return $this->view($data, $status, ['Access-Control-Allow-Origin' => '*']);
    }

    /**
     * @param int $id
     * @return object|null
     */
    protected function getCommentById(int $id)
    {
        return $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id);
    }
}
