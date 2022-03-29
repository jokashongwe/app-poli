<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Snappy\Pdf;

class MembreCardPrinter
{
    private Options $pdfOptions;
    private Dompdf $pdffile;
    private Pdf $pdf;

    public function __construct()
    {
        /*$this->pdfOptions = new Options();
        $this->pdfOptions->setDefaultFont('Arial');
        $this->pdffile = new Dompdf($this->pdfOptions);
        $customPaper= Array(0,0, 1920, 1048);
        $this->pdffile->setPaper($customPaper); //*/
        $this->pdf = new Pdf();
    }

    public function print($html)
    {
        /*$this->pdffile->loadHtml($html);
        $filename = 'Cartes-'. rand(20000, 99999) . '.pdf';
        $this->pdffile->render();
        $output = $this->pdffile->output();
        */
        $filename = 'Cartes-'. rand(20000, 99999) . '.pdf';
        $this->pdf->generateFromHtml($html, '../public/exports/' .$filename);
        //file_put_contents('../public/exports/' .$filename,  $output);
    }

}