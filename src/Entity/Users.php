<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"}), @ORM\UniqueConstraint(name="phone_number", columns={"phone_number"}), @ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity
 */
class Users
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
     * @ORM\Column(name="phone_number", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $phoneNumber = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $email = 'NULL';

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_url", type="string", length=500, nullable=true, options={"default"="NULL"})
     */
    private $photoUrl = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="full_name", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $fullName = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="bio", type="string", length=500, nullable=true, options={"default"="NULL"})
     */
    private $bio = 'NULL';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="date", nullable=false)
     */
    private $birthDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="join_date", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $joinDate = 'current_timestamp()';

    /**
     * @var bool
     *
     * @ORM\Column(name="invitable", type="boolean", nullable=false, options={"default"="1"})
     */
    private $invitable = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="role", type="string", length=20, nullable=true, options={"default"="NULL"})
     */
    private $role = 'NULL';


}
