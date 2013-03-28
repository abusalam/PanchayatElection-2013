<?php
use PanchayatElection as PE;
require_once('functions.php');
PE\srer_auth();
PE\HtmlHeader("Helpline");
?>
<style type="text/css" media="all" title="CSS By Abu Salam Parvez Alam">
<!--
@import url("css/Style.css");
-->
</style>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.10.2.custom.min.css"
			rel="Stylesheet" />
<script type="text/JavaScript" src='js/contact.js'></script>
<script
	type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script
	type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
<script>
$(function() {
    $( "#HelpLineNotes" ).accordion({
    	heightStyle: "content",
    	collapsible: true
    });
  });
</script>
</head>
<body>
	<div class="TopPanel">
		<div class="LeftPanelSide"></div>
		<div class="RightPanelSide"></div>
		<h1><?php echo AppTitle;?></h1>
	</div>
	<div class="Header"></div>
	<?php 
	require_once("topmenu.php");
	?>
	<div class="content">
		<h2>Helpline</h2>
		<?php 
		require_once '../captcha/securimage.php';
		if((PE\GetVal($_POST,'SendQry')==="Send Us Your Query") || (PE\GetVal($_SESSION,'SendQry')==="1")){
			$_SESSION['SendQry']="1";
			require_once("contact.php");		
		}	
		else
		{
			$Data=new PE\DB();
			$UnReplied=$Data->do_max_query("Select count(*) from ".MySQL_Pre."Helpline where Replied=0");
			?>
			<form method="post">
			<div class="FieldGroup">
				<b>Read the Frequently Asked Questions Carefully and then:</b><input name="SendQry" type="submit" value="Send Us Your Query" /><br/>
				<span class="Message"><b>Number of queries to be replied:</b> <?php echo $UnReplied;?></span>
				</div>
			</form>
			<div style="clear:both;"></div>
			<br/>
			<h2>Frequently Asked Questions:</h2>
			<?php
			
			$Data->do_sel_query("Select * from ".MySQL_Pre."Helpline where Replied=1 order by ReplyTime DESC,HelpID desc");
			if($Data->RowCount>0)
			echo '<div id="HelpLineNotes">';
			while($row = $Data->get_row())
			{
			?>
				<h3><?php echo '['.$row['HelpID'].'] '.htmlspecialchars($row['AppName'])." [Replied On: ".date("l d F Y g:i:s A ",strtotime($row['ReplyTime'])).']';?></h3>
				<div>
				<?php echo str_replace("\r\n","<br />",$row['TxtQry']); ?><br/>
					<small><i><?php echo "From IP: {$row['IP']} On: ".date("l d F Y g:i:s A ",strtotime($row['QryTime']));?></i></small>
				<br/><br/>
					<b>Reply:</b><p><i>&ldquo;<?php echo str_replace("\r\n","<br />",$row['ReplyTxt']);?>&rdquo;</i></p>
				</div>
			<?php 
			}
			if($Data->RowCount>0)
			echo '</div>';
			?>
		<div style="clear:both;"></div>
		<?php
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
