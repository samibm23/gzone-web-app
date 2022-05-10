<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StoreReports
 *
 * @ORM\Table(name="store_reports", indexes={@ORM\Index(name="store_id", columns={"store_id"}), @ORM\Index(name="report_id", columns={"report_id"})})
 * @ORM\Entity
 */
class StoreReports
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
     * @var \Stores
     *
     * @ORM\ManyToOne(targetEntity="Stores", cascade={"remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="store_id", referencedColumnName="id",onDelete="CASCADE")
     * })
     */
    private $store;

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

    public function getStore(): ?Stores
    {
        return $this->store;
    }

    public function setStore(?Stores $store): self
    {
        $this->store = $store;

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


}
