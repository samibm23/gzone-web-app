<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $admin_id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $game_id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private $description;

    #[ORM\Column(type: 'integer')]
    private $required_teams;

    #[ORM\Column(type: 'integer')]
    private $team_size;

    #[ORM\Column(type: 'boolean')]
    private $requestable;

    #[ORM\Column(type: 'boolean')]
    private $approved;

    #[ORM\Column(type: 'date')]
    private $create_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdminId(): ?int
    {
        return $this->admin_id;
    }

    public function setAdminId(int $admin_id): self
    {
        $this->admin_id = $admin_id;

        return $this;
    }

    public function getGameId(): ?int
    {
        return $this->game_id;
    }

    public function setGameId(?int $game_id): self
    {
        $this->game_id = $game_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
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
        return $this->required_teams;
    }

    public function setRequiredTeams(int $required_teams): self
    {
        $this->required_teams = $required_teams;

        return $this;
    }

    public function getTeamSize(): ?int
    {
        return $this->team_size;
    }

    public function setTeamSize(int $team_size): self
    {
        $this->team_size = $team_size;

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
        return $this->create_date;
    }

    public function setCreateDate(\DateTimeInterface $create_date): self
    {
        $this->create_date = $create_date;

        return $this;
    }
}
