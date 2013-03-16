<?php
use PanchayatElection as PE;
require_once('functions.php');
PE\srer_auth();
PE\HtmlHeader("Report");
?>
<style type="text/css" media="all">
<!--
@import url("css/Style.css");
-->
</style>
<link type="text/css" href="css/ui-darkness/jquery-ui-1.8.21.custom.css"
	rel="Stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript">
$(function() {
	$( ".datepick" ).datepicker({minDate: "-43Y", 
								maxDate: "-18Y",
								dateFormat: 'yy-mm-dd',
								showOtherMonths: true,
								selectOtherMonths: true,
								showButtonPanel: true,
								changeMonth: true,
							    changeYear: true,
								showAnim: "slideDown"
								});
});
</script>
</head>
<body>
	<div class="TopPanel">
		<div class="LeftPanelSide"></div>
		<div class="RightPanelSide"></div>
		<h1>
			<?php echo AppTitle;?>
		</h1>
	</div>
	<div class="Header"></div>
	<?php 
	require_once("topmenu.php");
	?>
	<div class="content">
		<h2>Personnel Report</h2>
		<?php 
			PE\ShowMsg();
			$Data=new PE\DB();
			$Qry="Select count(*) as `Total Staff` "
					. " from ".MySQL_Pre."office O INNER JOIN ".MySQL_Pre."personnel P ON (O.off_code=P.off_code) "
				. " Where O.blockmuni='" . PE\GetVal($_SESSION,'BlockCode') . "' AND NOT Deleted";
			echo "<p><b>Total Personnel: </b>".$Data->do_max_query($Qry)."</p>";
			if(PE\GetVal($_POST, 'OB')!==NULL)
				$_SESSION['BlockCode']=PE\GetVal($_POST, 'OB');
		?>
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<div class="FieldGroup">
			<h3>Office Code:</h3>
			<select name="off_code" id="off_code">
			<?php 
			$Qry="select off_code,CONCAT(off_code,' - ',office) as office from ".MySQL_Pre."office where blockmuni='".PE\GetVal($_SESSION,'BlockCode')."'";
			$Data->show_sel("off_code","office",$Qry,PE\GetVal($_POST,'off_code')); ?>
			</select>
			<input type="submit" name="CmdSubmit" value="Show Data"/><input type="submit" name="CmdSubmit" value="Show Deleted Data"/>
		</div>
		</form>
		<div style="clear:both;"></div>
		<br/>
		<?php
		if(PE\GetVal($_POST,'CmdSubmit')==="Show Data"){
			$ShowDelete=" AND NOT Deleted";
		}
		else {
			$ShowDelete=" AND Deleted";
		}
			
		if(PE\GetVal($_POST,'off_code')){
		$Qry="Select OldPerCode,P.per_code,P.officer_nm,DATE_FORMAT(date_ob,'%d/%m/%Y') as date_ob,present_ad1,pay,description,epic,"
				. " P.mobile,rem_desc,assembly_temp,assembly_off,HB "
				. " from ".MySQL_Pre."office O INNER JOIN ".MySQL_Pre."personnel P ON (O.off_code=P.off_code) "
				. " INNER JOIN ".MySQL_Pre."scale S ON (S.scalecode=P.scalecode) "
				. " INNER JOIN ".MySQL_Pre."remarks R ON (R.remarks=P.remarks) "
				. " where O.blockmuni='".PE\GetVal($_SESSION,'BlockCode')."' AND P.off_code='".PE\GetVal($_POST,'off_code')."' ".$ShowDelete;
		echo "<B>Office: </b>"
				. $Data->do_max_query("Select CONCAT('[',off_code,'] - [',OldOffCode,'] - [',office,'] - [',address1,'] - [',totstaff,']') "
				. "from ".MySQL_Pre."office Where off_code='".PE\GetVal($_POST,'off_code')."'")."<br/>";
		PE\ShowData($Qry);
		}
		else {
		PE\ShowData("Select O.off_code,OldOffCode,office,address1,totstaff,count(*) as `Actual Count` "
				. " from ".MySQL_Pre."office O INNER JOIN ".MySQL_Pre."personnel P ON (O.off_code=P.off_code) "
				. " Where O.blockmuni='" . PE\GetVal($_SESSION,'BlockCode') . "' Group By O.off_code");
		}
		?>
	</div>
	<div class="pageinfo">
		<?php PE\pageinfo(); ?>
	</div>
	<div class="footer">
		<?php PE\footerinfo();?>
	</div>
</body>
</html>