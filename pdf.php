<?php

require __DIR__ . "/vendor/fpdf186/fpdf.php";


class PDF extends FPDF
{
    function Header()
    {
        global $title;

        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Calculate width of title and position
        $w = $this->GetStringWidth($title) + 6;
        $this->SetX((210 - $w) / 2);
        // Colors of frame, background and text
        $this->SetDrawColor(0, 80, 180);
        $this->SetFillColor(230, 230, 0);
        $this->SetTextColor(220, 50, 50);
        // Thickness of frame (1 mm)
        $this->SetLineWidth(1);
        // Title
        $this->Cell($w, 9, $title, 1, 1, 'C', true);
        // Line break
        $this->Ln(10);
    }

    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Text color in gray
        $this->SetTextColor(128);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function ChapterTitle($num, $label)
    {
        // Arial 12
        $this->SetFont('Times', '', 13);
        // Background color
        $this->SetFillColor(200, 220, 255);
        // Title
        $this->Cell(0, 6, "Chapter $num : $label", 0, 1, 'L', true);
        // Line break
        $this->Ln(4);
    }

    function ChapterBody($file)
    {
        // Read text file
        $txt = file_get_contents($file);
        // Times 12
        $this->SetFont('Times', '', 10);
        // Output justified text
        $this->MultiCell(0, 5, $txt);
        // Line break
        $this->Ln();
        // Mention in italics
        $this->SetFont('', 'I');
        $this->Cell(0, 5, '');
        // $this->Cell(0, 5, '(end of excerpt)');
    }

    function PrintChapter($num, $title, $file)
    {
        $this->AddPage();
        $this->ChapterTitle($num, $title);
        $this->ChapterBody($file);
    }
}


$inputDir = "output";
$outputDir = "output_pdf";

$list = scandir($inputDir);


$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetAuthor('Mahazabin Sharmin Pia');

$chapterCount = 0;
for ($fileIndex = 1; $fileIndex <= count($list); $fileIndex++) {
    # code...

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

    $pdf->PrintChapter(++$chapterCount, "File: " . $fileName, $txt_path);
    // $pdf->PrintChapter(2, 'THE PROS AND CONS', '20k_c2.txt');

}

$title = $chapterCount . ' images';
$pdf->SetTitle($title);

if (!file_exists($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$outputFileName = $outputDir . DIRECTORY_SEPARATOR . $title . '.pdf';
$pdf->Output('F', $outputFileName);

echo "<a target='_blank' href='$outputFileName'>See file</a>";
