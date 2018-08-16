<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $parentId;

    /**
     * @ORM\Column(type="string", length=1000)
     * @Assert\NotBlank(message="Comment cannot be empty")
     * @Assert\Length(max=1000, maxMessage="Comment length can be max 1000 characters")
     */
    private $text;

    /**
     * @ORM\Column(type="integer")
     */
    private $createTime;

    public function getId() : ?int {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
        return $this;
    }

    public function getParentId() : ?int {
        return $this->parentId;
    }

    public function setParentId(int $parentId) {
        $this->parentId = $parentId;
        return $this;
    }

    public function getText() : ?string {
        return $this->text;
    }

    public function setText(string $text) {
        $this->text = $text;
        return $this;
    }

    public function getCreateTime() : ?int {
        return $this->createTime;
    }

    public function setCreateTime(int $createTime) {
        $this->createTime = $createTime;
        return $this;
    }
}
