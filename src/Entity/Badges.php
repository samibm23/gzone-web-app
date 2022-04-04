<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     *
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    private $title;

    /**
     * @var \Games
     *
     * @ORM\ManyToOne(targetEntity="Games")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     * })
     */
    private $game;


}
