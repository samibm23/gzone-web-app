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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(?int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getTournament(): ?Tournaments
    {
        return $this->tournament;
    }

    public function setTournament(?Tournaments $tournament): self
    {
        $this->tournament = $tournament;

        return $this;
    }

    public function getTeam2(): ?Teams
    {
        return $this->team2;
    }

    public function setTeam2(?Teams $team2): self
    {
        $this->team2 = $team2;

        return $this;
    }

    public function getTeam1(): ?Teams
    {
        return $this->team1;
    }

    public function setTeam1(?Teams $team1): self
    {
        $this->team1 = $team1;

        return $this;
    }

    public function getWinnerTeam(): ?Teams
    {
        return $this->winnerTeam;
    }

    public function setWinnerTeam(?Teams $winnerTeam): self
    {
        $this->winnerTeam = $winnerTeam;

        return $this;
    }

    public function __toString(): string
    {
        return $this->team1 . " vs " . $this->team2;
    }

}
