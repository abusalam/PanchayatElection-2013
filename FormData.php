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
if ((PE\GetVal($_POST, 'AppSubmit') === "Save")||(PE\GetVal($_POST, 'AppSubmit') === "Update")) {
	$_SESSION['PostData'] = PE\InpSanitize($_POST);
}
switch (PE\GetVal($_SESSION,"Step")) {
	case 'A':
		if (PE\GetVal($_POST, 'AppSubmit') === "Show") {
			$Qry="Select O.off_code,office,address1,totstaff,OldOffCode,count(*) as `ActStaff` " .
					"from " . MySQL_Pre . "office O INNER JOIN " . MySQL_Pre . "personnel P ON (O.off_code=P.off_code)" .
					"where blockmuni='{$_SESSION['BlockCode']}' AND OldOffCode='{$_SESSION['SubDivn']}".PE\GetVal($_POST, 'off_code')."' AND NOT Deleted";
			$Data->do_sel_query($Qry);
			if ($Data->RowCount>0) {
				$Row=$Data->get_row();
				$_SESSION['PostData'] = $Row;
				if ($Row['ActStaff']==$Row['totstaff']){
					$_SESSION['Step']=NULL;
					$_SESSION['Msg']=" No more Personnel can be added!";
				}
			}
			else{
				$_SESSION['Msg']=" Office Not Found!";
			}
		}
		
		if (PE\GetVal($_POST, 'AppSubmit') === "Save"){
			$Data->do_ins_query("Insert Into " . MySQL_Pre . "personnel(`off_code`, `officer_nm`, `present_ad1`, `date_ob`,"
				." `scalecode`, `pay`, `epic`, `assembly_temp`, `assembly_off`, `mobile`, `remarks`, `HB`, `BlockCode`)"
				." VALUES ('{$_SESSION['SubDivn']}{$_SESSION['PostData']['off_code']}','{$_SESSION['PostData']['officer_nm']}','{$_SESSION['PostData']['present_ad1']}',"
				."'{$_SESSION['PostData']['date_ob']}','{$_SESSION['PostData']['scalecode']}',{$_SESSION['PostData']['pay']},"
				."'{$_SESSION['PostData']['epic']}','{$_SESSION['PostData']['assembly_temp']}','{$_SESSION['PostData']['assembly_off']}',"
				."{$_SESSION['PostData']['mobile']},'{$_SESSION['PostData']['remarks']}','{$_SESSION['PostData']['HB']}','{$_SESSION['BlockCode']}')");
			if ($Data->RowCount>0) {
				$Data->do_ins_query("Update " . MySQL_Pre . "personnel SET OldPerCode=CONCAT('{$_SESSION['SubDivn']}',`PersSL`),"
							. "per_code=CONCAT('{$_SESSION['SubDivn']}',`PersSL`) Where OldPerCode IS NULL");
				$_SESSION["PostData"] = array();
				$_SESSION['Msg']="Personnel Added Successfully!";
			}
			else
				$_SESSION['Msg']="Unable to Insert!";
		}
		break;
	case 'U':
		if (PE\GetVal($_POST, 'AppSubmit') === "Save and Show Next"){
			
		}elseif (PE\GetVal($_POST, 'AppSubmit') === "Save and Show Previous"){
			
		}elseif ((PE\GetVal($_POST, 'AppSubmit') === "Update")&&(PE)){
			$Qry="Update " . MySQL_Pre . "personnel set HB='{$_SESSION['PostData']['HB']}',epic='{$_SESSION['PostData']['epic']}',"
					. "assembly_temp='{$_SESSION['PostData']['assembly_temp']}',assembly_off='{$_SESSION['PostData']['assembly_off']}',"
					. "pay='{$_SESSION['PostData']['pay']}',mobile='{$_SESSION['PostData']['mobile']}',date_ob='{$_SESSION['PostData']['date_ob']}',"
					. "remarks='{$_SESSION['PostData']['remarks']}',present_ad1='{$_SESSION['PostData']['present_ad1']}',"
					. "scalecode='{$_SESSION['PostData']['scalecode']}',officer_nm='{$_SESSION['PostData']['officer_nm']}'"
					. " Where per_code='{$_SESSION['SubDivn']}".PE\GetVal($_POST, 'per_code')."'";
			$Data->do_ins_query($Qry);
			if ($Data->RowCount>0){
				$_SESSION['Msg']="Personnel Updated Successfully!";
				$_SESSION["PostData"] = array();
			}
			else
				$_SESSION['Msg']="Unable to Update!";
		}elseif (PE\GetVal($_POST, 'AppSubmit') === "Delete"){
			$Qry="Update " . MySQL_Pre . "personnel set Deleted=1 Where per_code='{$_SESSION['SubDivn']}".PE\GetVal($_POST, 'per_code')."'";
			$Data->do_ins_query($Qry);
			if ($Data->RowCount>0){
				$_SESSION['Msg']="Personnel Deleted Successfully!";
				$_SESSION["PostData"] = array();
			}
			else
				$_SESSION['Msg']="Unable to Delete!";
			
		}
		
		if (PE\GetVal($_POST, 'AppSubmit') === "Show") {
			$Qry="Select HB,per_code,office,P.officer_nm,date_ob,pay,scalecode,present_ad1,remarks,P.mobile,epic,assembly_temp,assembly_off " .
					"from " . MySQL_Pre . "office O INNER JOIN " . MySQL_Pre . "personnel P ON (O.off_code=P.off_code)" .
					" where  blockmuni='{$_SESSION['BlockCode']}' AND OldPerCode='{$_SESSION['SubDivn']}".PE\GetVal($_POST, 'per_code')."' AND NOT Deleted";
			$Data->do_sel_query($Qry);
			if ($Data->RowCount>0) {
				$Row=$Data->get_row();
				$_SESSION['PostData'] = $Row;
			}
			else {
				$_SESSION['Msg']=" Personnel Not Found!";
			}
		} else
		if (PE\GetVal($_POST, 'AppSubmit') === "Save") {
			$_SESSION['PostData'] = PE\InpSanitize($_POST);
		}
		break;
}
?>