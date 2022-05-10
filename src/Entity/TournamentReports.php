<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TournamentReports
 *
 * @ORM\Table(name="tournament_reports", indexes={@ORM\Index(name="tournament_id", columns={"tournament_id"}), @ORM\Index(name="report_id", columns={"report_id"})})
 * @ORM\Entity
 */
class TournamentReports
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
     * @var \Tournaments
     *
     * @ORM\ManyToOne(targetEntity="Tournaments" , cascade={"remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tournament_id", referencedColumnName="id",onDelete="CASCADE")
     * })
     */
    private $tournament;

    /**
     * @var \Reports
     *
     * @ORM\ManyToOne(targetEntity="Reports")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     * })
     */
    private $report;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReport(): ?Reports
    {
        return $this->report;
    }

    public function setReport(?Reports $report): self
    {
        $this->report = $report;

        return $this;
    }

    public function __toString(): string
    {
        return $this->report;
    }

}
