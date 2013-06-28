<?php 

use PanchayatElection as PE;
require_once('functions.php');
session_start();
PE\HtmlHeader("Visitors Log");
?>
<style type="text/css" media="all">
<!--
@import url("css/Style.css");
-->
</style>
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
				<li
					class="<?php echo ($_SERVER['SCRIPT_NAME']==BaseDIR.'viewlog.php')?'SelMenuitems':'Menuitems';?>">
					<a href="<?php echo PE\GetAbsoluteURLFolder(); ?>viewlog.php">Visitors Log</a>
				</li>
			</ul>
		</div>
	<div class="content">
		<h2>Visitors Log</h2>
		<?php
			$Qry= 'SELECT * FROM '.MySQL_Pre.'Visitors order by vtime desc limit 0, 100';
			PE\ShowData($Qry);
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
