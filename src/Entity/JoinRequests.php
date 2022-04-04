<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JoinRequests
 *
 * @ORM\Table(name="join_requests", indexes={@ORM\Index(name="tournament_id", columns={"tournament_id"}), @ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="team_id", columns={"team_id"})})
 * @ORM\Entity
 */
class JoinRequests
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
     * @var string|null
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $message = 'NULL';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="request_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $requestDate = 'current_timestamp()';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="accepted", type="boolean", nullable=true, options={"default"="NULL"})
     */
    private $accepted = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="response_date", type="datetime", nullable=true, options={"default"="NULL"})
     */
    private $responseDate = 'NULL';

    /**
     * @var bool
     *
     * @ORM\Column(name="invitation", type="boolean", nullable=false)
     */
    private $invitation = '0';

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
     * @var \Tournaments
     *
     * @ORM\ManyToOne(targetEntity="Tournaments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
     * })
     */
    private $tournament;

    /**
     * @var \Teams
     *
     * @ORM\ManyToOne(targetEntity="Teams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     * })
     */
    private $team;


}
