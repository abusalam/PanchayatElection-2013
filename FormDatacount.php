<?php

use PanchayatElection as PE;
use PanchayatElection\DB as DB;

$lessB = '';
$lessM = '';
$lessNM = '';
$lessDG = '';
$Ferror = 0;
$Uerror = 0;
require_once('functions.php');
PE\srer_auth();
$Data = new DB();
if (!isset($_SESSION['StepDesc']))
  $_SESSION['StepDesc'] = "Personnel Data Entry";
if (!isset($_SESSION["PostData"]))
  $_SESSION["PostData"] = array();
if (Pe\GetVal($_POST, 'CmdMode') !== NULL) {
  $_SESSION['StepDesc'] = Pe\GetVal($_POST, 'CmdMode');
  $_SESSION['Step'] = substr($_SESSION['StepDesc'], 0, 1);
  $_SESSION["PostData"] = array();
}
$_SESSION['PostData'] = PE\InpSanitize($_POST);
$_SESSION['Msg'] = '';
switch (PE\GetVal($_SESSION, "Step")) {
  case 'A':
    if (PE\GetVal($_POST, 'AppSubmit') === "Save") {

      if (strlen($_SESSION['PostData']['off_code_cp']) === 8) {


        if (($_SESSION['PostData']['BASICpay'] < 6600) || (is_numeric($_SESSION['PostData']['BASICpay']) == FALSE)) {
          $Ferror = 1;
          $lessB = " Basic Pay Can not less than 6600!";
        }
        if (strlen($_SESSION['PostData']['officer_nm']) === 0) {
          $Ferror = 1;
          $lessNM = " Name of Employee can not be blank!";
        }

        if (strlen($_SESSION['PostData']['OFF_DESC']) === 0) {
          $Ferror = 1;
          $lessDG = " Office Designation can not be blank!";
        }
        // elseif (!is_numeric($_SESSION['PostData']['mobile']) && strlen($_SESSION['PostData']['mobile'])<10){
        if ((strlen($_SESSION['PostData']['mobile']) < 10) || (is_numeric($_SESSION['PostData']['mobile']) == FALSE)) {
          $Ferror = 1;
          $lessM = " Mobile No must be 10 digit numeric field!";
        }
        if ($Ferror === 0) {
          $Data->do_ins_query("Insert Into " . MySQL_Pre . "count_personnel(`off_code_cp`, `officer_nm`, `OFF_DESC`, `gender`,"
                  . " `BASICpay`, `STATUS`, `OFFBLOCK_CODE`, `HOMEBLOCK_CODE`, `mobile`)"
                  . " VALUES ('{$_SESSION['PostData']['off_code_cp']}','{$_SESSION['PostData']['officer_nm']}','{$_SESSION['PostData']['OFF_DESC']}',"
                  . "'{$_SESSION['PostData']['gender']}','{$_SESSION['PostData']['BASICpay']}','{$_SESSION['PostData']['STATUS']}',"
                  . "'{$_SESSION['BlockCode']}','{$_SESSION['PostData']['HOMEBLOCK_CODE']}',"
                  . "'{$_SESSION['PostData']['mobile']}')");
        }
      }
      if ($Data->RowCount > 0) {
        $_SESSION['Msg'] = "Personnel Added Successfully!";
        $_SESSION['PostData']['mobile'] = '';
        $_SESSION['PostData']['HOMEBLOCK_CODE'] = '';
        $_SESSION['PostData']['BASICpay'] = '';
        $_SESSION['PostData']['OFF_DESC'] = '';
        $_SESSION['PostData']['officer_nm'] = '';
        $_SESSION['PostData']['gender'] = '';
        $_SESSION['PostData']['STATUS'] = '';
      }
      else
        $_SESSION['Msg'] = "Unable to Insert! " . $lessB . $lessM . $lessNM . $lessDG;
    }
    break;
  case 'U':
    if ((PE\GetVal($_POST, 'AppSubmit') === "Update")) {
      if (($_SESSION['PostData']['BASICpay'] < 6600) || (is_numeric($_SESSION['PostData']['BASICpay']) == FALSE)) {
        $Uerror = 1;
        $lessB = " Basic Pay Can not less than 6600!";
      }
      if (strlen($_SESSION['PostData']['officer_nm']) === 0) {
        $Uerror = 1;
        $lessNM = " Name of Employee can not be blank!";
      }
      if (strlen($_SESSION['PostData']['OFF_DESC']) === 0) {
        $Uerror = 1;
        $lessDG = " Office Designation can not be blank!";
      }
      if ((strlen($_SESSION['PostData']['mobile']) < 10) || (is_numeric($_SESSION['PostData']['mobile']) == FALSE)) {
        $Uerror = 1;
        $lessM = " Mobile No must be 10 digit numeric field!";
      }
      if ($Uerror === 0) {
        $Qry = "Update " . MySQL_Pre . "count_personnel set officer_nm='{$_SESSION['PostData']['officer_nm']}',OFF_DESC='{$_SESSION['PostData']['OFF_DESC']}',"
                . " STATUS='{$_SESSION['PostData']['STATUS']}',HOMEBLOCK_CODE='{$_SESSION['PostData']['HOMEBLOCK_CODE']}',"
                . " BASICpay='{$_SESSION['PostData']['BASICpay']}',mobile='{$_SESSION['PostData']['mobile']}',gender='{$_SESSION['PostData']['gender']}' "
                . " Where PersSL={$_SESSION['PostData']['PersSL']} AND OFFBLOCK_CODE='{$_SESSION['BlockCode']}'";
        $Data->do_ins_query($Qry);
        if ($Data->RowCount > 0) {
          $_SESSION['Msg'] = "Personnel Updated Successfully!";
        } else {
          $_SESSION['Msg'] = "Unable to Update!" . $lessB . $lessM . $lessNM . $lessDG;
        }
      } elseif (PE\GetVal($_POST, 'AppSubmit') === "Delete") {
        $Qry = "Update " . MySQL_Pre . "count_personnel set Deleted=1 Where PersSL='" . PE\GetVal($_POST, 'PersSL') . "'";
        $Data->do_ins_query($Qry);
        if ($Data->RowCount > 0) {
          $_SESSION['Msg'] = "Personnel Deleted Successfully!";
        }
        else
          $_SESSION['Msg'] = "Unable to Delete!";
      }
      if (PE\GetVal($_POST, 'AppSubmit') === "Show") {
        $Qry = "Select P.officer_nm,OFF_DESC,STATUS,HOMEBLOCK_CODE,BASICpay,gender,P.mobile,PersSL,P.off_code_cp" .
                " from " . MySQL_Pre . "count_personnel P LEFT JOIN " . MySQL_Pre . "office O ON (O.off_code=P.off_code_cp)" .
                " where  P.OFFBLOCK_CODE='{$_SESSION['BlockCode']}' AND P.PersSL='" . PE\GetVal($_POST, 'PersSL') . "'";
        $Data->do_sel_query($Qry);
        if ($Data->RowCount > 0) {
          $Row = $Data->get_row();
          $_SESSION['PostData'] = $Row;
        } elseif ($Data->RowCount > 1) {
          $_SESSION['Msg'] = " Duplicate Personnel Found!";
        } else {
          $_SESSION['Msg'] = " Personnel Not Found!";
        }
      } else {
        if (PE\GetVal($_POST, 'AppSubmit') === "Save") {
          $_SESSION['PostData'] = PE\InpSanitize($_POST);
        }
      }
    }
    break;
}
?>