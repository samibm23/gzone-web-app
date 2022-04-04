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
     * @ORM\ManyToOne(targetEntity="Stores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="store_id", referencedColumnName="id")
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


}
