<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * })
     */
    private $owner;

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
