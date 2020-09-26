<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

use App\Repository\MobileRepository;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * 
 * @ORM\Entity(repositoryClass=MobileRepository::class)
 * @ExclusionPolicy("all")
 * 
 */
class Mobile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     * @Expose
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Expose
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * @Expose
     */
    private $price;


    public static function create($name, $description, $price)
    {
        $mobile = new self();
        $mobile->name = $name;
        $mobile->description = $description;
        $mobile->price = $price;
        return $mobile;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
