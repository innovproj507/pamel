<?php

namespace Plugins\Elearning\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;

class CertificateService
{
    /**
     * Generate a certificate document.
     */
    public static function generate($userName, $courseTitle, $date, $code)
    {
        $phpWord = new PhpWord();
        
        // Page setup (Landscape)
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginTop'   => 1200,
            'marginBottom'=> 1200,
            'marginLeft'  => 1200,
            'marginRight' => 1200,
        ]);

        // Add a border (Optional, using a table or shape)
        
        // Header / Logo
        $section->addText('PANAMA MARITIME E-LEARNING (PAMEL), S.A.', 
            ['bold' => true, 'size' => 20, 'color' => '1a3a6b'], 
            ['alignment' => Jc::CENTER]);
        
        $section->addTextBreak(1);
        
        $section->addText('CERTIFICADO DE COMPLETITUD', 
            ['bold' => true, 'size' => 28, 'color' => 'b8860b'], 
            ['alignment' => Jc::CENTER]);
            
        $section->addTextBreak(2);
        
        $section->addText('Se otorga el presente a:', 
            ['italic' => true, 'size' => 14], 
            ['alignment' => Jc::CENTER]);
            
        $section->addTextBreak(1);
        
        $section->addText(strtoupper($userName), 
            ['bold' => true, 'size' => 32, 'underline' => 'single'], 
            ['alignment' => Jc::CENTER]);
            
        $section->addTextBreak(2);
        
        $section->addText('Por haber completado satisfactoriamente el curso de formación:', 
            ['size' => 14], 
            ['alignment' => Jc::CENTER]);
            
        $section->addTextBreak(1);
        
        $section->addText('"' . strtoupper($courseTitle) . '"', 
            ['bold' => true, 'size' => 22, 'color' => '1a3a6b'], 
            ['alignment' => Jc::CENTER]);
            
        $section->addTextBreak(3);
        
        // Footer: Date and Code
        $table = $section->addTable(['alignment' => Jc::CENTER]);
        $table->addRow();
        $table->addCell(4500)->addText('Fecha: ' . $date, ['size' => 11], ['alignment' => Jc::LEFT]);
        $table->addCell(4500)->addText('Código de Verificación: ' . $code, ['size' => 11], ['alignment' => Jc::RIGHT]);
        
        $section->addTextBreak(2);
        
        $section->addText('Este documento certifica que el alumno ha cumplido con todos los requisitos académicos del programa.', 
            ['size' => 9, 'color' => '666666'], 
            ['alignment' => Jc::CENTER]);

        // Save to temporary file
        $filename = 'certificado_' . $code . '.docx';
        $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filepath);
        
        return $filepath;
    }
}
