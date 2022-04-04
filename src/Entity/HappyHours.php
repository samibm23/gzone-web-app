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


}
