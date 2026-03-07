<?php

namespace App\Entity;

use App\Repository\SessionFormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionFormationRepository::class)]
class SessionFormation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_fin = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    /**
     * @var Collection<int, DevisFormation>
     */
    #[ORM\OneToMany(mappedBy: 'sessionFormation', targetEntity: DevisFormation::class)]
    private Collection $DevisFormation;

    #[ORM\ManyToOne(inversedBy: 'sessionFormations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formation $formation = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_debut = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_fin = null;

    #[ORM\Column]
    private ?int $bloc = null;

    public function __construct()
    {
        $this->DevisFormation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTime $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTime $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, DevisFormation>
     */
    public function getDevisFormation(): Collection
    {
        return $this->DevisFormation;
    }

    public function addDevisFormation(DevisFormation $devisFormation): static
    {
        if (!$this->DevisFormation->contains($devisFormation)) {
            $this->DevisFormation->add($devisFormation);
            $devisFormation->setSessionFormation($this);
        }

        return $this;
    }

    public function removeDevisFormation(DevisFormation $devisFormation): static
    {
        if ($this->DevisFormation->removeElement($devisFormation)) {
            // set the owning side to null (unless already changed)
            if ($devisFormation->getSessionFormation() === $this) {
                $devisFormation->setSessionFormation(null);
            }
        }

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getHeureDebut(): ?\DateTime
    {
        return $this->heure_debut;
    }

    public function setHeureDebut(\DateTime $heure_debut): static
    {
        $this->heure_debut = $heure_debut;

        return $this;
    }

    public function getHeureFin(): ?\DateTime
    {
        return $this->heure_fin;
    }

    public function setHeureFin(\DateTime $heure_fin): static
    {
        $this->heure_fin = $heure_fin;

        return $this;
    }

    public function getBloc(): ?int
    {
        return $this->bloc;
    }

    public function setBloc(int $bloc): static
    {
        $this->bloc = $bloc;

        return $this;
    }
}
