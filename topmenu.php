<?php use PanchayatElection as FN; ?>
<div class="MenuBar">
	<ul>
		<li
			class="<?php echo ($_SERVER['SCRIPT_NAME']==BaseDIR.'index.php')?'SelMenuitems':'Menuitems';?>">
			<a href="<?php echo FN\GetAbsoluteURLFolder(); ?>index.php">Home</a>
		</li>
		<li
			class="<?php echo ($_SERVER['SCRIPT_NAME']==BaseDIR.'AppForm.php')?'SelMenuitems':'Menuitems';?>">
			<a href="<?php echo FN\GetAbsoluteURLFolder(); ?>dataentry.php">Personnel Data Entry</a>
		</li>
		<li
			class="<?php echo ($_SERVER['SCRIPT_NAME']==BaseDIR.'Report.php')?'SelMenuitems':'Menuitems';?>">
			<a href="<?php echo FN\GetAbsoluteURLFolder(); ?>Report.php">Report</a>
		</li>
		<?php 
			if ($_SESSION['UserName']==="Admin") {
				?>
				<li 
					class="<?php echo ($_SERVER['SCRIPT_NAME']==BaseDIR.'AdminQuery.php')?'SelMenuitems':'Menuitems';?>">
					<a href="<?php echo FN\GetAbsoluteURLFolder(); ?>AdminQuery.php">District Admin Query</a>
				</li>
				<li 
					class="<?php echo ($_SERVER['SCRIPT_NAME']==BaseDIR.'reply.php')?'SelMenuitems':'Menuitems';?>">
					<a href="<?php echo FN\GetAbsoluteURLFolder(); ?>reply.php">Reply Helpline</a>
				</li>
				<?php 
			}
		?>	
		
		<li
			class="<?php echo ($_SERVER['SCRIPT_NAME']==BaseDIR.'Helpline.php')?'SelMenuitems':'Menuitems';?>">
			<a href="<?php echo FN\GetAbsoluteURLFolder(); ?>Helpline.php">Helpline</a>
		</li>
		<li
			class="<?php echo ($_SERVER['SCRIPT_NAME']==BaseDIR.'?LogOut=1')?'SelMenuitems':'Menuitems';?>">
			<a href="<?php echo FN\GetAbsoluteURLFolder(); ?>?LogOut=1">Logout</a>
		</li>
	</ul>
</div>