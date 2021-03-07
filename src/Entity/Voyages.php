<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VoyagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=VoyagesRepository::class)
 * @ApiResource()
 */
class Voyages
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time",nullable=true)
     * @Assert\Time
     * @var string A "H:i" formatted value
     */
    private $heureArrivee;

    /**
     * @ORM\OneToMany(targetEntity=Tickets::class, mappedBy="voyages")
     */
    private $tickets;



    /**
     * @ORM\ManyToOne(targetEntity=Chauffeurs::class, inversedBy="voyages")
     */
    private $chauffeurs;

    /**
     * @ORM\ManyToOne(targetEntity=Lignes::class, inversedBy="voyages")
     */
    private $lignes;

    /**
     * @ORM\ManyToOne(targetEntity=Vehicules::class, inversedBy="voyages")
     */
    private $vehicule;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateVoyage;

    /**
     * @ORM\Column(type="time")
     * @Assert\Time
     * @var string A "H:i" formatted value
     */
    private $heureDepart;

    /**
     * @ORM\ManyToOne(targetEntity=Libelle::class, inversedBy="voyages")
     */
    private $libelle;

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
        $this->tickets = new ArrayCollection();
        $this->vehicules = new ArrayCollection();
        $this->active = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
public  function info (){
        return $this->getLibelle()->getLibelle() ;
}

    public function getHeureArrivee(): ?\DateTimeInterface
    {
        return $this->heureArrivee;
    }

    public function setHeureArrivee(\DateTimeInterface  $heureArrivee): self
    {
        $this->heureArrivee = $heureArrivee;

        return $this;
    }

    /**
     * @return Collection|Tickets[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Tickets $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setVoyages($this);
        }

        return $this;
    }

    public function removeTicket(Tickets $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getVoyages() === $this) {
                $ticket->setVoyages(null);
            }
        }

        return $this;
    }



    public function getChauffeurs(): ?Chauffeurs
    {
        return $this->chauffeurs;
    }

    public function setChauffeurs(?Chauffeurs $chauffeurs): self
    {
        $this->chauffeurs = $chauffeurs;

        return $this;
    }

    public function getLignes(): ?Lignes
    {
        return $this->lignes;
    }

    public function setLignes(?Lignes $lignes): self
    {
        $this->lignes = $lignes;

        return $this;
    }

    public function getVehicule(): ?Vehicules
    {
        return $this->vehicule;
    }

    public function setVehicule(?Vehicules $vehicule): self
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    public function getDateVoyage(): ?\DateTimeInterface
    {
        return $this->dateVoyage;
    }

    public function setDateVoyage(\DateTimeInterface $dateVoyage): self
    {
        $this->dateVoyage = $dateVoyage;

        return $this;
    }

    public function getHeureDepart(): ?\DateTimeInterface
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(\DateTimeInterface $heureDepart): self
    {
        $this->heureDepart = $heureDepart;

        return $this;
    }

    public function getLibelle(): ?Libelle
    {
        return $this->libelle;
    }

    public function setLibelle(?Libelle $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }
}
