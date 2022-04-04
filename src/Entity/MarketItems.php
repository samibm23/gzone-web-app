<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarketItems
 *
 * @ORM\Table(name="market_items", indexes={@ORM\Index(name="store_id", columns={"store_id"})})
 * @ORM\Entity
 */
class MarketItems
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true, options={"default"="NULL"})
     */
    private $description = 'NULL';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="sold", type="boolean", nullable=true)
     */
    private $sold = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="post_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $postDate = 'current_timestamp()';

    /**
     * @var \Stores
     *
     * @ORM\ManyToOne(targetEntity="Stores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="store_id", referencedColumnName="id")
     * })
     */
    private $store;


}
