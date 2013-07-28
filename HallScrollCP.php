<?php

use PanchayatElection as PE;

require_once('functions.php');
include_once 'MyPDF.php';
session_start();
//$_SESSION['BlockCode'] = 'B01';
$RowHeight = 10;
$pdf = new PDF('L');
$pdf->SetMargins(15, 5);
$Data = new PE\DB();
$Query = 'Select `PerCode`,`OfficerName`,`MobileNo`,`OfficeBlock`,`Desg`,`Post`,`CountingHall`,`TableNo`,`GroupID`'
        . ' from ' . MySQL_Pre . 'CP_Decoding '
        . ' Where AssemblyCode=\'' . PE\GetVal($_SESSION, 'BlockCode') . '\''
        . ' Order By `PerCode`';
$Data->do_sel_query($Query);
$pdf->AddPage();
PrintHeading($pdf, $RowHeight);
$Posts[1] = 'Counting Officer';
$Posts[2] = 'Counting Assistant';
while ($Row = $Data->get_row()) {
  $h = ($pdf->maxln * $pdf->fh + $RowHeight);
  if (($pdf->GetY() + $h) > $pdf->PageBreakTrigger) {
    $pdf->AddPage();
    PrintHeading($pdf, $RowHeight);
  }
  $pdf->SetFont('Arial', '', 14);
  $pdf->Cell(150, $RowHeight, $Row['PerCode'] . "    " . $Row['OfficerName'], 'LB', 0, "L");
  $pdf->Cell(55, $RowHeight, " " . $Posts[$Row['Post']], 'B', 0, "L");
  $pdf->Cell(35, $RowHeight, $Row['CountingHall'], 'B', 0, "C");
  $pdf->Cell(0, $RowHeight, " " . $Row['TableNo'], 'BR', 1, "C");
}
//$pdf->Output();
$pdf->Output("DecodingCP.pdf", "D");
unset($pdf);
exit();

function PrintHeading(&$pdf, $RowHeight) {
  $pdf->SetFont('Arial', 'B', 14);
  $pdf->Cell(0, $RowHeight, "Counting Personnel Decoding List", 0, 1, "C");
  $pdf->Cell(150, $RowHeight, 'Name of Counting Personnel', 1, 0, "C");
  $pdf->Cell(55, $RowHeight, 'Post', 1, 0, "C");
  $pdf->Cell(35, $RowHeight, 'Counting Hall', 1, 0, "C");
  $pdf->Cell(0, $RowHeight, 'Table No', 1, 1, "C");
}

?>
