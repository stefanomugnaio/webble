<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(length: 150, unique: true)]
    private ?string $slug = null;

    /**
     * @var Collection<int, DevisFormation>
     */
    #[ORM\OneToMany(mappedBy: 'Formations', targetEntity: DevisFormation::class)]
    private Collection $DevisFormation;

    public function __construct()
    {
        $this->DevisFormation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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
            $devisFormation->setFormations($this);
        }

        return $this;
    }

    public function removeDevisFormation(DevisFormation $devisFormation): static
    {
        if ($this->DevisFormation->removeElement($devisFormation)) {
            // set the owning side to null (unless already changed)
            if ($devisFormation->getFormations() === $this) {
                $devisFormation->setFormations(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of slug
     */ 
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @return  self
     */ 
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }
}
