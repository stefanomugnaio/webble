<?php

namespace App\Service;

use App\Entity\OffreCommerciale;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class OffrePdfService
{
    public function __construct(private Environment $twig) {}

    public function generer(OffreCommerciale $offre): string
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($this->twig->render('admin/offre/pdf.html.twig', ['offre' => $offre]), 'UTF-8');
        $dompdf->render();

        return $dompdf->output();
    }
}
