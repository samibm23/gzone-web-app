<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Acme\CascadeBundle\Entity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Stores
 *
 * @ORM\Table(name="stores", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})}, indexes={@ORM\Index(name="game_id", columns={"game_id"}), @ORM\Index(name="owner_id", columns={"owner_id"})})
 * @ORM\Entity
 */
class Stores
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Le Champ Titre est obligatoire")
     * @Groups("post:read")
     */
    private $name;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * })
     * @Groups("post:read")
     */
    private $owner;

    /**
     * @var \Games
     *
     * @ORM\ManyToOne(targetEntity="Games")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     * })
     * @Groups("post:read")
     */
    private $game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOwner(): ?Users
    {
        return $this->owner;
    }

    public function setOwner(?Users $owner): self
    {
        $this->owner = $owner;

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
        return $this->name;
    }

    public function hash()
    {
        return date();
    }

    public function equals($obj): bool
    {
        return $this->hash() === $obj->hash();
    }

}
