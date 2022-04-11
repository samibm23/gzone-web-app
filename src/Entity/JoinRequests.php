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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getRequestDate(): ?\DateTimeInterface
    {
        return $this->requestDate;
    }

    public function setRequestDate(\DateTimeInterface $requestDate): self
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    public function getAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(?bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getResponseDate(): ?\DateTimeInterface
    {
        return $this->responseDate;
    }

    public function setResponseDate(?\DateTimeInterface $responseDate): self
    {
        $this->responseDate = $responseDate;

        return $this;
    }

    public function getInvitation(): ?bool
    {
        return $this->invitation;
    }

    public function setInvitation(bool $invitation): self
    {
        $this->invitation = $invitation;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

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

    public function getTeam(): ?Teams
    {
        return $this->team;
    }

    public function setTeam(?Teams $team): self
    {
        $this->team = $team;

        return $this;
    }
    public function __toString(): string
    {
        return $this->message;
    }

}
