<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserGamePreferences
 *
 * @ORM\Table(name="user_game_preferences", indexes={@ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="game_id", columns={"game_id"})})
 * @ORM\Entity
 */
class UserGamePreferences
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
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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
