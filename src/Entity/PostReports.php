<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostReports
 *
 * @ORM\Table(name="post_reports", indexes={@ORM\Index(name="post_id", columns={"post_id"}), @ORM\Index(name="report_id", columns={"report_id"})})
 * @ORM\Entity
 */
class PostReports
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
     * @var \Posts
     *
     * @ORM\ManyToOne(targetEntity="Posts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     * })
     */
    private $post;

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
