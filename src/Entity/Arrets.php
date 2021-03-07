<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ArretsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArretsRepository::class)
 * @ApiResource()
 */
class Arrets
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
/*knkcjf*/
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity=Zones::class, inversedBy="arrets")
     */
    private $zone;

    /**
     * @ORM\ManyToOne(targetEntity=Typedestinations::class, inversedBy="arrets")
     */
    private $typedestinations;

    /**
     * @ORM\ManyToMany(targetEntity=Trajets::class, mappedBy="arrets")
     */
    private $trajets;

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
        $this->trajets = new ArrayCollection();
        $this->active =1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getZone(): ?Zones
    {
        return $this->zone;
    }

    public function setZone(?Zones $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getTypedestinations(): ?Typedestinations
    {
        return $this->typedestinations;
    }

    public function setTypedestinations(?Typedestinations $typedestinations): self
    {
        $this->typedestinations = $typedestinations;

        return $this;
    }

    /**
     * @return Collection|Trajets[]
     */
    public function getTrajets(): Collection
    {
        return $this->trajets;
    }

    public function addTrajet(Trajets $trajet): self
    {
        if (!$this->trajets->contains($trajet)) {
            $this->trajets[] = $trajet;
            $trajet->addArret($this);
        }

        return $this;
    }

    public function removeTrajet(Trajets $trajet): self
    {
        if ($this->trajets->removeElement($trajet)) {
            $trajet->removeArret($this);
        }

        return $this;
    }
}
