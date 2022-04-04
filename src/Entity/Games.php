<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Games
 *
 * @ORM\Table(name="games", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class Games
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_url", type="string", length=500, nullable=true, options={"default"="NULL"})
     */
    private $photoUrl = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true, options={"default"="NULL"})
     */
    private $description = 'NULL';


}
