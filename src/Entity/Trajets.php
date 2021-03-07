<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TrajetsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * @ORM\Entity(repositoryClass=TrajetsRepository::class)
 *  @GRID\Source(columns="id, libelle,ligne.numero")
 * @ApiResource()
 */
class Trajets
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
     * @ORM\ManyToOne(targetEntity=Lignes::class, inversedBy="trajets")
     * @GRID\Column(field="ligne.numero", title="Ligne")
     */
    private $ligne;

    /**
     * @ORM\ManyToMany(targetEntity=Arrets::class, inversedBy="trajets")
     */
    private $arrets;

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

    public function getLigne(): ?Lignes
    {
        return $this->ligne;
    }

    public function setLigne(?Lignes $ligne): self
    {
        $this->ligne = $ligne;

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
        }

        return $this;
    }

    public function removeArret(Arrets $arret): self
    {
        $this->arrets->removeElement($arret);

        return $this;
    }
}
