<?php

$inputDir = "output/emonbhai";

$list = scandir($inputDir);


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

    $fileContent = file_get_contents($txt_path);

    // echo $fileContent;

    // $extractedData = extractPhoneNumbersAndEmails($fileContent);
    $extractedData = extractBangladeshiPhoneNumbersAndEmails($fileContent);
    $phoneNumbers = $extractedData['phoneNumbers'];
    $emails = $extractedData['emails'];

    // Process the extracted data (e.g., store in a database, print, etc.)
    echo "File $fileName:<br/>";
    echo "Phone Numbers: " . implode(", ", $phoneNumbers) . "<br/>";
    echo "Email Addresses: " . implode(", ", $emails) . "<br/><br/>";

    // break;
}

function extractBangladeshiPhoneNumbersAndEmails($fileContent) {
    $phoneNumbers = [];
    $emails = [];

    // Regular expression pattern to match Bangladeshi cell phone numbers
    // $phonePattern = '/\+88-\d{2}-\d{8}|\+880\d{2}\d{8}|01\d{9}/'; // working
    $phonePattern = '/\+88(?:-\d{2}-|\s?\d{2}\s?)\d{8}|\+880\s?\d{10}|01\d{9}/'; 


    // Regular expression pattern to match email addresses
    $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';

    // Match phone numbers
    preg_match_all($phonePattern, $fileContent, $matches);
    if (!empty($matches[0])) {
        $phoneNumbers = $matches[0];
    }

    // Match email addresses
    preg_match_all($emailPattern, $fileContent, $matches);
    if (!empty($matches[0])) {
        $emails = $matches[0];
    }

    return [
        'phoneNumbers' => $phoneNumbers,
        'emails' => $emails
    ];
}



function extractPhoneNumbersAndEmails($fileContent)
{
    $phoneNumbers = [];
    $emails = [];

    // Regular expression pattern to match phone numbers
    $phonePattern = '/(?:\+?\d{2,4}-?)?\(?\d{2,4}\)?[-.\s]?\d{2,4}[-.\s]?\d{2,4}/';

    // Regular expression pattern to match email addresses
    $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';

    // Match phone numbers
    preg_match_all($phonePattern, $fileContent, $matches);
    if (!empty($matches[0])) {
        $phoneNumbers = $matches[0];
    }

    // Match email addresses
    preg_match_all($emailPattern, $fileContent, $matches);
    if (!empty($matches[0])) {
        $emails = $matches[0];
    }

    return [
        'phoneNumbers' => $phoneNumbers,
        'emails' => $emails
    ];
}

// // Loop through 200 files
// $numberOfFiles = 200;
// for ($i = 1; $i <= $numberOfFiles; $i++) {
//     $filename = "file_" . $i . ".txt"; // Replace with the actual filenames and paths
//     $fileContent = file_get_contents($filename);

//     $extractedData = extractPhoneNumbersAndEmails($fileContent);
//     $phoneNumbers = $extractedData['phoneNumbers'];
//     $emails = $extractedData['emails'];

//     // Process the extracted data (e.g., store in a database, print, etc.)
//     echo "File $i:\n";
//     echo "Phone Numbers: " . implode(", ", $phoneNumbers) . "\n";
//     echo "Email Addresses: " . implode(", ", $emails) . "\n\n";
// }
