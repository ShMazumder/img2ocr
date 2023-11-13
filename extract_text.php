<?php
require_once __DIR__ . '/vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;

define('K_PATH_MAIN', dirname(__FILE__) . '/');

if (isset($_SERVER['HTTP_HOST']) and (!empty($_SERVER['HTTP_HOST']))) {
    if (isset($_SERVER['HTTPS']) and (!empty($_SERVER['HTTPS'])) and (strtolower($_SERVER['HTTPS']) != 'off')) {
        $host_protocol = 'https://';
    } else {
        $host_protocol = 'http://';
    }
    $host = $_SERVER['HTTP_HOST'];
    $host .= str_replace('\\', '/', substr(K_PATH_MAIN, (strlen($_SERVER['DOCUMENT_ROOT']) - 0)));

    // echo "<br/>";
    // echo K_PATH_MAIN;
    // echo "<br/>";
    // echo $_SERVER['DOCUMENT_ROOT']." ". (strlen($_SERVER['DOCUMENT_ROOT']) - 0). " ".substr(K_PATH_MAIN, (strlen($_SERVER['DOCUMENT_ROOT']) - 0));
    // echo "<br/>";
    // echo $host;
    // echo "<br/>";

} else {
    $host_protocol = 'http://';
    $host = 'localhost/img2ocr';
}

$user_agent = getenv("HTTP_USER_AGENT");

if (strpos($user_agent, "Win") !== FALSE)
    $os = "Windows";
elseif (strpos($user_agent, "Mac") !== FALSE)
    $os = "Mac";

echo "<br/>";
echo "os=$os user_agent=$user_agent";
echo "<br/>";

$ocr = new TesseractOCR();
$ocr->lang('ben', 'eng');
// $langs = $ocr->availableLanguages();
// var_dump($langs);

if (strcasecmp($_SERVER['HTTP_HOST'], "localhost") === 0) {
    if ($os === "Windows") {
        $ocr->executable("C:\Program Files\Tesseract-OCR\\tesseract.exe");
    } elseif ($os === "Mac") {
        $ocr->tempDir("/tmp");
        $ocr->executable("/usr/local/bin/tesseract");
    } else {
        // echo "os=$os";
        // $ocr->tempDir("/tmp");
        // $ocr->executable("/usr/local/bin/tesseract");
    }
} else {
    $ocr->tempDir("/tmp");
    $ocr->executable("/var/home/shmazumd/build/tesseract");
}

$inputDirRoot = dirname(__FILE__) . DIRECTORY_SEPARATOR . $target_dir . DIRECTORY_SEPARATOR; //"uploads";
$outputDirRoot = dirname(__FILE__) . DIRECTORY_SEPARATOR . "results/" . explode("/", $target_dir)[1];
if (!file_exists($outputDirRoot)) {
    mkdir($outputDirRoot, 0777, true);
}
processDir($ocr, $inputDirRoot, $outputDirRoot);

// // echo json_encode(array("result" => implode("<br/>", explode("\n", $result))));
// $listOfInputDir = scandir($inputDirRoot);
// // echo $inputDirRoot;
// // var_dump($listOfInputDir);
// for ($inputDirIndex = 0; $inputDirIndex < count($listOfInputDir); $inputDirIndex++) {

//     $currentItem = $listOfInputDir[$inputDirIndex];

//     if (in_array($currentItem, array('.', '..', '.DS_Store'))) {
//         continue;
//     }

//     $inputDir = $inputDirRoot . DIRECTORY_SEPARATOR . $currentItem;
//     $outputDir = $outputDirRoot . DIRECTORY_SEPARATOR . $currentItem;

//     $inputDir = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $inputDir);
//     $outputDir = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $outputDir);

//     if (!is_dir($inputDir)) {
//         echo "Dir Skipped: " . $inputDir;
//         continue;
//     }

//     processDir($ocr, $inputDir, $outputDir);
// }

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

        if (in_array($fileName, array('.', '..', '.DS_Store'))) {
            continue;
        }

        $img_path = $inputDir . DIRECTORY_SEPARATOR . $fileName;
        $img_path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $img_path);

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

        $publicInputUrl = str_replace(dirname(__FILE__), $GLOBALS['host'], $img_path);
        $publicInputUrl = $GLOBALS['host_protocol'] . str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $publicInputUrl);
        echo "<br/>";

        $publicUrl = str_replace(dirname(__FILE__), $GLOBALS['host'], $outputFileName);
        $publicUrl = $GLOBALS['host_protocol'] . str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $publicUrl);
        echo "<br/>";

        echo "<div style='display:flex;'>";
        echo "<div style='max-width: 50%;min-width: 50%; padding: 8px;'>";
        echo "<div>Given photo</div>";
        echo "<img src='" . $publicInputUrl . "' style='max-width: 100%; min-width: 100%; border:1px solid grey;'/>";
        echo "</div>";
        echo "<div style='padding: 8px;'>";
        echo "<div>Result</div>";
        echo "<div style='border:1px solid grey; padding: 8px;'>$contents</div>";
        echo  "<a href='$publicUrl' download>Click to download</a>";
        echo "</div>";
        echo "</div>";
    }
}
