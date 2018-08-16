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

        if (!$this->getParentComment($parentId)) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['parentId' => $parentId], ['id' => 'ASC']);

        return $this->sendResponse(['success' => true, 'data' => $comments]);
    }

    /**
     * @Route(methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param int $parentId
     * @return JsonResponse
     */
    public function postAction(Request $request, ValidatorInterface $validator, int $parentId)
    {
        if (!$this->getParentComment($parentId)) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_BAD_REQUEST);
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
            return new JsonResponse([
                'success' => false,
                'data' => $errorMessages,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->sendResponse([
            'success' => true,
            'data' => $comment,
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route(methods={"OPTIONS"})
     * @return \FOS\RestBundle\View\View
     */
    public function optionsAction()
    {
        return $this->sendOptionsResponse();
    }

    /**
     * @param int $parentId
     * @return mixed
     */
    private function getParentComment(int $parentId)
    {
        $repository = $this->getDoctrine()
            ->getRepository(Comment::class);
        return ($parentId === 0) ? true : $repository->find($parentId);
    }
}
