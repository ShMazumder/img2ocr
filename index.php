<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$action = "NONE";

$action = isset($_GET['action']) ? $_GET['action'] : $action;

if (strcasecmp($action, "OCR") === 0) {
    require "run.php";
} else if (strcasecmp($action, "PDF") === 0) {
    require "pdf.php";
} else if (strcasecmp($action, "VIEW") === 0) {
    require "view.php";
} else {
    // upload options?

    echo "<a href='index.php?action=ocr'>OCR<a>";
    echo "<a href='index.php?action=pdf'>PDF<a>";
    echo "<a href='index.php?action=view'>VIEW<a>";
}
