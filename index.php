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
} else if (strcasecmp($action, "DOC") === 0) {
    require "doc.php";
} else if (strcasecmp($action, "multidoc") === 0) {
    require "multiple_doc.php";
} else if (strcasecmp($action, "doc2pdf") === 0) {
    require "doc2pdf.php";
} else if (strcasecmp($action, "VIEW") === 0) {
    require "view.php";
} else {
    // upload options?

    echo "<div style='width:100%; display:flex; justify-content:around;'>";

    echo "<div style='width: 200px'>";
    echo "<a href='index.php?action=ocr'>OCR<a>";
    echo "</div>";

    echo "<div style='width: 200px'>";
    echo "<a href='index.php?action=pdf'>PDF<a>";
    echo "</div>";

    echo "<div style='width: 200px'>";
    echo "<a href='index.php?action=doc'>DOC<a>";
    echo "</div>";

    echo "<div style='width: 200px'>";
    echo "<a href='index.php?action=multidoc'>Multi DOC<a>";
    echo "</div>";

    echo "<div style='width: 200px'>";
    echo "<a href='index.php?action=doc2pdf'>Doc To PDF<a>";
    echo "</div>";

    echo "<div style='width: 200px'>";
    echo "<a href='index.php?action=view'>VIEW<a>";
    echo "</div>";

    echo "</div>";
}
