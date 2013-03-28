<?php
use PanchayatElection as PE;
require_once('functions.php');
session_start();
if ($_SESSION['UserName']!=="Admin") {
	header("HTTP/1.0 404 Not Found");
	exit;
}
if(PE\GetVal($_GET,'Me'))
	$_SESSION['ShowQuery']=1;
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
		if(PE\GetVal($_POST, 'off_code')!==NULL){
			$_SESSION['SubDivn'] = substr(PE\GetVal($_POST, 'off_code'),0,4);
		}
		?>
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<div class="FieldGroup">
			<h3>Block Code: <?php echo $_SESSION['BlockCode'];?></h3>
			<select name="BlockCode" id="BlockCode">
			<?php 
			$Data=new PE\DB();
			$Qry="select block_municd,CONCAT(block_municd,' - ',block_muni_nm) as block_muni_nm from ".MySQL_Pre."Block_muni";
			$Data->show_sel("block_municd","block_muni_nm",$Qry,PE\GetVal($_POST,'BlockCode')); ?>
			</select>
			<input type="submit" name="CmdSubmit" value="Show Office"/>
			<h3>Office Code:</h3>
			<select name="off_code" id="off_code">
			<?php 
			$Qry="select off_code,CONCAT(off_code,' - ',office) as office from ".MySQL_Pre."office where blockmuni='".PE\GetVal($_POST,'BlockCode')."'";
			$Data->show_sel("off_code","office",$Qry,PE\GetVal($_POST,'off_code')); ?>
			</select>
			<label for="ChkShow"><input type="checkbox" name="ChkShow" id="ChkShow" value="NOT" checked="checked"/>Hide Deleted Data</label>
			<input type="submit" name="CmdSubmit" value="Show Data"/>
		</div>
		<div style="clear:both;"></div>
			<hr />
			<input type="submit" name="CmdQuery" value="Block wise Personnel Count"/>
			<input type="submit" name="CmdQuery" value="Block wise Office Count"/>
			<input type="submit" name="CmdQuery" value="Total Personnel Count"/>
			<input type="submit" name="CmdQuery" value="Office Count"/>
			<input type="submit" name="CmdQuery" value="Total Office"/>
			<hr />
		<div style="clear:both;"></div>
		</form>
		
		<br/>
		<?php
		$ShowDelete=PE\GetVal($_POST,'ChkShow');
		switch (TRUE){
			case ((PE\GetVal($_POST,'off_code')) && (PE\GetVal($_POST,'CmdSubmit')==="Show Data")):
				$Qry="Select OldPerCode,P.per_code,P.officer_nm,DATE_FORMAT(date_ob,'%d/%m/%Y') as date_ob,present_ad1,pay,description,epic,"
					. " P.mobile,rem_desc,assembly_temp,assembly_off,HB "
					. " from ".MySQL_Pre."office O LEFT JOIN (Select * from ".MySQL_Pre."personnel Where {$ShowDelete} Deleted) P ON (O.off_code=P.off_code) "
					. " LEFT JOIN ".MySQL_Pre."scale S ON (S.scalecode=P.scalecode) "
					. " LEFT JOIN ".MySQL_Pre."remarks R ON (R.remarks=P.remarks) "
					. " where O.blockmuni='".PE\GetVal($_POST,'BlockCode')."' AND P.off_code='".PE\GetVal($_POST,'off_code')."'";
				echo "<B>Office: </b>" . $Data->do_max_query("Select CONCAT('[',off_code,'] - [',OldOffCode,'] - [',office,'] - [',address1,'] - [',totstaff,']') "
					. "from ".MySQL_Pre."office Where off_code='".PE\GetVal($_POST,'off_code')."'")."<br/>";
				PE\ShowData($Qry);
				break;
			case (PE\GetVal($_POST,'CmdSubmit')==="Show Data"):
				$Qry="Select OldPerCode,P.per_code,P.officer_nm,DATE_FORMAT(date_ob,'%d/%m/%Y') as date_ob,present_ad1,pay,description,epic,"
					. " P.mobile,rem_desc,assembly_temp,assembly_off,HB "
					. " from ".MySQL_Pre."office O LEFT JOIN ".MySQL_Pre."personnel P ON (O.off_code=P.off_code) "
					. " LEFT JOIN ".MySQL_Pre."scale S ON (S.scalecode=P.scalecode) "
					. " LEFT JOIN ".MySQL_Pre."remarks R ON (R.remarks=P.remarks) "
					. " where O.blockmuni='".PE\GetVal($_POST,'BlockCode')."' AND P.off_code='".PE\GetVal($_POST,'off_code')."' AND {$ShowDelete} Deleted";
				PE\ShowData($Qry);
				break;
			case (PE\GetVal($_POST,'CmdSubmit')==="Show Office"):
				$Qry="Select O.off_code,OldOffCode,office,address1,totstaff,count(per_code) as `Actual Count` "
					. " from ".MySQL_Pre."office O LEFT JOIN (Select * from ".MySQL_Pre."personnel Where {$ShowDelete} Deleted) P ON (O.off_code=P.off_code) "
					. "Where O.blockmuni='" . PE\GetVal($_POST,'BlockCode') . "' Group By O.off_code";
				PE\ShowData($Qry);
				$_SESSION['BlockCode'] = PE\GetVal($_POST,'BlockCode');
				break;
			case (PE\GetVal($_POST,'CmdQuery')==="Block wise Personnel Count"):
				$Qry="Select O.blockmuni,B.block_muni_nm,count(per_code) as `Total Staff` "
					. " from ".MySQL_Pre."office O LEFT JOIN ".MySQL_Pre."personnel P ON (O.off_code=P.off_code)"
					. " LEFT JOIN ".MySQL_Pre."Block_muni B ON (B.block_municd=O.blockmuni) Where {$ShowDelete} Deleted Group By O.blockmuni";
				PE\ShowData($Qry);
				break;
			case (PE\GetVal($_POST,'CmdQuery')==="Block wise Office Count"):
				$Qry="Select O.blockmuni,B.block_muni_nm,count(DISTINCT O.off_code) as `Total Staff` "
					. " from ".MySQL_Pre."office O LEFT JOIN ".MySQL_Pre."personnel P ON (O.off_code=P.off_code)"
					. " LEFT JOIN ".MySQL_Pre."Block_muni B ON (B.block_municd=O.blockmuni) Where {$ShowDelete} Deleted Group By O.blockmuni";
				PE\ShowData($Qry);
				break;
			case (PE\GetVal($_POST,'CmdQuery')==="Total Personnel Count"):
				$Qry="Select count(per_code) as `Total Staff` "
					. " from ".MySQL_Pre."personnel Where {$ShowDelete} Deleted";
				echo "<p>Total Personnel: ".$Data->do_max_query($Qry)."</p>";
				break;
			case (PE\GetVal($_POST,'CmdQuery')==="Office Count"):
				$Qry="Select count(distinct O.off_code) "
					. "  from ".MySQL_Pre."office O LEFT JOIN ".MySQL_Pre."personnel P ON (O.off_code=P.off_code) Where {$ShowDelete} Deleted";
				echo "<p>Total Offices having at least one personnel: ".$Data->do_max_query($Qry)."</p>";
				break;
			case (PE\GetVal($_POST,'CmdQuery')==="Total Office"):
				$Qry="Select count(*)"
					. "  from ".MySQL_Pre."office Where {$ShowDelete} OffDeleted";
				echo "<p>Total Offices: ".$Data->do_max_query($Qry)."</p>";
				break;
		}
		if(PE\GetVal($_SESSION,'ShowQuery')==1)
			$_SESSION['Msg']=$Qry;
		
		PE\ShowMsg();
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