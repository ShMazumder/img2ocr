<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;

$user_agent = getenv("HTTP_USER_AGENT");

if (strpos($user_agent, "Win") !== FALSE)
    $os = "Windows";
elseif (strpos($user_agent, "Mac") !== FALSE)
    $os = "Mac";

$inputDir = "imgs";
$outputDir = "output";

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
    $outputFileName = $outputDir . DIRECTORY_SEPARATOR . (explode('.', $fileName)[0]) . '.txt';

    // file_put_contents($outputFileName, $result); // full save
    file_put_contents($outputFileName, implode("<br/>", array_slice(explode("\n", $result), 1)));
    break;
}

// echo json_encode(array("result" => implode("<br/>", explode("\n", $result))));
