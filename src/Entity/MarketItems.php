<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Acme\CascadeBundle\Entity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * MarketItems
 *
 * @ORM\Table(name="market_items", indexes={@ORM\Index(name="store_id", columns={"store_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\MarketItemsRepository")
 */
class MarketItems
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Le Champ Titre est obligatoire")
     *  @Groups("post:read")
     * 
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true, options={"default"="NULL"})
     * @Assert\NotBlank(message="Le Champ Titre est obligatoire")
     *  @Groups("post:read")
     */
    private $description;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="sold", type="boolean", nullable=true)
     * @Groups("post:read")
     */
    private $sold = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="post_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     * @Assert\GreaterThanOrEqual("today")
     * @Groups("post:read")
     */
    private $postDate;

    /**
     * @var \Stores
     * 
     * @ORM\ManyToOne(targetEntity="Stores")
     
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="store_id", referencedColumnName="id")
     * })
     * @Groups("post:read")
     */
    private $store;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSold(): ?bool
    {
        return $this->sold;
    }

    public function setSold(?bool $sold): self
    {
        $this->sold = $sold;

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

    public function getStore(): ?Stores
    {
        return $this->store;
    }

    public function setStore(?Stores $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }

}
