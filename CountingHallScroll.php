<?php

use PanchayatElection as PE;

require_once('functions.php');
include_once 'MyPDF.php';
session_start();
//$_SESSION['BlockCode'] = 'B01';
$pdf = new PDF();
$pdf->SetMargins(15, 5);
$Data = new PE\DB();
$Query = 'Select `PerCode`,`OfficerName`,`Desg`,`Post`,`CountingHall`,`TableNo`,`GroupID`'
        . ' from ' . MySQL_Pre . 'CP_Decoding '
        . ' Where AssemblyCode=\'' . PE\GetVal($_SESSION, 'BlockCode') . '\''
        . ' Order By CountingHall,TableNo,GroupID,Post';
$Data->do_sel_query($Query);

$pdf->SetTitle("Counting Hall-wise Decoding List");
$Posts[1] = 'Counting Officer';
$Posts[2] = 'Counting Assistant';
$Hall = "";
while ($Row = $Data->get_row()) {
  if ($Hall !== $Row['CountingHall']) {
    $pdf->AddPage();
    PrintHeading($pdf, $Row);
  }
  $h = ($pdf->maxln * $pdf->fh + 10);
  if (($pdf->GetY() + $h) > $pdf->PageBreakTrigger) {
    $pdf->AddPage();
    PrintHeading($pdf, $Row);
  }
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(20, 10, $Row['PerCode'], 'LB', 0, "L");
  $pdf->SetFont('Arial', '', 8);
  $pdf->Cell(60, 10, $Row['OfficerName'], 'B', 0, "L");
  $pdf->Cell(28, 10, " " . $Posts[$Row['Post']], 'B', 0, "L");
  $pdf->SetFont('Arial', 'B', 12);
  $pdf->Cell(17, 10, " " . $Row['TableNo'], 'B', 0, "C");
  $pdf->Cell(0, 10, '', 'LBR', 1, "C");
  $Hall = $Row['CountingHall'];
}
//$pdf->Output();
$pdf->Output("HallDecodingCP.pdf", "D");
unset($pdf);
exit();

function PrintHeading(&$pdf, &$Row) {
  $pdf->SetFont('Arial', 'B', 12);
  $pdf->Cell(0, 10, "Counting Hall-wise Decoding List", 0, 1, "C");
  $pdf->Cell(0, 6, 'Counting Hall: ' . $Row['CountingHall'], 'LRT', 1, "L");
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(80, 6, 'Name of Counting Personnel', 1, 0, "C");
  $pdf->Cell(28, 6, 'Post', 1, 0, "C");
  $pdf->Cell(17, 6, 'Table No', 1, 0, "C");
  $pdf->Cell(0, 6, 'Signature', 1, 1, "C");
}

?>
