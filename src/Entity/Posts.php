<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Posts
 *
 * @ORM\Table(name="posts", uniqueConstraints={@ORM\UniqueConstraint(name="title", columns={"title"})}, indexes={@ORM\Index(name="poster_id", columns={"poster_id"})})
 * @ORM\Entity
 * 
 */
class Posts
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * Groups("post:read")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="resolved", type="boolean", nullable=false)
     * Groups("post:read")
     */
    private $resolved = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="Le Champ Titre est obligatoire")
     * Groups("post:read")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=2555, nullable=false)
     * @Assert\NotBlank(message="Le Champ Titre est obligatoire")
     * Groups("post:read")
     */
    private $content;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tags", type="string", length=255, nullable=true, options={"default"="NULL"})
     * Groups("post:read")
     */
    private $tags;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="post_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     * @Assert\GreaterThanOrEqual("today")
     * Groups("post:read")
     */
    private $postDate;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="poster_id", referencedColumnName="id")
     * })
     * Groups("post:read")
     * 
     */
    private $poster;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResolved(): ?bool
    {
        return $this->resolved;
    }

    public function setResolved(bool $resolved): self
    {
        $this->resolved = $resolved;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getPostDate(): ?\DateTimeInterface
    {
        return $this->postDate;
    }

    public function setPostDate(\DateTimeInterface $postDate): self
    {
        $this->postDate = $postDate;

        return $this;
    }

    public function getPoster(): ?Users
    {
        return $this->poster;
    }

    public function setPoster(?Users $poster): self
    {
        $this->poster = $poster;

        return $this;
    }
    
    public function __toString(): string
    {
        return $this->title;
    }

}
