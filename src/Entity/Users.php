<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class Users implements UserInterface
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
     *  @Assert\NotBlank(message="Le Champ Titre est obligatoire")
      * @Assert\Length(
     *      min = 8,
     *      max = 8,
     *      minMessage="le numero de telephone doit etre 8 chiffres",
     *      maxMessage="le numero de telephone doit etre 8 chiffres"
     * )
     */
    private $phoneNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true, options={"default"="NULL"})
     * * @Assert\NotBlank(message="le champs ne doit pas etre vide")
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
      * @Assert\NotBlank(message="le champs ne doit pas etre vide")
      * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="The username must be at least 3 characters long",
     *     maxMessage="The username cannot be longer than 50 characters"
     * )
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="The password must be at least 3 characters long",
     *     maxMessage="The password cannot be longer than 50 characters"
     * )
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_url", type="string", length=500, nullable=true, options={"default"="NULL"})
     */
    private $photoUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="full_name", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $fullName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bio", type="string", length=500, nullable=true, options={"default"="NULL"})
     */
    private $bio;

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
    private $joinDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="invitable", type="boolean", nullable=false, options={"default"="1"})
     */
    private $invitable = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="role", type="string", length=20, nullable=true, options={"default"="ROLE_USER"})
     */
    private $role;
    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $activation_token;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $reset_token;

    /**
     * @ORM\Column(type="string")
     */
    private $disable_token;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Length(min="8" , minMessage="Phone number must contain exactly 8 numbers")
     * @Assert\Length(max="8" , maxMessage="Phone number must contain exactly 8 numbers")
     */
    private $verificationCode;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): self
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getJoinDate(): ?\DateTimeInterface
    {
        return $this->joinDate;
    }

    public function setJoinDate(\DateTimeInterface $joinDate): self
    {
        $this->joinDate = $joinDate;

        return $this;
    }

    public function getInvitable(): ?bool
    {
        return $this->invitable;
    }

    public function setInvitable(bool $invitable): self
    {
        $this->invitable = $invitable;

        return $this;
    }
    public function getRoles(): array
    {
        $role = $this->role;
        if (strcmp($role, "ROLE_USER")==0){
            return $roles = array("ROLE_USER");
        }else{
            return $roles = array("ROLE_ADMIN");
        }
        
    }
    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    public function getUserIdentifier(): ?string
    {
        return $this->id;
    }
    
    public function __toString(): string
    {
        return $this->username;
    }
    public function serialize()
    {
        return serialize($this->id);
    }

    public function unserialize($data)
    {
        $this->id = unserialize($data);
    }


    /**
     * Get the value of isVerified
     */ 
    public function getIsVerified()
    {
        return $this->isVerified;
    }

    /**
     * Set the value of isVerified
     *
     * @return  self
     */ 
    public function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Get the value of activation_token
     */ 
    public function getActivationToken()
    {
        return $this->activation_token;
    }

    /**
     * Set the value of activation_token
     *
     * @return  self
     */ 
    public function setActivationToken($activation_token)
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    /**
     * Get the value of reset_token
     */ 
    public function getResetToken()
    {
        return $this->reset_token;
    }

    /**
     * Set the value of reset_token
     *
     * @return  self
     */ 
    public function setResetToken($reset_token)
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    /**
     * Get the value of disable_token
     */ 
    public function getDisableToken()
    {
        return $this->disable_token;
    }

    /**
     * Set the value of disable_token
     *
     * @return  self
     */ 
    public function setDisableToken($disable_token)
    {
        $this->disable_token = $disable_token;

        return $this;
    }

    /**
     * Get the value of verificationCode
     */ 
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }

    /**
     * Set the value of verificationCode
     *
     * @return  self
     */ 
    public function setVerificationCode($verificationCode)
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }
}
