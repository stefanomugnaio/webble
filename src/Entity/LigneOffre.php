<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ligne_offre')]
class LigneOffre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?OffreCommerciale $offre = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $libelle = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $quantite = '1.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $prixUnitaire = '0.00';

    #[ORM\Column]
    private bool $offerte = false;

    #[ORM\Column]
    private int $position = 0;

    public function getId(): ?int { return $this->id; }
    public function getOffre(): ?OffreCommerciale { return $this->offre; }
    public function setOffre(?OffreCommerciale $offre): static { $this->offre = $offre; return $this; }
    public function getLibelle(): string { return $this->libelle; }
    public function setLibelle(string $libelle): static { $this->libelle = $libelle; return $this; }
    public function getQuantite(): string { return $this->quantite; }
    public function setQuantite(string|float|int $quantite): static { $this->quantite = number_format((float) $quantite, 2, '.', ''); return $this; }
    public function getPrixUnitaire(): string { return $this->prixUnitaire; }
    public function setPrixUnitaire(string|float|int $prixUnitaire): static { $this->prixUnitaire = number_format((float) $prixUnitaire, 2, '.', ''); return $this; }
    public function isOfferte(): bool { return $this->offerte; }
    public function setOfferte(bool $offerte): static { $this->offerte = $offerte; return $this; }
    public function getPosition(): int { return $this->position; }
    public function setPosition(int $position): static { $this->position = $position; return $this; }
    public function getTotalHt(): float { return $this->offerte ? 0.0 : round((float) $this->quantite * (float) $this->prixUnitaire, 2); }
}
