<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'offre_commerciale')]
class OffreCommerciale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40, unique: true)]
    private string $numero = '';

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $dateEmission;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $dateValidite;

    #[ORM\Column(length: 30)]
    private string $statut = 'brouillon';

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $clientSociete = null;

    #[ORM\Column(length: 180)]
    private string $clientNom = '';

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $clientEmail = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $clientAdresse = null;

    #[ORM\Column(length: 255)]
    private string $objet = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $introduction = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $conditions = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $tauxTva = '0.00';

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $papierEnteteUrl = null;

    /** @var Collection<int, LigneOffre> */
    #[ORM\OneToMany(mappedBy: 'offre', targetEntity: LigneOffre::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    private Collection $lignes;

    public function __construct()
    {
        $this->dateEmission = new \DateTimeImmutable('today');
        $this->dateValidite = new \DateTimeImmutable('+30 days');
        $this->lignes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNumero(): string { return $this->numero; }
    public function setNumero(string $numero): static { $this->numero = $numero; return $this; }
    public function getDateEmission(): \DateTimeImmutable { return $this->dateEmission; }
    public function setDateEmission(\DateTimeInterface $dateEmission): static { $this->dateEmission = \DateTimeImmutable::createFromInterface($dateEmission); return $this; }
    public function getDateValidite(): \DateTimeImmutable { return $this->dateValidite; }
    public function setDateValidite(\DateTimeInterface $dateValidite): static { $this->dateValidite = \DateTimeImmutable::createFromInterface($dateValidite); return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }
    public function getClientSociete(): ?string { return $this->clientSociete; }
    public function setClientSociete(?string $clientSociete): static { $this->clientSociete = $clientSociete; return $this; }
    public function getClientNom(): string { return $this->clientNom; }
    public function setClientNom(string $clientNom): static { $this->clientNom = $clientNom; return $this; }
    public function getClientEmail(): ?string { return $this->clientEmail; }
    public function setClientEmail(?string $clientEmail): static { $this->clientEmail = $clientEmail; return $this; }
    public function getClientAdresse(): ?string { return $this->clientAdresse; }
    public function setClientAdresse(?string $clientAdresse): static { $this->clientAdresse = $clientAdresse; return $this; }
    public function getObjet(): string { return $this->objet; }
    public function setObjet(string $objet): static { $this->objet = $objet; return $this; }
    public function getIntroduction(): ?string { return $this->introduction; }
    public function setIntroduction(?string $introduction): static { $this->introduction = $introduction; return $this; }
    public function getConditions(): ?string { return $this->conditions; }
    public function setConditions(?string $conditions): static { $this->conditions = $conditions; return $this; }
    public function getTauxTva(): string { return $this->tauxTva; }
    public function setTauxTva(string|float|int $tauxTva): static { $this->tauxTva = number_format((float) $tauxTva, 2, '.', ''); return $this; }
    public function getPapierEnteteUrl(): ?string { return $this->papierEnteteUrl; }
    public function setPapierEnteteUrl(?string $papierEnteteUrl): static { $this->papierEnteteUrl = $papierEnteteUrl; return $this; }
    /** @return Collection<int, LigneOffre> */
    public function getLignes(): Collection { return $this->lignes; }
    public function addLigne(LigneOffre $ligne): static { if (!$this->lignes->contains($ligne)) { $this->lignes->add($ligne); $ligne->setOffre($this); } return $this; }
    public function removeLigne(LigneOffre $ligne): static { if ($this->lignes->removeElement($ligne) && $ligne->getOffre() === $this) { $ligne->setOffre(null); } return $this; }
    public function getTotalHt(): float { return array_reduce($this->lignes->toArray(), fn (float $total, LigneOffre $ligne) => $total + $ligne->getTotalHt(), 0.0); }
    public function getMontantTva(): float { return round($this->getTotalHt() * ((float) $this->tauxTva / 100), 2); }
    public function getTotalTtc(): float { return round($this->getTotalHt() + $this->getMontantTva(), 2); }
}
