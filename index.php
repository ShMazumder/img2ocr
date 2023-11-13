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
} else if (strcasecmp($action, "extract_phone") === 0) {
    require "extract_phone.php";
} else if (strcasecmp($action, "VIEW") === 0) {
    require "view.php";
} else if (strcasecmp($action, "IMAGE2TEXT") === 0 || (isset($_POST['action']) && strcasecmp($_POST['action'], "IMAGE2TEXT")===0)) {

    if (!isset($_FILES["fileToUpload"])) {
        // var_dump($_GET);
        var_dump($_FILES);
        exit();
    }
    if ($_FILES["fileToUpload"]["error"] > 0)
        echo "Error: " . $_FILES["fileToUpload"]["error"] . "<br />";
    else {
        echo "Upload: " . $_FILES["fileToUpload"]["name"] . "<br />";
        echo "Type: " . $_FILES["fileToUpload"]["type"] . "<br />";
        echo "Size: " . ($_FILES["fileToUpload"]["size"] / 1024) . " Kb<br />";
        echo "Stored in: " . $_FILES["fileToUpload"]["tmp_name"];

        $target_dir = "uploads/".date('Y-m-d H-i-s')."";
        $target_file = $target_dir .DIRECTORY_SEPARATOR. basename($_FILES["fileToUpload"]["name"]);
        if(!file_exists($target_dir)){
            mkdir($target_dir, 0777, true);
        }

        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

        require "extract_text.php";
    }
} else {
    // upload options?

    // echo "<div style='width:100%; display:flex; justify-content:around;'>";

    // echo "<div style='width: 200px'>";
    // echo "<a href='index.php?action=ocr'>OCR<a>";
    // echo "</div>";

    // echo "<div style='width: 200px'>";
    // echo "<a href='index.php?action=pdf'>PDF<a>";
    // echo "</div>";

    // echo "<div style='width: 200px'>";
    // echo "<a href='index.php?action=doc'>DOC<a>";
    // echo "</div>";

    // echo "<div style='width: 200px'>";
    // echo "<a href='index.php?action=multidoc'>Multi DOC<a>";
    // echo "</div>";

    // echo "<div style='width: 200px'>";
    // echo "<a href='index.php?action=doc2pdf'>Doc To PDF<a>";
    // echo "</div>";

    // echo "<div style='width: 200px'>";
    // echo "<a href='index.php?action=extract_phone'>Extract Phone<a>";
    // echo "</div>";

    // echo "<div style='width: 200px'>";
    // echo "<a href='index.php?action=view'>VIEW<a>";
    // echo "</div>";

    // echo "</div>";

    // 

    // echo "<form action='index.php' method='post' enctype='multipart/form-data'>";
    // echo "<input name='fileToUpload' id='fileToUpload' type='file' required>";
    // echo "<input type='submit' name='action' value='IMAGE2TEXT'/>";
    // echo "</form>";

    echo '<form action="index.php" method="post" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" name="action" value="IMAGE2TEXT"/>
        </form>';
}
