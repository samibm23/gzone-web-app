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
     * @ORM\ManyToOne(targetEntity="MarketItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="market_item_id", referencedColumnName="id")
     * })
     */
    private $marketItem;

    /**
     * @var \Reports
     *
     * @ORM\ManyToOne(targetEntity="Reports")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     * })
     */
    private $report;


}
