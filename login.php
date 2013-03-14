<?php
use PanchayatElection as PE;

require_once('functions.php');

session_start();
$Data = new PE\DB();
$Data->Debug = 1;
$Data->do_max_query("Select 1");
$ID = PE\GetVal($_SESSION, 'ID');
$_SESSION['ID'] = session_id();
if (!isset($_SESSION['LifeTime']))
	$_SESSION['LifeTime'] = time();
$action = PE\CheckAuth();
if ($action == "LogOut") {
	$Data->do_ins_query(
		"INSERT INTO logs (`SessionID`,`IP`,`Referrer`,`UserAgent`,`UserID`,`URL`,`Action`,`Method`,`URI`) values"
		. "('" . $_SESSION['ID'] . "','" . $_SERVER['REMOTE_ADDR'] . "','" .
		$Data->SqlSafe($_SERVER["HTTP_REFERER"]) . "','" . $_SERVER['HTTP_USER_AGENT'] . "','" . PE\
		GetVal($_SESSION, 'UserName') . "','" . $Data->SqlSafe($_SERVER[
		'PHP_SELF']) . "','" . $action . ": (" . $_SERVER['SCRIPT_NAME'] .
		")','" . $Data->SqlSafe($_SERVER['REQUEST_METHOD']) . "','" . $Data->
		SqlSafe($_SERVER['REQUEST_URI']) . "');");
	session_unset();
	session_destroy();
	session_start();
	$_SESSION = array();
	$_SESSION['Debug'] = $_SESSION['Debug'] . $SessRet . "TOKEN-!Valid";
	header("Location: index.php");
	exit;
}
if ($action != "Valid") {
	PE\initSRER();
}
$LogC = 0;
if ((PE\GetVal($_POST, 'UserID') !== NULL) && (PE\GetVal($_POST, 'UserPass') !==
	NULL)) {
	$QueryLogin =
		"Select BlockCode,UserName,sdiv_cd from `".MySQL_Pre."Users` U,".MySQL_Pre."Block_muni B where U.BlockCode=B.block_municd AND `UserID`='" .
		$_POST['UserID'] . "' AND MD5(concat(`UserPass`,MD5('" . $_POST[
		'LoginToken'] . "')))='" . $_POST['UserPass'] . "'";
	$rows = $Data->do_sel_query($QueryLogin);
	if ($rows > 0) {
		session_regenerate_id();
		$Row = $Data->get_row();
		$_SESSION['UserName'] = $Row['UserName'];
		$_SESSION['BlockCode'] = $Row['BlockCode'];
		$_SESSION['SubDivn'] = $Row['sdiv_cd'];
		$_SESSION['ID'] = session_id();
		$_SESSION['FingerPrint'] = md5($_SERVER['REMOTE_ADDR'] . $_SERVER[
			'HTTP_USER_AGENT'] . "KeyLeft");
		$_SESSION['REFERER1'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER[
			'REQUEST_URI'];
		$action = "JustLoggedIn";
		$Data->do_ins_query(
			"Update ".MySQL_Pre."Users Set LoginCount=LoginCount+1 where `UserID`='" .
			$_POST['UserID'] . "' AND MD5(concat(`UserPass`,MD5('" . $_POST[
			'LoginToken'] . "')))='" . $_POST['UserPass'] . "'");
		$Data->do_ins_query(
			"INSERT INTO ".MySQL_Pre."logs (`SessionID`,`IP`,`Referrer`,`UserAgent`,`UserID`,`URL`,`Action`,`Method`,`URI`) values"
			. "('" . $_SESSION['ID'] . "','" . $_SERVER['REMOTE_ADDR'] . "','" .
			mysql_real_escape_string($_SERVER['HTTP_REFERER']) . "','" .
			$_SERVER['HTTP_USER_AGENT'] . "','" . $_SESSION['UserName'] . "','"
			. mysql_real_escape_string($_SERVER['PHP_SELF']) .
			"','Login: Success','" . mysql_real_escape_string($_SERVER[
			'REQUEST_METHOD']) . "','" . mysql_real_escape_string($_SERVER[
			'REQUEST_URI'] . $_SERVER['QUERY_STRING']) . "');");
	} else {
		$action = "NoAccess";
		$Data->do_ins_query(
			"INSERT INTO ".MySQL_Pre."logs (`SessionID`,`IP`,`Referrer`,`UserAgent`,`UserID`,`URL`,`Action`,`Method`,`URI`) values"
			. "('" . $_SESSION['ID'] . "','" . $_SERVER['REMOTE_ADDR'] . "','" .
			mysql_real_escape_string($_SERVER['HTTP_REFERER']) . "','" .
			$_SERVER['HTTP_USER_AGENT'] . "','" . $_POST['UserID'] . "','" .
			mysql_real_escape_string($_SERVER['PHP_SELF']) .
			"','Login: Failed','" . mysql_real_escape_string($_SERVER[
			'REQUEST_METHOD']) . "','" . mysql_real_escape_string($_SERVER[
			'REQUEST_URI'] . $_SERVER['QUERY_STRING']) . "');");
	}
}
$_SESSION['Token'] = md5($_SERVER['REMOTE_ADDR'] . $ID . time());
?>

<head>
<meta name="robots" content="noarchive">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Login - Panchayat Election 2013 Paschim Medinipur</title>
<style type="text/css" media="all" title="CSS By Abu Salam Parvez Alam" >
<!--
@import url("css/Style.css");
-->
</style>
<?php
include "js/php.js";
?>
</head>
<body>
<div class="TopPanel">
  <div class="LeftPanelSide"></div>
  <div class="RightPanelSide"></div>
  <h1><?php echo AppTitle; ?></h1>
</div>
<div class="Header">
</div>
<?php
if (PE\GetVal($_SESSION, 'UserName') !== NULL) {
	require_once('topmenu.php');
} else {
?>
<div class="MenuBar"></div>
  	<?php
}
?>
<div class="content" style="margin-left:5px;margin-right:5px;">
<?php
//echo $action."Captcha: ".$_SESSION['Captcha'];
switch ($action) {
	case "LogOut":
		echo
			"<h2 align=\"center\">Thank You! You Have Successfully Logged Out!</h2>";
		break;
	case "JustLoggedIn":
		echo "<h2 align=\"center\">Welcome " . $_SESSION['UserName'] .
			" You Have Successfully Logged In!</h2>";
		break;
	case "Valid":
		echo "<h2 align=\"center\">You are already Logged In!</h2>";
		break;
	case "NoAccess":
		echo "<h2 align=\"center\">Sorry! Access Denied!</h2>";
		break;
	default:
		echo "<h2>Login - " . AppTitle . "</h2>";
		break;
}
if (($action != "JustLoggedIn") && ($action != "Valid")) {
?> 
<form name="frmLogin" method="post" action="<?php $_SERVER['PHP_SELF']?>">
	<?php //echo "USERID: ".$_POST['UserID']."<br/>".$_POST['UserPass']."<br />".$UserPass.$QueryLogin.$action; ?>
    <label for="UserID">User ID:</label><br />
	<input type="text" id="UserID" name="UserID" value="WBAC21901" autocomplete="off"/>
<br />
<label for="UserPass">Password:</label><br />
<input type="password" id="UserPass" name="UserPass" value="WBAC21901" autocomplete="off"/><br />
<input type="hidden" name="LoginToken" value="<?php echo $_SESSION['Token'];?>" />
<input style="width:80px;" type="submit" value="Login" onClick="document.getElementById('UserPass').value=MD5(MD5(document.getElementById('UserPass').value)+'<?php echo md5($_SESSION['Token']);?>');"/>
</form>
<p><b>Note:</b>Contact System Manager on (9647182926) for User ID and Password.</p>
<?php
	//echo $_SESSION['Debug'];
}
?>
</div>
<div class="pageinfo">
  <?php PE\pageinfo(); ?>
</div>
<div class="footer">
  <?php PE\footerinfo(); ?>
</div>
<?php
//print_r($_SESSION);
?>
</body>
</html>