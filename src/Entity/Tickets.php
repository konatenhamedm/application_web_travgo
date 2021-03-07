<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TicketsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketsRepository::class)
 * @ApiResource()
 */
class Tickets
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateHeure;

    /**
     * @ORM\ManyToOne(targetEntity=Voyages::class, inversedBy="tickets")
     */
    private $voyages;

    /**
     * @ORM\Column(type="integer")
     */
    private $active;


    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }


    public function __construct()
    {
        $this->dateHeure= new \DateTime('now');
        $this->active = 1;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHeure(): ?\DateTimeInterface
    {
        return $this->dateHeure;
    }

    public function setDateHeure(\DateTimeInterface $dateHeure): self
    {
        $this->dateHeure = $dateHeure;

        return $this;
    }


    public function getVoyages(): ?Voyages
    {
        return $this->voyages;
    }

    public function setVoyages(?Voyages $voyages): self
    {
        $this->voyages = $voyages;

        return $this;
    }
}
