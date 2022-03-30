<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;

class MembreCardPrinter
{
    private Options $pdfOptions;
    private Dompdf $pdffile;
    //private Pdf $pdf;

    public function __construct()
    {
        $this->pdfOptions = new Options();
        $this->pdfOptions->setDefaultFont('Arial');
        $this->pdfOptions->setIsRemoteEnabled(true);
        $this->pdffile = new Dompdf($this->pdfOptions);
        $customPaper= Array(0,0, 567.00, 283.00);
        $this->pdffile->setPaper($customPaper);
        /*$windowsENV = "C:\Users\LENOVO\wkhtmltox-0.12.6-1.mxe-cross-win64\wkhtmltox\bin\wkhtmltopdf.exe";
        $linuxEnv = "/usr/local/bin/wkhtmltopdf";
        $this->pdf = new Pdf($windowsENV);*/
    }

    public function print($html)
    {
        $this->pdffile->loadHtml($html);

        $this->pdffile->render();
        $output = $this->pdffile->output();

       /*$filename = 'Cartes-'. rand(20000, 99999) . '.pdf';
         $this->pdf->generateFromHtml($html, '../public/exports/' .$filename, [
           'page-width' => 567.00,
            'page-height' => 283.00,
            'enable-javascript' => true,
            'javascript-delay' => 1000,
            'no-stop-slow-scripts' => true,
            'no-background' => false,
            'lowquality' => false,
            'encoding' => 'utf-8',
            'images' => true,
            'enable-external-links' => true,
            'enable-internal-links' => true
        ]);*/
        return new Response($output);
    }

}