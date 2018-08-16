<?php
namespace App\Controller;

use App\Entity\Comment;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CommentController
 * @package App\Controller
 *
 * @Annotations\RouteResource(
 *      "Comment",
 *      pluralize=false
 * )
 */
class CommentController extends BaseController implements ClassResourceInterface
{
    /**
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function getAction(int $id)
    {
        $comment = $this->getComment($id);
        if (!$comment) {
            return $this->sendResponse([
                'success' => false,
                'message' => 'No comment found for id ' . $id,
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return $comment;
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param int $id
     * @return object
     */
    public function putAction(Request $request, int $id, ValidatorInterface $validator)
    {
        $comment = $this->getComment($id);
        if (!$comment) {
            return $this->sendResponse([
                'success' => false,
                'message' => 'No comment found for id ' . $id,
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return $this->sendResponse([
                'success' => false,
                'message' => 'Malformed request body',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $comment->setText($data['text'] ?? null);

        $errors = $validator->validate($comment);
        if (count($errors)) {
            $errorMessages = [];
            foreach ($errors as $e) {
                $errorMessages[$e->getPropertyPath()] = $e->getMessage();
            }
            return $this->sendResponse([
                'success' => false,
                'data' => $errorMessages,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->sendResponse(['success' => true]);
    }

    /**
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction($id)
    {
        $comment = $this->getComment($id);
        if (!$comment) {
            return $this->sendResponse([
                'success' => false,
                'message' => 'No comment found for id ' . $id,
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return $this->sendResponse(['success' => true]);
    }

    public function optionsAction($id)
    {
        return $this->sendOptionsResponse();
    }

    private function getComment(int $id)
    {
        return $this->getDoctrine()
            ->getRepository(Comment::class)
            ->find($id);
    }
}
