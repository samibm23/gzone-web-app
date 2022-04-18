<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * HappyHours
 *
 * @ORM\Table(name="happy_hours", indexes={@ORM\Index(name="badge_id", columns={"badge_id"})})
 * @ORM\Entity
 */
class HappyHours
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
     * @var \DateTime
      *
     * @ORM\Column(name="start_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
      * @Assert\GreaterThanOrEqual("today")

     */
    private $startDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     * @Assert\GreaterThanOrEqual(propertyPath="startDate", message="la date fin doit etre superieurà la date de debut")

     */
    private $endDate;

    /**
     * @var \Badges
     *
     * @ORM\ManyToOne(targetEntity="Badges", cascade={"remove"})
     *
     *   @ORM\JoinColumn(name="badge_id", referencedColumnName="id")
     *
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



}
