<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LignesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LignesRepository::class)
 * @ApiResource()
 */
class Lignes
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
    private $libelle_alle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle_retour;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cout;

    /**
     * @ORM\Column(type="float")
     */
    private $distance;

    /**
     * @ORM\Column(type="integer")
     */
    private $dureeMoyenneDepart;

    /**
     * @ORM\OneToMany(targetEntity=Voyages::class, mappedBy="lignes")
     */
    private $voyages;

    /**
     * @ORM\OneToMany(targetEntity=Trajets::class, mappedBy="ligne")
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
        $this->voyages = new ArrayCollection();
        $this->trajets = new ArrayCollection();
        $this->active = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleAlle(): ?string
    {
        return $this->libelle_alle;
    }

    public function setLibelleAlle(string $libelle_alle): self
    {
        $this->libelle_alle = $libelle_alle;

        return $this;
    }

    public function getLibelleRetour(): ?string
    {
        return $this->libelle_retour;
    }

    public function setLibelleRetour(string $libelle_retour): self
    {
        $this->libelle_retour = $libelle_retour;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCout(): ?string
    {
        return $this->cout;
    }

    public function setCout(string $cout): self
    {
        $this->cout = $cout;

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getDureeMoyenneDepart(): ?int
    {
        return $this->dureeMoyenneDepart;
    }

    public function setDureeMoyenneDepart(int $dureeMoyenneDepart): self
    {
        $this->dureeMoyenneDepart = $dureeMoyenneDepart;

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
            $voyage->setLignes($this);
        }

        return $this;
    }

    public function removeVoyage(Voyages $voyage): self
    {
        if ($this->voyages->removeElement($voyage)) {
            // set the owning side to null (unless already changed)
            if ($voyage->getLignes() === $this) {
                $voyage->setLignes(null);
            }
        }

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
            $trajet->setLigne($this);
        }

        return $this;
    }

    public function removeTrajet(Trajets $trajet): self
    {
        if ($this->trajets->removeElement($trajet)) {
            // set the owning side to null (unless already changed)
            if ($trajet->getLigne() === $this) {
                $trajet->setLigne(null);
            }
        }

        return $this;
    }
}
