<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Badges
 *
 * @ORM\Table(name="badges", uniqueConstraints={@ORM\UniqueConstraint(name="title", columns={"title"})}, indexes={@ORM\Index(name="game_id", columns={"game_id"})})
 * @ORM\Entity
 */
class Badges
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

     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     * @Assert\NotBlank
    @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Your description must be at least {{ 2 }} characters long",
     *      maxMessage = "Your description cannot be longer than {{ 50 }} characters"
     * )
     *      @Assert\Unique

     */
    private $title;

    /**
     * @var \Games
     *
     * @ORM\ManyToOne(targetEntity="Games", cascade={"remove"} )
     *
     *   @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     *
     */
    private $game;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGame(): ?Games
    {
        return $this->game;
    }

    public function setGame(?Games $game): self
    {
        $this->game = $game;

        return $this;
    }
    public function __toString(): string
    {
        return $this->title;
    }

}
