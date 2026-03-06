<?php

namespace App\Enum;

enum FormationStatus: string
{
    case INSCRIPTION = 'inscription';
    case EN_COURS = 'en_cours';
    case TERMINEE = 'terminee';
    case ANNULEE = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::INSCRIPTION => 'Inscription',
            self::EN_COURS => 'En cours',
            self::TERMINEE => 'Terminée',
            self::ANNULEE => 'Annulée',
        };
    }
}

enum FormationLibelle: string
{
    case DECOUVERTE = 'decouverte_informatique';

    public function label(): string
    {
        return match ($this) {
            self::DECOUVERTE => 'Découverte informatique',
        };
    }
}
