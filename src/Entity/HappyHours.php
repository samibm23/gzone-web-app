<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use JsonSerializable;

/**
 * HappyHours
 *
 * @ORM\Table(name="happy_hours", indexes={@ORM\Index(name="badge_id", columns={"badge_id"})})
 * @ORM\Entity
 */
class HappyHours implements JsonSerializable
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
     * @var \DateTime
      *
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
      * @Assert\GreaterThanOrEqual("today")
      * @Groups("post:read")

     */
    private $startDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="datetime", nullable=false)
     * @Assert\GreaterThanOrEqual(propertyPath="startDate", message="the End Date should be greater than Start date")
     * @Groups("post:read")
     *

     */
    private $endDate;

    /**
     * @var \Badges
     *
     * @ORM\ManyToOne(targetEntity="Badges", cascade={"remove"})
     *
     *   @ORM\JoinColumn(name="badge_id", referencedColumnName="id")
     * @Groups("post:read")

     */
    private $badge;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }



    public function getBadge(): ?Badges
    {
        return $this->badge;
    }

    public function setBadge(?Badges $badge): self
    {
        $this->badge = $badge;

        return $this;
    }
    // c'est pas sur 
    public function __toString(): string
    {
        return $this->badge;
    }


    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id,
            'badge' => $this->badge,
            'startDate' => $this->startDate->format("d-m-Y"),
            'endDate' => $this->endDate->format("d-m-Y")

        );
    }

    public function setUp($badge, $startDate, $endDate)
    {
        $this->badge = $badge;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

    }
}
