<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Please enter a title")
     * @Assert\Type("string",message="title must be a string")
     * @Assert\Regex("/\S+/",message="add some characters")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @Assert\NotBlank(message="Please enter a description")
     * @Assert\Type("string",message="title must be a string")
     * @Assert\Regex("/^\w+/")
     * @Assert\Regex("/\S+/")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
