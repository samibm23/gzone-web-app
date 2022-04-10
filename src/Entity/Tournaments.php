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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRequiredTeams(): ?int
    {
        return $this->requiredTeams;
    }

    public function setRequiredTeams(int $requiredTeams): self
    {
        $this->requiredTeams = $requiredTeams;

        return $this;
    }

    public function getTeamSize(): ?int
    {
        return $this->teamSize;
    }

    public function setTeamSize(int $teamSize): self
    {
        $this->teamSize = $teamSize;

        return $this;
    }

    public function getRequestable(): ?bool
    {
        return $this->requestable;
    }

    public function setRequestable(bool $requestable): self
    {
        $this->requestable = $requestable;

        return $this;
    }

    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getAdmin(): ?Users
    {
        return $this->admin;
    }

    public function setAdmin(?Users $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getGame(): ?Games
    {
        return $this->game;
    }

    public function setGame(?Games $game): self
    {
        $this->game = $game;

        return $this;
    }


}
