<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarketItemReports
 *
 * @ORM\Table(name="market_item_reports", indexes={@ORM\Index(name="market_item_id", columns={"market_item_id"}), @ORM\Index(name="report_id", columns={"report_id"})})
 * @ORM\Entity
 */
class MarketItemReports
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
     * @var \MarketItems
     *
     * @ORM\ManyToOne(targetEntity="MarketItems" , cascade={"remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="market_item_id", referencedColumnName="id" ,onDelete="CASCADE")
     * })
     */
    private $marketItem;

    /**
     * @var \Reports
     *
     * @ORM\ManyToOne(targetEntity="Reports" )
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     * })
     */
    private $report;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarketItem(): ?MarketItems
    {
        return $this->marketItem;
    }

    public function setMarketItem(?MarketItems $marketItem): self
    {
        $this->marketItem = $marketItem;

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
