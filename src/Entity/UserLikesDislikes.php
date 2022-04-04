<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserLikesDislikes
 *
 * @ORM\Table(name="user_likes_dislikes", indexes={@ORM\Index(name="comment_id", columns={"comment_id"}), @ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="store_id", columns={"store_id"}), @ORM\Index(name="post_id", columns={"post_id"})})
 * @ORM\Entity
 */
class UserLikesDislikes
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
     * @var bool
     *
     * @ORM\Column(name="like", type="boolean", nullable=false)
     */
    private $like;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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
     * @var \Stores
     *
     * @ORM\ManyToOne(targetEntity="Stores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="store_id", referencedColumnName="id")
     * })
     */
    private $store;

    /**
     * @var \Comments
     *
     * @ORM\ManyToOne(targetEntity="Comments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
     * })
     */
    private $comment;


}
