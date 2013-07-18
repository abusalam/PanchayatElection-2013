<?php

include_once 'MyPDF.php';
session_start();
$pdf = new PDF();
$Data = new PanchayatElection\DB();
$Query = "Select * from " . MySQL_Pre . "OfficeWiseScroll "
        . " Where block_municd='" . \PanchayatElection\GetVal($_SESSION, 'BlockCode') . "'";
$Data->do_sel_query($Query);

$pdf->SetTitle("");
/* $pdf->SetFont('Arial', 'B', 12);
  $pdf->Cell(0, 4, "Attendance Sheet", 0, 1, "C");
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->Cell(0, 5, "Staff Recruitment Examination 2013", 0, 1, "C");
  $pdf->Cell(0, 4, "Paschim Medinipur Judgeship", 0, 1, "C");
  $pdf->Ln(4); */
$x = 0;
$y = 0;
$h = 22;
$w = 60;
$cpp = 3;
$rpp = 12;
$m = 15;
$Office = "";
while ($Row = $Data->get_row()) {
//while ($y < 100) {
  //$pdf->Rect($x + $m, $y + $m, $w, $h);
  //$pdf->SetXY($x + $m + 18, $y + $m + 1);

  if ($Office !== $Row['office']) {
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, "Notice", 0, 1, "C");
    $pdf->Cell(0, 3, "==========", 0, 1, "C");
    $pdf->Ln(2);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Write(6, "      Following polling personnel are informed that in pursuance of "
            . "Order No. 1714(17)-SEC/1E-70/2012 (Pt.-II) "
            . "dated 29/06/2013 of the Secretary, West Bengal State Election Commission, the date of "
            . "Poll for Panchayat General Election 2013 in Paschim Medinipur District "
            . "has been  rescheduled from 02/07/2013 to 11/07/2013. "
            . "Polling Personnel are directed to report to the Distribution Centre on 10/07/2013 at "
            . "the schedule time in place of 01/07/2013. All other instructions remain same.");
    $pdf->Ln(7);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, "Block: " . $Row['block_muni_nm'], 0, 1, "L");
    //$pdf->Cell(0, 6, "To,", 0, 1, "L");
    //$pdf->Cell(0, 6, "__________________________", 0, 1, "L");
    //$pdf->Cell(0, 6, "__________________________", 0, 1, "L");
    $pdf->Cell(0, 6, "Office: " . $Row['office'], 0, 1, "L");
    $pdf->Ln(3);
    $pdf->Cell(0, 6, "PIN         - Name of Polling Personnel", "TB", 1, "L");
  }
  $h = ($pdf->maxln * $pdf->fh + 6);
  if (($pdf->GetY() + $h) > $pdf->PageBreakTrigger) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, "PIN         - Name of Polling Personnel", "TB", 1, "L");
  }
  $pdf->SetFont('Arial', '', 8);
  $pdf->Cell(40, 6, $Row['per_code'] . " - " . $Row['officer_nm'], 0, 1, "L");

  $Office = $Row['office'];

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
$pdf->Output("ElectionNotice.pdf", "D");
unset($pdf);
exit();
?>
