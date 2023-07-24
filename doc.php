<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/bootstrap.php';

// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();

$section = $phpWord->addSection();
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(10);

// $fontStyle = new \PhpOffice\PhpWord\Style\Font();
// $fontStyle->setBold(true);
// $fontStyle->setName('Tahoma');
// $fontStyle->setSize(13);
$myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
// $myTextElement->setFontStyle($fontStyle);

$dirname = uniqid('', true);
// $dir = \PhpOffice\PhpWord\Settings::getTempDir() . '/' . $dirname;
$dir = __DIR__ . DIRECTORY_SEPARATOR . "tmp";
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}
\PhpOffice\PhpWord\Settings::setTempDir($dir);

// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

$fileName = 'output_doc/helloWorld.docx';
$objWriter->save($fileName);
