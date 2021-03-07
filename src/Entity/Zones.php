<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ZonesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ZonesRepository::class)
 * @ApiResource()
 */
class Zones
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
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Arrets::class, mappedBy="zone")
     */
    private $arrets;

    /**
     * @ORM\ManyToOne(targetEntity=Communes::class, inversedBy="zones")
     */
    private $communes;

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
        $this->arrets = new ArrayCollection();
        $this->active = 1;
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

    /**
     * @return Collection|Arrets[]
     */
    public function getArrets(): Collection
    {
        return $this->arrets;
    }

    public function addArret(Arrets $arret): self
    {
        if (!$this->arrets->contains($arret)) {
            $this->arrets[] = $arret;
            $arret->setZone($this);
        }

        return $this;
    }

    public function removeArret(Arrets $arret): self
    {
        if ($this->arrets->removeElement($arret)) {
            // set the owning side to null (unless already changed)
            if ($arret->getZone() === $this) {
                $arret->setZone(null);
            }
        }

        return $this;
    }

    public function getCommunes(): ?Communes
    {
        return $this->communes;
    }

    public function setCommunes(?Communes $communes): self
    {
        $this->communes = $communes;

        return $this;
    }
}
