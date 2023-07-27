<?php
require_once __DIR__ . '/vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;

$user_agent = getenv("HTTP_USER_AGENT");

if (strpos($user_agent, "Win") !== FALSE)
    $os = "Windows";
elseif (strpos($user_agent, "Mac") !== FALSE)
    $os = "Mac";

$inputDir = "imgs/emonbhai";
$outputDir = "output/emonbhai";

$ocr = new TesseractOCR();
if ($os === "Windows") {
    $ocr->executable("C:\Program Files\Tesseract-OCR\\tesseract.exe");
} elseif ($os === "Mac") {
    $ocr->tempDir("/tmp");
    $ocr->executable("/usr/local/bin/tesseract");
}


$list = scandir($inputDir);

for ($fileIndex = 0; $fileIndex < count($list); $fileIndex++) {
    # code...

    // echo $list[$fileIndex];
    // echo "<br/>";

    $fileName = $list[$fileIndex];
    $img_path = $inputDir . DIRECTORY_SEPARATOR . $fileName;

    if (in_array($fileName, array('.', '..', '.DS_Store'))) {
        continue;
    }

    if (!is_file($img_path)) {
        continue;
    }

    $ocr->image($img_path);

    // $ocr->configFile('pdf');
    // $ocr->setOutputFile('output/IMG_0101.pdf');
    $result = $ocr->run();

    // echo $result;
    $fileNameArr = explode('.', $fileName);
    $fileNameArr = array_slice($fileNameArr, 0, count($fileNameArr) - 1);
    $outputFileName = $outputDir . DIRECTORY_SEPARATOR . implode($fileNameArr) . '.txt';

    // file_put_contents($outputFileName, $result); // full save

    // save without first line and removing single breaks.
    $contents = explode("\n", $result); // break the result by newline: array
    $contents = array_slice($contents, 1); // remove first line : array
    $contents = implode(" ", $contents); // join the array using space : string
    $contents = explode("  ", $contents); // break content using double space : array
    $contents = implode(" ", $contents); // join the array with break


    file_put_contents($outputFileName, $contents);

    echo $outputFileName . "=OK" . "<br/>";
}

// echo json_encode(array("result" => implode("<br/>", explode("\n", $result))));
