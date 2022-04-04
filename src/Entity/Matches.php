<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Matches
 *
 * @ORM\Table(name="matches", indexes={@ORM\Index(name="team2_id", columns={"team2_id"}), @ORM\Index(name="winner_team_id", columns={"winner_team_id"}), @ORM\Index(name="tournament_id", columns={"tournament_id"}), @ORM\Index(name="team1_id", columns={"team1_id"})})
 * @ORM\Entity
 */
class Matches
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
     * @ORM\Column(name="start_time", type="datetime", nullable=false)
     */
    private $startTime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="round", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $round = NULL;

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
     *   @ORM\JoinColumn(name="team2_id", referencedColumnName="id")
     * })
     */
    private $team2;

    /**
     * @var \Teams
     *
     * @ORM\ManyToOne(targetEntity="Teams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team1_id", referencedColumnName="id")
     * })
     */
    private $team1;

    /**
     * @var \Teams
     *
     * @ORM\ManyToOne(targetEntity="Teams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="winner_team_id", referencedColumnName="id")
     * })
     */
    private $winnerTeam;


}
