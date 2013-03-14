<?php
use PanchayatElection as PE;
use PanchayatElection\DB as DB;
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
switch (PE\GetVal($_SESSION,"Step")) {
	case 'A':
		if (PE\GetVal($_POST, 'AppSubmit') === "Show") {
			$Qry="Select O.off_code,office,address1,totstaff,OldOffCode,count(*) as `ActStaff` " .
					"from " . MySQL_Pre . "office O INNER JOIN " . MySQL_Pre . "personnel P ON (O.off_code=P.off_code)" .
					"where blockmuni='{$_SESSION['BlockCode']}' AND OldOffCode='{$_SESSION['SubDivn']}".PE\GetVal($_POST, 'off_code')."'";
			$Data->do_sel_query($Qry);
			if ($Data->RowCount>0) {
				$Row=$Data->get_row();
				$_SESSION['PostData'] = $Row;
			}
			else{
				$_SESSION['Msg']=$Qry;
			}
		} else
		if (PE\GetVal($_POST, 'AppSubmit') === "Save") {
			$_SESSION['PostData'] = PE\InpSanitize($_POST);
		}
		break;
	case 'U':
		if (PE\GetVal($_POST, 'AppSubmit') === "Save and Show Next"){
			
		}elseif (PE\GetVal($_POST, 'AppSubmit') === "Save and Show Previous"){
			
		}elseif (PE\GetVal($_POST, 'AppSubmit') === "Delete"){
			
		}
		
		if (PE\GetVal($_POST, 'AppSubmit') === "Show") {
			$Data->do_sel_query("Select office,officer_nm,date_ob,pay,scalecode " .
					"from " . MySQL_Pre . "office O INNER JOIN " . MySQL_Pre . "personnel P ON (O.off_code=P.off_code)" .
					" where  blockmuni='{$_SESSION['BlockCode']}' AND per_code='{$_SESSION['SubDivn']}".PE\GetVal($_POST, 'per_code')."'");
			if ($Data->RowCount>0) {
				$Row=$Data->get_row();
				$_SESSION['PostData'] = $Row;
			}
		} else
		if (PE\GetVal($_POST, 'AppSubmit') === "Save") {
			$_SESSION['PostData'] = PE\InpSanitize($_POST);
		}
		break;
}
?>