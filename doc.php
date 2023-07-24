<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/bootstrap.php';

$inputDir = "output";
$outputDir = "output_doc";

$list = scandir($inputDir);
$chapterCount = 0;

// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(10);
// each page 
$section = $phpWord->addSection();

$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Times New Roman');
$fontStyle->setSize(13);
$header = $section->addHeader();
// $header->addWatermark('resources/_earth.jpg', array('marginTop' => 200, 'marginLeft' => 55));
$header->addText("Project Task");
$header->setFontStyle($fontStyle);

for ($fileIndex = 1; $fileIndex <= count($list); $fileIndex++) {
    $fileName = $list[$fileIndex - 1];
    $txt_path = $inputDir . DIRECTORY_SEPARATOR . $fileName;

    if (in_array($fileName, array('.', '..', '.DS_Store'))) {
        continue;
    }

    if (!is_file($txt_path)) {
        continue;
    }

    if (is_dir($txt_path)) {
        continue;
    }

    echo $txt_path;
    echo "<br/>";

    // $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    // $fontStyle->setBold(true);
    // $fontStyle->setName('Tahoma');
    // $fontStyle->setSize(13);
    $content = file_get_contents($txt_path);
    $myTextElement = $section->addText($content);
    // $myTextElement->setFontStyle($fontStyle);
    $section->addPageBreak();
}

// save 
$dirname = uniqid('', true);
// $dir = \PhpOffice\PhpWord\Settings::getTempDir() . '/' . $dirname;
$dir = __DIR__ . DIRECTORY_SEPARATOR . "tmp";
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}
\PhpOffice\PhpWord\Settings::setTempDir($dir);

// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

$fileName = "output";
$fileLocation = "output_doc/$fileName.docx";
$objWriter->save($fileLocation);
