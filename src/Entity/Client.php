<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use JMS\Serializer\Annotation\Expose;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @ExclusionPolicy("all")
 *  
 */
class Client implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80, unique=true)
     * @Expose
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     * @Expose
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="client")
     * 
     */
    private $User;


    public static function create($name, $email ) {
        $client = new self();
        $client->name = $name;
        $client->email = $email;
        
        return $client;
    }
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->User = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
    public function getRoles()
    {
        return [];
    }
    public function getUsername()
    {
        return $this->name;
    }
    public function getSalt()
    {
        
    }
    public function eraseCredentials()
    {
        
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->User;
    }

    public function addUser(User $user): self
    {
        if (!$this->User->contains($user)) {
            $this->User[] = $user;
            $user->setClient($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->User->contains($user)) {
            $this->User->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getClient() === $this) {
                $user->setClient(null);
            }
        }

        return $this;
    }
}
