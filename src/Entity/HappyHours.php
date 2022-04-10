<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=false)
     */
    private $endDate;

    /**
     * @var \Badges
     *
     * @ORM\ManyToOne(targetEntity="Badges")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="badge_id", referencedColumnName="id")
     * })
     */
    private $badge;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
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


}
