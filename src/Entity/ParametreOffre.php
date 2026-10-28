<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'parametre_offre')]
class ParametreOffre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $papierEnteteUrl = null;

    public function getId(): ?int { return $this->id; }
    public function getPapierEnteteUrl(): ?string { return $this->papierEnteteUrl; }
    public function setPapierEnteteUrl(?string $papierEnteteUrl): static { $this->papierEnteteUrl = $papierEnteteUrl; return $this; }
}
