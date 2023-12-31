<?php
require_once __DIR__ . '/vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;

$user_agent = getenv("HTTP_USER_AGENT");

if (strpos($user_agent, "Win") !== FALSE)
    $os = "Windows";
elseif (strpos($user_agent, "Mac") !== FALSE)
    $os = "Mac";

$ocr = new TesseractOCR();
$ocr->lang('ben', 'eng');
$langs = $ocr->availableLanguages();
var_dump($langs);

if ($os === "Windows") {
    $ocr->executable("C:\Program Files\Tesseract-OCR\\tesseract.exe");
} elseif ($os === "Mac") {
    $ocr->tempDir("/tmp");
    $ocr->executable("/usr/local/bin/tesseract");
}

$inputDirRoot = "imgs/project-plan-d/";
$outputDirRoot = "output/project-plan-d/";

// echo json_encode(array("result" => implode("<br/>", explode("\n", $result))));
$listOfInputDir = scandir($inputDirRoot);

for ($inputDirIndex = 0; $inputDirIndex < count($listOfInputDir); $inputDirIndex++) {

    $currentItem = $listOfInputDir[$inputDirIndex];
    $inputDir = $inputDirRoot . DIRECTORY_SEPARATOR . $currentItem;
    $outputDir = $outputDirRoot . DIRECTORY_SEPARATOR . $currentItem;

    if (in_array($currentItem, array('.', '..', '.DS_Store'))) {
        continue;
    }

    if (!is_dir($inputDir)) {
        echo "Skipped: " . $inputDir;
        continue;
    }

    processDir($ocr, $inputDir, $outputDir);
}

function processDir($ocr, $inputDir, $outputDir)
{
    $removeFirstList = false;

    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0777, true);
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
        try {
            $result = $ocr->run();
        } catch (\Throwable $th) {
            echo $img_path . "=Failed (" . $th->getMessage() . ")";
            continue;
        }

        // echo $result;
        $fileNameArr = explode('.', $fileName);
        $fileNameArr = array_slice($fileNameArr, 0, count($fileNameArr) - 1);
        $outputFileName = $outputDir . DIRECTORY_SEPARATOR . implode($fileNameArr) . '.txt';

        // file_put_contents($outputFileName, $result); // full save

        // save without first line and removing single breaks.
        $contents = explode("\n", $result); // break the result by newline: array

        if ($removeFirstList) {
            $contents = array_slice($contents, 1); // remove first line : array
        }

        $contents = implode(" ", $contents); // join the array using space : string
        $contents = explode("  ", $contents); // break content using double space : array
        $contents = implode(" ", $contents); // join the array with break


        file_put_contents($outputFileName, $contents);

        echo $outputFileName . "=OK" . "<br/>";
    }
}
