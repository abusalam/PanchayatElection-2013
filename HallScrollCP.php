<?php

use PanchayatElection as PE;

require_once('functions.php');
include_once 'MyPDF.php';
session_start();
//$_SESSION['BlockCode'] = 'B01';
$pdf = new PDF('L');
$Data = new PE\DB();
$Query = 'Select `PerCode`,`OfficerName`,`MobileNo`,`OfficeBlock`,`Desg`,`Post`,`CountingHall`,`TableNo`,`GroupID`'
        . ' from ' . MySQL_Pre . 'CP_Decoding '
        . ' Where AssemblyCode=\'' . PE\GetVal($_SESSION, 'BlockCode') . '\''
        . ' Order By `PerCode`';
$Data->do_sel_query($Query);
$pdf->AddPage();
PrintHeading($pdf);
$Posts[1] = 'Counting Officer';
$Posts[2] = 'Counting Assistant';
while ($Row = $Data->get_row()) {
  $h = ($pdf->maxln * $pdf->fh + 6);
  if (($pdf->GetY() + $h) > $pdf->PageBreakTrigger) {
    $pdf->AddPage();
    PrintHeading($pdf);
  }
  $pdf->SetFont('Arial', '', 9);
  $pdf->Cell(70, 6, '[' . $Row['PerCode'] . "]  " . $Row['OfficerName'], 'LB', 0, "L");
  $pdf->Cell(20, 6, $Row['MobileNo'], 'B', 0, "C");
  $pdf->Cell(45, 6, $Row['OfficeBlock'], 'B', 0, "C");
  $pdf->Cell(50, 6, " " . $Row['Desg'], 'B', 0, "L");
  $pdf->Cell(25, 6, " " . $Posts[$Row['Post']], 'B', 0, "L");
  $pdf->Cell(25, 6, $Row['CountingHall'], 'B', 0, "C");
  $pdf->Cell(17, 6, " " . $Row['TableNo'], 'B', 0, "C");
  $pdf->Cell(0, 6, " " . $Row['GroupID'], 'BR', 1, "C");
}
$pdf->Output();
//$pdf->Output("ElectionNotice.pdf", "D");
unset($pdf);
exit();

function PrintHeading(&$pdf) {
  $pdf->SetFont('Arial', 'B', 12);
  $pdf->Cell(0, 8, "Counting Personnel Decoding List - [For Testing Do Not Print]", 0, 1, "C");
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(70, 6, 'Name of Counting Personnel', 1, 0, "C");
  $pdf->Cell(20, 6, 'Mobile No', 1, 0, "C");
  $pdf->Cell(45, 6, 'Office Block', 1, 0, "C");
  $pdf->Cell(50, 6, 'Designation', 1, 0, "C");
  $pdf->Cell(25, 6, 'Post', 1, 0, "C");
  $pdf->Cell(25, 6, 'Counting Hall', 1, 0, "C");
  $pdf->Cell(17, 6, 'Table No', 1, 0, "C");
  $pdf->Cell(0, 6, 'Group', 1, 1, "C");
}

?>
