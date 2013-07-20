<?php
use PanchayatElection as PE;
require_once('functions.php');
PE\initpage();
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
		<div class="MenuBar">
			<ul>
				<li
					class="<?php echo ($_SERVER['SCRIPT_NAME']==BaseDIR.'index.php')?'SelMenuitems':'Menuitems';?>">
					<a href="<?php echo PE\GetAbsoluteURLFolder(); ?>index.php">Home</a>
				</li>
			</ul>
		</div>
		<?php 
		//require_once("topmenu.php");
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
			<input type="submit" name="CmdQuery" value="Show Data"/>
			<input type="submit" name="CmdQuery" value="Polling Duty Status"/>
		</div>
		<div style="clear:both;"></div>
			<hr />
			<input type="submit" name="CmdQuery" value="Show Scalewise Count"/>
			<input type="text" name="per_code" value="PIN or Name" />
			<input type="submit" name="CmdQuery" value="Show Duty Status"/>
			<hr />
		<div style="clear:both;"></div>
		</form>
		<br/>
		<?php
		$ShowDelete=PE\GetVal($_POST,'ChkShow');
		switch (TRUE){
			case ((PE\GetVal($_POST,'off_code')) && (PE\GetVal($_POST,'CmdQuery')==="Show Data")):
				$Qry="Select P.per_code,P.officer_nm,DATE_FORMAT(date_ob,'%d/%m/%Y') as date_ob,present_ad1,pay,description,epic,"
					. " P.mobile,rem_desc,assembly_temp,assembly_off,HB "
					. " from ".MySQL_Pre."office O LEFT JOIN (Select * from ".MySQL_Pre."personnel Where {$ShowDelete} Deleted) P ON (O.off_code=P.off_code) "
					. " LEFT JOIN ".MySQL_Pre."scale S ON (S.scalecode=P.scalecode) "
					. " LEFT JOIN ".MySQL_Pre."remarks R ON (R.remarks=P.remarks) "
					. " where O.blockmuni='".PE\GetVal($_POST,'BlockCode')."' AND P.off_code='".PE\GetVal($_POST,'off_code')."'";
				echo "<B>Office: </b>" . $Data->do_max_query("Select CONCAT('[',off_code,'] - [',OldOffCode,'] - [',office,'] - [',address1,'] - [',totstaff,']') "
					. "from ".MySQL_Pre."office Where off_code='".PE\GetVal($_POST,'off_code')."'")."<br/>";
				PE\ShowData($Qry);
				break;
			case (PE\GetVal($_POST,'CmdQuery')==="Show Office"):
				$Qry="Select O.off_code,office,address1,totstaff,count(per_code) as `Actual Count` "
					. " from ".MySQL_Pre."office O LEFT JOIN (Select * from ".MySQL_Pre."personnel Where {$ShowDelete} Deleted) P ON (O.off_code=P.off_code) "
					. "Where O.blockmuni='" . PE\GetVal($_POST,'BlockCode') . "' Group By O.off_code";
				PE\ShowData($Qry);
                $_SESSION['Msg']=$Qry;
				$_SESSION['BlockCode'] = PE\GetVal($_POST,'BlockCode');
				break;
			case (PE\GetVal($_POST,'CmdQuery')==="Show Scalewise Count"):
				$Qry="Select description, count(per_code) as `Actual Count`,PostStatus "
					. " from ".MySQL_Pre."office O INNER JOIN (Select * from ".MySQL_Pre."personnel Where Deleted=0 AND remarks='99' and date_ob>'1953-07-01') P "
					. "ON (O.off_code=P.off_code) INNER JOIN ".MySQL_Pre."scale S ON (S.scalecode=P.scalecode AND S.PostStatus!='P0') Where O.blockmuni='" 
					. PE\GetVal($_POST,'BlockCode') . "' Group By P.scalecode";
				echo "<B>PostStatus: </b>PrO=1 P1=2 P2=3 P3=4 P4=5<br/>";				
				PE\ShowData($Qry);
				$_SESSION['Msg']=$Qry;
				break;
			case (PE\GetVal($_POST,'CmdQuery')==="Polling Duty Status"):
				$Qry="Select P.per_code,officer_nm,block_muni_nm,GroupID,PS.PostStatus,D.Status "
					. "from ".MySQL_Pre."personnel P INNER JOIN ".MySQL_Pre."scale S ON (S.scalecode=P.scalecode) "
					. "INNER JOIN ".MySQL_Pre."Booked B ON (B.per_code=P.per_code) INNER JOIN ".MySQL_Pre."Block_muni A ON (A.block_municd=B.ForAssembly)" 
					. " INNER JOIN ".MySQL_Pre."PostCodes PS ON (PS.PostCode=B.PostCode) INNER JOIN ".MySQL_Pre."DutyStatus D ON (D.DutyCode=B.DutyCode)"
					. "Where off_code='" . PE\GetVal($_POST,'off_code') . "'";
				PE\ShowData($Qry);
				$_SESSION['Msg']=$Qry;
				break;
			case (PE\GetVal($_POST,'CmdQuery')==="Show Duty Status"):
				$Qry="Select P.off_code,P.per_code,officer_nm,block_muni_nm,GroupID,PS.PostStatus,D.Status "
					. "from ".MySQL_Pre."personnel P INNER JOIN ".MySQL_Pre."scale S ON (S.scalecode=P.scalecode) "
					. "INNER JOIN ".MySQL_Pre."Booked B ON (B.per_code=P.per_code) INNER JOIN ".MySQL_Pre."Block_muni A ON (A.block_municd=B.ForAssembly)" 
					. " INNER JOIN ".MySQL_Pre."PostCodes PS ON (PS.PostCode=B.PostCode) INNER JOIN ".MySQL_Pre."DutyStatus D ON (D.DutyCode=B.DutyCode)"
					. "Where P.per_code='" . PE\GetVal($_POST,'per_code') . "' or officer_nm like '%" . PE\GetVal($_POST,'per_code') . "%'";
				PE\ShowData($Qry);
				$_SESSION['Msg']=$Qry;
				break;
		}
		?>
	</div>
	<div class="pageinfo">
		<?php PE\pageinfo(); ?>
	</div>
	<div class="footer">
		<?php PE\footerinfo();?>
	</div>
<?php echo "<!-- ".$_SESSION['Msg']."-->"; ?>
</body>
</html>
