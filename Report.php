<?php
use PanchayatElection as PE, PE\DB as DB;
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
		<?php PE\ShowMsg(); /*?>
		<span class="Notice">
		<b>Please Note: </b>The Applicant is instructed to deposit the necessary fees using the Bank Challan and make sure that the
		<b>&ldquo;Applicant ID&rdquo;</b> is entered by the Bank Operator at Bank Counter, Instead of Applicant Name.
		</span>
		<span class="Notice">
		<b>Online Transfer: </b>Applicant may also transfer the amount using Internet Banking, Only make sure that the 
		<b>&ldquo;Applicant ID&rdquo;</b> is mentioned in the transaction remarks.
		</span>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
		<?php if(GetVal($_SESSION, 'Step')!="Print"){?>
		<div class="FieldGroup">
			<h3>Applicant ID:</h3>
			<input type="text" name="AppID" maxlength="4" />
		</div>
		<div class="FieldGroup">
				<h3>Mobile No:</h3>
				<input type="text" name="AppMobile" maxlength="10" />
				
		</div>
		<?php }?>
		<input type="submit" value="<?php echo ($_SESSION['Step']!="Print")?"Search":"Print Challan";?>" name="CmdPrint" />
		<div style="clear:both;"></div>
		</form>
		<?php
		//$Data=new DB();
		//echo "<br/><p>Count: ".$Data->do_max_query("Select count(*) from ".MySQL_Pre."AppIDs")."</p>";
		//$Data->do_close();
		//echo $_SESSION['Step']."<br/>".$_SESSION['Qry'].$_SESSION['PostData']['Fields'];
		 * 
		 */
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
