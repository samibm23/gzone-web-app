<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BadgeShips
 *
 * @ORM\Table(name="badge_ships", indexes={@ORM\Index(name="badge_id", columns={"badge_id"}), @ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class BadgeShips
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
     * @var \Badges
     *
     * @ORM\ManyToOne(targetEntity="Badges")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="badge_id", referencedColumnName="id")
     * })
     */
    private $badge;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;


}
