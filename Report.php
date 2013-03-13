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
		<?php PE\ShowMsg();
		if(PE\GetVal($_POST, 'OB')!==NULL)
			$_SESSION['BlockCode']=PE\GetVal($_POST, 'OB');
		?>
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<div class="FieldGroup">
			<h3>Office Block:</h3>
			<select name="OB" id="OB">
			<?php 
			$Data=new PE\DB();
			$Qry='select block_municd,block_muni_nm from '.MySQL_Pre.'Block_muni';
			$Data->show_sel("block_municd","block_muni_nm",$Qry,PE\GetVal($_SESSION,'BlockCode')); ?>
			</select>
			<input type="submit" name="CmdSubmit" value="Show Data"/>
		</div>
		</form>
		<div style="clear:both;"></div>
		<br/>
		<?php		
		PE\ShowData("Select O.off_code,OldOffCode,office,address1,totstaff,count(*) as `Actual Count` from ".MySQL_Pre."office O,".MySQL_Pre."personnel P where O.off_code=P.off_code AND O.blockmuni='".PE\GetVal($_SESSION,'BlockCode')."' Group By O.off_code");
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
