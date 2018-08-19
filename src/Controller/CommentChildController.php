<?php
namespace App\Controller;


use App\Entity\Comment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CommentChildController
 * @package App\Controller
 *
 * @Route("/comment/{parentId}/comments")
 */
class CommentChildController extends BaseController
{
    /**
     * @Route(methods={"GET"})
     * @param int $parentId
     * @return object
     */
    public function getAction(int $parentId)
    {
        if (($parentId !== 0) && !$this->getCommentById($parentId)) {
            return $this->sendResponse([
                'message' => 'No comment found for id ' . $parentId,
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['parentId' => $parentId], ['id' => 'ASC']);

        return $this->sendResponse($comments);
    }

    /**
     * @Route(methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param int $parentId
     * @return object
     */
    public function postAction(Request $request, ValidatorInterface $validator, int $parentId)
    {
        if (($parentId !== 0) && !$this->getCommentById($parentId)) {
            return $this->sendResponse([
                'message' => 'No comment found for id ' . $id,
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return $this->sendResponse([
                'message' => 'Malformed request body',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $comment = new Comment();
        $comment->setParentId($parentId)
            ->setText($data['text'] ?? null)
            ->setCreateTime(time());

        $errors = $validator->validate($comment);
        if (count($errors)) {
            $errorMessages = [];
            foreach ($errors as $e) {
                $errorMessages[$e->getPropertyPath()] = $e->getMessage();
            }
            return $this->sendResponse([
                'message' => 'There are validation errors',
                'data' => $errorMessages,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->sendResponse($comment,JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route(methods={"OPTIONS"})
     * @return \FOS\RestBundle\View\View
     */
    public function optionsAction()
    {
        return $this->sendOptionsResponse();
    }
}
