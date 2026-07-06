<?php

namespace App\Config;

final class AppConfig
{
    public const TAUX_TVA = 0.00;

    public const WEBBLE_PLUS = [
        'libelle' => 'Webble+',
        'prix_mensuel_ht' => 12.00,
        'description' => 'Abonnement mensuel séparé du devis de création.',
    ];

    public const OPTIONS_DEVIS = [
        'nom_domaine' => [
            'libelle' => 'Assistance à la création du nom de domaine',
            'description' => 'Assistance à la création du nom de domaine.',
            'prix_ht' => 20.00,
            'active_par_defaut' => true,
        ],

        'hebergement' => [
            'libelle' => 'Configuration de l’hébergement et mise en ligne',
            'description' => 'Configuration de l’hébergement et mise en ligne.',
            'prix_ht' => 30.00,
            'active_par_defaut' => true,
        ],
    ];

    public const OFFRES = [
        'tranquille' => [
            'libelle' => 'Tranquille',
            'titre_carte' => 'Offre Tranquille',
            'description' => 'Pour avoir enfin un vrai site professionnel.',
            'accroche_creation_site' => 'Pour enfin avoir un vrai site, et plus seulement une page Facebook.',
            'resume_creation_site' => 'Idéal pour lancer rapidement votre présence en ligne avec une page claire et professionnelle.',
            'prix_site_ht' => 150.00,
            'pourcentage_reduction' => 0.00,
            'webble_plus_mois_offerts' => 0,
            'caracteristiques' => [
                '1 page principale (landing page)',
                'Design responsive sur mobile, tablette et ordinateur',
                'Intégration de votre logo et de vos couleurs',
                'Livraison clé en main, prête à être mise en ligne',
            ],
        ],

        'serieuse' => [
            'libelle' => 'Sérieuse',
            'titre_carte' => 'Offre Sérieuse',
            'description' => 'La formule idéale pour un site solide et évolutif.',
            'accroche_creation_site' => 'Pour ceux qui en ont marre que le site du voisin soit mieux que le leur.',
            'resume_creation_site' => 'La formule idéale pour les indépendants et petites structures qui veulent un site solide et évolutif.',
            'prix_site_ht' => 550.00,
            'pourcentage_reduction' => 0.00,
            'webble_plus_mois_offerts' => 3,
            'caracteristiques' => [
                'Tout ce qui est inclus dans l’offre Tranquille',
                'Jusqu’à 5 pages',
                'Optimisation de base pour le référencement naturel',
                'Intégration des réseaux sociaux',
                'Formation rapide à la prise en main',
                'Support pendant 3 mois après la mise en ligne',
            ],
        ],

        'pro' => [
            'libelle' => 'Pro',
            'titre_carte' => 'Offre Pro',
            'description' => 'Pour les projets qui sortent du cadre.',
            'accroche_creation_site' => 'Pour les idées qui ne rentrent dans aucune case, et c’est très bien comme ça.',
            'resume_creation_site' => 'Pour les projets qui sortent du cadre : fonctionnalités avancées, espace client, etc.',
            'prix_site_ht' => null,
            'pourcentage_reduction' => 0.00,
            'webble_plus_mois_offerts' => 6,
            'caracteristiques' => [
                'Pages libres',
                'Fonctionnalités avancées',
                'Blog',
                'Espace client',
                'Réservation',
                'Accompagnement personnalisé',
            ],
        ],
    ];

    public const PRESTATIONS_COMPLEMENTAIRES = [
        'refonte_site' => [
            'badge' => 'Site existant',
            'theme' => 'violet',
            'icone' => 'bi bi-arrow-repeat',
            'titre' => 'Refonte de site web',
            'description' => 'Votre site est vieillissant, difficile à utiliser ou ne correspond plus à votre activité ? Une refonte permet de repartir sur une base plus claire, moderne et efficace.',
            'libelle_condition' => null,
            'caracteristiques' => [
                'Analyse de l’existant et identification des améliorations',
                'Modernisation du design et de l’expérience utilisateur',
                'Adaptation complète aux mobiles et aux tablettes',
                'Amélioration des performances et du référencement de base',
                'Reprise ou réorganisation des contenus existants',
            ],
            'prix_libelle' => 'Sur devis',
            'prix_description' => 'Selon l’état et la complexité du site',
            'bouton_libelle' => 'Parler de ma refonte',
            'route' => 'app_contact',
            'route_params' => [
                'sujet' => 'refonte-site',
            ],
        ],

        'nouvelle_fonctionnalite' => [
            'badge' => 'Client Webble',
            'theme' => 'jaune',
            'icone' => 'bi bi-puzzle-fill',
            'titre' => 'Nouvelle fonctionnalité',
            'description' => 'Votre activité évolue ? Une nouvelle fonctionnalité peut être ajoutée à un site déjà développé par Webble, sans avoir besoin de tout reconstruire.',
            'libelle_condition' => 'Uniquement pour les clients Webble',
            'caracteristiques' => [
                'Étude de la demande et vérification de sa faisabilité technique',
                'Développement adapté à la structure actuelle de votre site',
                'Intégration cohérente avec le design et les fonctionnalités existantes',
                'Tests complets avant la mise en ligne de la nouvelle fonctionnalité',
                'Tarif préférentiel pour les clients disposant d’un abonnement Webble+',
            ],
            'prix_libelle' => 'Sur devis',
            'prix_description' => 'Selon la fonctionnalité demandée',
            'bouton_libelle' => 'Faire évoluer mon site',
            'route' => 'app_contact',
            'route_params' => [
                'sujet' => 'nouvelle-fonctionnalite',
            ],
        ],
    ];
}
