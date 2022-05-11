<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use JsonSerializable;

/**
 * Games
 *
 * @ORM\Table(name="games", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class Games implements JsonSerializable
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
     * @Assert\NotBlank
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups("post:read")


     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="photo_url",
     * length=500, nullable=true)
     * @Assert\Image(
     *     minWidth = 200,
     *     maxWidth = 400,
     *     minHeight = 200,
     *     maxHeight = 400

     * )

     */
    private $photo_url	;

    /**
     * @var string|null
     * @Assert\NotBlank
     * @ORM\Column(name="description", type="string", length=500, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 500,
     *      minMessage = "Your description must be at least {{ 2 }} characters long",
     *      maxMessage = "Your description cannot be longer than {{ 500 }} characters"
     * )
     * @Groups("post:read")

     */
     
    private $description;

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

    /**
     * @return string|null
     */
    public function getPhoto_Url(): ?string
    {
        return $this->photo_url;
    }

    /**
     * @param string|null $photo_url
     */
    public function setPhotoUrl(?string $photo_url): void
    {
        $this->photo_url = $photo_url;
    }


    /**
     * @return string|null
     */


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function __toString(): string
    {
        return $this->name;
    }
    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->photo_url,
            'description' => $this->description
        );
    }

    public function setUp($name, $photoUrl, $description)
    {
        $this->name = $name;
        $this->photo_url = $photoUrl;
        $this->description = $description;
    }
}
