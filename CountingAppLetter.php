<?php

include_once 'MyPDF.php';
include_once 'functions.php';
session_start();
//$_SESSION['BlockCode'] = 'B01';
$h = 80;
$w = 180;
$gap = 10;
$pdf = new PDF();
$Data = new PanchayatElection\DB();
switch (PanchayatElection\GetVal($_POST, 'CmdSubmit')) {
  case "Office-wise Corrigendum Letters":
    $Query = "Select * from " . MySQL_Pre . "Appointments "
            . " Where OfficeCode='" . PanchayatElection\GetVal($_POST, 'off_code')
            . "' AND BlockCode='" . PanchayatElection\GetVal($_SESSION, 'BlockCode') . "'";
    $FileName = 'Corrigendum-[' . PanchayatElection\GetVal($_POST, 'off_code') . '].pdf';
    break;
  case "Single Corrigendum Letter":
    $Query = "Select * from " . MySQL_Pre . "Appointments "
            . " Where PerCode='" . PanchayatElection\GetVal($_POST, 'PerCode')
            . "' AND BlockCode='" . PanchayatElection\GetVal($_SESSION, 'BlockCode') . "'";
    $FileName = 'SingleCorrigendum-[' . PanchayatElection\GetVal($_POST, 'PerCode') . '].pdf';
    break;
  default:
    $Query = "Select * from " . MySQL_Pre . "Appointments "
            . " Where BlockCode='" . PanchayatElection\GetVal($_SESSION, 'BlockCode') . "' Order by OfficeCode";
    $FileName = 'AllCorrigendum-[' . PanchayatElection\GetVal($_SESSION, 'BlockCode') . '].pdf';
    break;
}
$Data->do_sel_query($Query);

$pdf->SetTitle("");
$pdf->SetMargins(15, 5);
$Office = "";
while ($Row = $Data->get_row()) {

  if ($Office !== $Row['OfficeCode']) {
    $pdf->AddPage();
    //$pdf->Cell(0, 6, "PIN         - Name of Polling Personnel", "TB", 1, "L");
  } else if (($pdf->GetY() + $h) > $pdf->PageBreakTrigger) {
    $pdf->AddPage();
  }

  $x = $pdf->GetX();
  $y = $pdf->GetY();
  $pdf->Image('Signature/' . $Row['Sign'] . '.jpg', $x + $w - 35, $y + $h - 25, 30);
  $pdf->Rect($x, $y - 1, $w, $h);
  $pdf->SetXY($x, $y);
  $pdf->SetFont('Arial', 'BU', 12);
  $pdf->Cell(0, 5, "Revised Order of Appointment of Presiding and Polling Officers", 0, 1, "C");
  $pdf->Cell(0, 5, "General Election to the Gram Panchayats, Panchayat Samities and Zilla Parishad, 2013", 0, 1, "C");
  $pdf->Ln(2);
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->Cell(0, 4, "Block: " . $Row['OfficeBlock'], 0, 1, "L");
  $pdf->Cell(0, 4, "Office: " . $Row['Office'] . ' [' . $Row['OfficeCode'] . ']', 0, 1, "L");
  $pdf->Ln(2);
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(40, 6, 'To', 0, 1, "L");
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->Cell(40, 4, $Row['PPName'] . ' [' . $Row['PerCode'] . ']', 0, 1, "L");
  $pdf->Cell(40, 4, 'Party No: ' . $Row['GroupID'] . ' [' . $Row['PostStatus'] . ']', 0, 1, "L");
  $pdf->Ln(2);
  $pdf->SetFont('Arial', '', 8);
  $pdf->Write(4, "      In partial modification of the appointment order issued earlier, you are hereby"
          . " informed that the poll will be taken on 11/07/2013 during the hours 7 AM to 5 PM in"
          . " place of 02/07/2013. Consequently you are directed to attend Distribution Centre at "
          . "{$Row['DCRCVenue']}, {$Row['DCRCAddress']} on 10/07/2013 within 9 AM for Collection "
          . "of materials. Other instructions remain the same.");

  $pdf->SetFont('Arial', 'B', 8);
  $pdf->SetY($y + $h - 10);
  $pdf->Cell(30, 4, 'Place: Paschim Medinipur', 0, 1, "L");
  $pdf->Cell(30, 6, 'Date: ' . date('d/m/Y'), 0, 1, "L");

  $pdf->SetXY($x + $w - 35, $y + $h - 10);
  $pdf->Cell(30, 4, 'Panchayat Returning Officer', 0, 1, "C");
  $pdf->SetX($x + $w - 35);
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
$pdf->Output($FileName, 'D');
unset($pdf);
exit();
?>
