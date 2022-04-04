<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tournaments
 *
 * @ORM\Table(name="tournaments", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})}, indexes={@ORM\Index(name="admin_id", columns={"admin_id"}), @ORM\Index(name="game_id", columns={"game_id"})})
 * @ORM\Entity
 */
class Tournaments
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
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true, options={"default"="NULL"})
     */
    private $description = 'NULL';

    /**
     * @var int
     *
     * @ORM\Column(name="required_teams", type="integer", nullable=false)
     */
    private $requiredTeams;

    /**
     * @var int
     *
     * @ORM\Column(name="team_size", type="integer", nullable=false, options={"default"="1"})
     */
    private $teamSize = 1;

    /**
     * @var bool
     *
     * @ORM\Column(name="requestable", type="boolean", nullable=false, options={"default"="1"})
     */
    private $requestable = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="approved", type="boolean", nullable=false)
     */
    private $approved = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $createDate = 'current_timestamp()';

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="admin_id", referencedColumnName="id")
     * })
     */
    private $admin;

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
