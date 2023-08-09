<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/bootstrap.php';

$inputDir = "output/project-plan-d/ppd";
$outputDir = "output_doc/project-plan-d/ppd";

$list = scandir($inputDir);
$chapterCount = 0;

$combined_doc = false;


for ($fileIndex = 1; $fileIndex <= count($list); $fileIndex++) {

    if (!isset($phpWord) || !$combined_doc) {
        // Creating the new document...
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
    }

    $phpWord->setDefaultFontName('Arial');
    $phpWord->setDefaultFontSize(14);
    // each page 
    $section = $phpWord->addSection();

    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setBold(true);
    $fontStyle->setName('Arial');
    $fontStyle->setSize(16);
    $header = $section->addHeader();
    // $header->addWatermark('resources/_earth.jpg', array('marginTop' => 200, 'marginLeft' => 55));
    $header->addText("Project Task");
    $header->setFontStyle($fontStyle);



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

    // echo $txt_path;
    // echo "<br/>";

    // $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    // $fontStyle->setBold(true);
    // $fontStyle->setName('Tahoma');
    // $fontStyle->setSize(13);

    $pageTitle = explode('.', explode("/", $txt_path)[1])[0];
    $myTextElement = $section->addText(htmlspecialchars($pageTitle));
    $myTextElement->setFontStyle($fontStyle);

    $section->addTextBreak(2);

    $content = file_get_contents($txt_path);
    $myTextElement = $section->addText(htmlspecialchars($content));
    // $myTextElement->setFontStyle($fontStyle);
    $section->addPageBreak();


    // 
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

    if (!$combined_doc) {
        $fileName = explode(".", $fileName)[0];
        $fileLocation = "$outputDir/$fileName.docx";
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        $objWriter->save($fileLocation);
    } else if ($fileIndex <= count($list)) {
        $fileLocation = "$outputDir/merged-all.docx";
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        $objWriter->save($fileLocation);
    }
}
