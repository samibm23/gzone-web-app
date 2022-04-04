<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reports
 *
 * @ORM\Table(name="reports", uniqueConstraints={@ORM\UniqueConstraint(name="head", columns={"head"})}, indexes={@ORM\Index(name="reporter_id", columns={"reporter_id"})})
 * @ORM\Entity
 */
class Reports
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
     * @ORM\Column(name="subject", type="string", length=20, nullable=true, options={"default"="NULL"})
     */
    private $subject = 'NULL';

    /**
     * @var string
     *
     * @ORM\Column(name="head", type="string", length=255, nullable=false)
     */
    private $head;

    /**
     * @var string|null
     *
     * @ORM\Column(name="body", type="string", length=1000, nullable=true, options={"default"="NULL"})
     */
    private $body = 'NULL';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="report_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $reportDate = 'current_timestamp()';

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reporter_id", referencedColumnName="id")
     * })
     */
    private $reporter;


}
