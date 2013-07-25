<?php

include_once 'MyPDF.php';
include_once 'functions.php';
session_start();
$_SESSION['BlockCode'] = 'M01';
$h = 84;
$w = 190;
$gap = 4;
$pdf = new PDF();
$Data = new PanchayatElection\DB();
switch (PanchayatElection\GetVal($_POST, 'CmdSubmit')) {
  case "Office-wise Corrigendum Letters":
    $Query = "Select * from " . MySQL_Pre . "CP_Appointments "
            . " Where OfficeCode='" . PanchayatElection\GetVal($_POST, 'off_code')
            . "' AND BlockCode='" . PanchayatElection\GetVal($_SESSION, 'BlockCode') . "'";
    $FileName = 'OffAppLetterCP-[' . PanchayatElection\GetVal($_POST, 'off_code') . '].pdf';
    break;
  case "Single Corrigendum Letter":
    $Query = "Select * from " . MySQL_Pre . "CP_Appointments "
            . " Where PerCode='" . PanchayatElection\GetVal($_POST, 'PerCode')
            . "' AND BlockCode='" . PanchayatElection\GetVal($_SESSION, 'BlockCode') . "'";
    $FileName = 'SingleAppLetterCP-[' . PanchayatElection\GetVal($_POST, 'PerCode') . '].pdf';
    break;
  default:
    $Query = "Select * from " . MySQL_Pre . "CP_Appointments "
            . " Where BlockCode='" . PanchayatElection\GetVal($_SESSION, 'BlockCode') . "' Order by OfficeCode";
    $FileName = 'AllAppLetterCP-[' . PanchayatElection\GetVal($_SESSION, 'BlockCode') . '].pdf';
    break;
}
$Data->do_sel_query($Query);

$pdf->SetTitle("");
$pdf->SetMargins(10, 4);
$Office = "";
while ($Row = $Data->get_row()) {

  if ($Office !== $Row['OfficeCode']) {
    $pdf->AddPage();
    //$pdf->Cell(0, 6, "PIN         - Name of Polling Personnel", "TB", 1, "L");
  } else if (($pdf->GetY() + $h) > $pdf->PageBreakTrigger) {
    $pdf->AddPage();
  }
  if ($Row['Sex'] === '01') {
    $OfficerName = 'Shri ' . $Row['OfficerName'];
  } else {
    $OfficerName = 'Smt ' . $Row['OfficerName'];
  }
  if ($Row['Post'] === 1) {
    $Post = 'Counting Officer';
  } else {
    $Post = 'Counting Assistant';
  }

  $x = $pdf->GetX();
  $y = $pdf->GetY();
  $pdf->Image('Signature/' . $Row['Sign'] . '.jpg', $x + $w - 38, $y + $h - 20, 30);
  $pdf->Rect($x, $y - 1, $w, $h);
  $pdf->SetXY($x, $y);
  $pdf->SetFont('Arial', 'B', 8);
  //$pdf->Cell(0, 4, 'FORMAT', 0, 1, "C");
  $pdf->Cell(0, 4, '(Under Rule 97)', 0, 1, "C");
  $pdf->SetFont('Arial', 'BU', 11);
  $pdf->Cell(0, 5, 'Appointment of Counting Officers / Assistants', 0, 1, "C");
  $pdf->Cell(0, 5, 'Election to ' . $Row['BlockName'] . ' Gram Panchayat / Panchayat Samity / Zilla Parishad', 0, 1, "C");
  $pdf->Ln(2);

  $pdf->Ln(2);
  $pdf->SetFont('Arial', '', 8);
  $pdf->Write(4, '       I, the Returning Officer of the above mentioned Constituency(s) do hereby appoint the persons'
          . ' whose names are specified below to act as ' . $Post . ' and to attend the counting venue'
          . ' as mentioned below at 06:30 hours on 29/07/2013 for the purpose of assisting me in the counting of votes'
          . ' at the said election(s).');
  $pdf->Ln(5);
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->Cell(0, 6, $OfficerName . ' [' . $Row['PerCode'] . ']' . ', ' . $Post, 0, 1, "C");
  $pdf->Ln(2);
  $pdf->Cell(40, 6, 'Counting Venue: ' . $Row['VenueDCRC'] . ', ' . $Row['AddressDCRC'], 0, 1, "L");
  $pdf->SetFont('Arial', '', 8);
  $pdf->Cell(40, 4, 'Training Venue: ' . $Row['TrVenue'], 0, 1, "L");
  $pdf->Cell(40, 4, 'Training Date: ' . $Row['TrDate'] . ' Time:' . $Row['TrTime'], 0, 1, "L");
  $pdf->Ln(2);
  $pdf->SetFont('Arial', '', 8);
  $pdf->Write(3, 'N.B.: Counting Personnel are instructed not to bring Mobile phones or other electronic gadgets to the'
          . ' Counting Venue. Counting Personnel are instructed to carry their EPIC cards for identification and to '
          . ' bring one Passport size photo for pasting on Identity Card.');

  $pdf->SetY($y + $h - 15);
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->Cell(30, 6, 'Office: ' . $Row['OfficeName'], 0, 1, "L");
  $pdf->Cell(30, 4, 'Place: Paschim Medinipur', 0, 1, "L");
  $pdf->Cell(30, 4, 'Date: ' . date('d/m/Y'), 0, 1, "L");

  $pdf->SetXY($x + $w - 38, $y + $h - 10);
  $pdf->Cell(30, 4, 'Panchayat Returning Officer', 0, 1, "C");
  $pdf->SetX($x + $w - 38);
  $pdf->Cell(30, 4, '& B.D.O. ' . $Row['AssemblyName'], 0, 1, "C");
  $pdf->Ln(7);
  $pdf->SetY($y + $h + $gap);
  $Office = $Row['OfficeCode'];

  //$pdf->SetX($x + $m + 18);
  //$pdf->MemImage($Row['Photo'], $x + $m + 1, $y + $m + 1, 15);
  /* $x+=$w;

    if ($x == $w * $cpp)
    $y+=$h;
    $x%=$w * $cpp;

    if ($y == $h * $rpp)
    $pdf->AddPage();
    $y%=$h * $rpp; */
}
$pdf->Output();
//$pdf->Output($FileName, 'D');
unset($pdf);
exit();
?>
