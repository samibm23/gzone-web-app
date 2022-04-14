<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Acme\CascadeBundle\Entity;

/**
 * Comments
 *
 * @ORM\Table(name="comments", indexes={@ORM\Index(name="post_id", columns={"post_id"}), @ORM\Index(name="commenter_id", columns={"commenter_id"})})
 * @ORM\Entity
 */
class Comments
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="comment_body", type="string", length=1000, nullable=false)
     * @Assert\NotBlank(message="Le Champ Titre est obligatoire")
     */
    private $commentBody;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="comment_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     * @Assert\GreaterThanOrEqual("today")
     * 
     */
    private $commentDate;

    /**
     * @var \Posts
     *
     * @ORM\ManyToOne(targetEntity="Posts" , cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     * })
     * 
     */
    private $post;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="commenter_id", referencedColumnName="id")
     * })
     */
    private $commenter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentBody(): ?string
    {
        return $this->commentBody;
    }

    public function setCommentBody(string $commentBody): self
    {
        $this->commentBody = $commentBody;

        return $this;
    }

    public function getCommentDate(): ?\DateTimeInterface
    {
        return $this->commentDate;
    }

    public function setCommentDate(\DateTimeInterface $commentDate): self
    {
        $this->commentDate = $commentDate;

        return $this;
    }

    public function getPost(): ?Posts
    {
        return $this->post;
    }

    public function setPost(?Posts $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getCommenter(): ?Users
    {
        return $this->commenter;
    }

    public function setCommenter(?Users $commenter): self
    {
        $this->commenter = $commenter;

        return $this;
    }
    public function __toString(): string
    {
        return $this->getCommentBody;
    }



}
