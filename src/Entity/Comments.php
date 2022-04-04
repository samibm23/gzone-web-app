<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comments
 *
 * @ORM\Table(name="comments", indexes={@ORM\Index(name="post_id", columns={"post_id"}), @ORM\Index(name="commenter_id", columns={"commenter_id"})})
 * @ORM\Entity
 */
class Comments
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
     * @ORM\Column(name="comment_body", type="string", length=1000, nullable=false)
     */
    private $commentBody;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="comment_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $commentDate = 'current_timestamp()';

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
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="commenter_id", referencedColumnName="id")
     * })
     */
    private $commenter;


}
