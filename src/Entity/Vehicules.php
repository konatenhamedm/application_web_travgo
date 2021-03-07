<?php

namespace App\Entity;

use App\Repository\VehiculesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=VehiculesRepository::class)
 * @ApiResource()
 */
class Vehicules
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $immatriculation;

    /**
     * @ORM\ManyToOne(targetEntity=TypeVehicule::class, inversedBy="vehicules")
     */
    private $typeVehicule;

    /**
     * @ORM\ManyToOne(targetEntity=Marques::class, inversedBy="vehicules")
     */
    private $marques;

    /**
     * @ORM\OneToMany(targetEntity=Position::class, mappedBy="vehicule")
     */
    private $positions;

    /**
     * @ORM\OneToMany(targetEntity=Pannes::class, mappedBy="vehicule")
     */
    private $pannes;

    /**
     * @ORM\OneToMany(targetEntity=Voyages::class, mappedBy="vehicule")
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
        $this->positions = new ArrayCollection();
        $this->pannes = new ArrayCollection();
        $this->voyages = new ArrayCollection();
        $this->active = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): self
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getTypeVehicule(): ?TypeVehicule
    {
        return $this->typeVehicule;
    }

    public function setTypeVehicule(?TypeVehicule $typeVehicule): self
    {
        $this->typeVehicule = $typeVehicule;

        return $this;
    }

    public function getMarques(): ?Marques
    {
        return $this->marques;
    }

    public function setMarques(?Marques $marques): self
    {
        $this->marques = $marques;

        return $this;
    }

    /**
     * @return Collection|Position[]
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    public function addPosition(Position $position): self
    {
        if (!$this->positions->contains($position)) {
            $this->positions[] = $position;
            $position->setVehicule($this);
        }

        return $this;
    }

    public function removePosition(Position $position): self
    {
        if ($this->positions->removeElement($position)) {
            // set the owning side to null (unless already changed)
            if ($position->getVehicule() === $this) {
                $position->setVehicule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Pannes[]
     */
    public function getPannes(): Collection
    {
        return $this->pannes;
    }

    public function addPanne(Pannes $panne): self
    {
        if (!$this->pannes->contains($panne)) {
            $this->pannes[] = $panne;
            $panne->setVehicule($this);
        }

        return $this;
    }

    public function removePanne(Pannes $panne): self
    {
        if ($this->pannes->removeElement($panne)) {
            // set the owning side to null (unless already changed)
            if ($panne->getVehicule() === $this) {
                $panne->setVehicule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Voyages[]
     */
    public function getVoyages(): Collection
    {
        return $this->voyages;
    }

    public function addVoyage(Voyages $voyage): self
    {
        if (!$this->voyages->contains($voyage)) {
            $this->voyages[] = $voyage;
            $voyage->setVehicule($this);
        }

        return $this;
    }

    public function removeVoyage(Voyages $voyage): self
    {
        if ($this->voyages->removeElement($voyage)) {
            // set the owning side to null (unless already changed)
            if ($voyage->getVehicule() === $this) {
                $voyage->setVehicule(null);
            }
        }

        return $this;
    }


}
