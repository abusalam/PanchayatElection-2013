<?php
use PanchayatElection as PE;
use PanchayatElection\DB as DB;
require_once('functions.php');
PE\srer_auth();
$Data=new DB();
/*
SetCurrForm();
if($_SESSION['ACNo']=="")
	$_SESSION['ACNo']="-- Choose --";
if($_SESSION['PartID']=="")
	$_SESSION['PartID']="-- Choose --";
if (intval($_POST['PartID'])>0)
	$_SESSION['PartID']=intval($_POST['PartID']);
if($_POST['ACNo']!="")
	$_SESSION['ACNo']=$_POST['ACNo'];
if (intval($_REQUEST['ID'])>0)
	$_SESSION['PartMapID']=intval($_REQUEST['ID']);*/
PE\HtmlHeader("Data Entry");
?>
<style type="text/css" media="all" title="CSS By Abu Salam Parvez Alam" >
	<!--
	@import url("css/Style.css");
	-->
</style>
<link type="text/css" href="css/ui-darkness/jquery-ui-1.8.21.custom.css"
			rel="Stylesheet" />
<script
	type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script
	type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
<script>
$(function() {
	$( ".datepick" ).datepicker(
			{yearRange: '1953:1995',
		dateFormat: 'yy-mm-dd',
		showOtherMonths: true,
		selectOtherMonths: true,
		showButtonPanel: true,
		changeMonth: true,
	    changeYear: true,
		showAnim: "slideDown"
		});
	$( "#Dept" ).autocomplete({
			source: "query.php",
			minLength: 3,
			select: function( event, ui ) {
				$('#Dept').val(ui.item.value);
			}
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
<div class="Header">
</div>
<?php 
	require_once("topmenu.php");
?>
<div class="content">
	<h2>Personnel Data Entry</h2>
	<?php PE\ShowMsg(); ?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
		<h3>Office ID:</h3><input type="text" name="off_code"  size="20" maxlength="20" />
		<hr />
		<div class="FieldGroup">
			<h3>Pay Scale:</h3>
			<select name="scalecode">
			<?php 
			$Data=new DB();
			$Data->show_sel("scalecode","description","select scalecode,description"
					." from ".MySQL_Pre."scale",PE\GetVal($_POST, 'scalecode')); ?>
			</select>
		</div>
		<div class="FieldGroup">
			<h3>Home Block:</h3>
			<select name="HB">
			<?php
			$Data->show_sel('select block_municd,block_muni_nm from '.MySQL_Pre.'Block_muni',PE\GetVal($_POST, 'HB')); ?>
			</select>
		</div>
		<div class="FieldGroup">
			<h3>Office Block:</h3>
			<select name="OB">
			<?php
			$Data->show_sel("ResID","ResName","select ResID,CONCAT(PostName,' [',PostGroup,']-',Category) as ResName"
					." from ".MySQL_Pre."Posts P,".MySQL_Pre."Categories C,".MySQL_Pre."Reserved R"
					." Where P.PostID=R.PostID AND C.CatgID=R.CatgID Order by ResID"); ?>
			</select>
		</div>
		<div class="FieldGroup">
		<h3>EPIC:</h3>
			<input type="text" name="epic"  size="20" maxlength="20" />
		</div>
		<div class="FieldGroup">
			<h3>Mobile No:</h3>
			<input type="text" name="mobile" maxlength="10" />
		</div>
		<div style="clear:both;"></div>
		<hr />
		<div class="FieldGroup">
			<h3>Employee Name:</h3>
			<input type="text" name="officer_nm" size="35" maxlength="50" />
			<h3>Basic Pay:</h3>
			<input type="text" name="pay" size="35" maxlength="50" />
			<h3>Date of Birth:</h3>
			<input class="datepick" type="text" size="10" name="date_ob" readonly="readonly"/>
			<h3>Sex:</h3>
			<label><input type="radio" name="gender" value="M"/>Male</label> <label><input
				type="radio" name="gender" value="F"/>Female</label>
			<h3>Present Address:</h3>
				<textarea rows="5" cols="30" name="present_ad1" maxlength="100"></textarea>
		</div>
		<div class="FieldGroup">
			<h3>Present Assembly:</h3>
			<select name="assembly_temp">
			<?php 
			$Data->show_sel("ResID","ResName","select ResID,CONCAT(PostName,' [',PostGroup,']-',Category) as ResName"
					." from ".MySQL_Pre."Posts P,".MySQL_Pre."Categories C,".MySQL_Pre."Reserved R"
					." Where P.PostID=R.PostID AND C.CatgID=R.CatgID Order by ResID"); ?>
			</select>
			<h3>Office Assembly:</h3>
			<select name="assembly_off">
			<?php 
			$Data->show_sel("ResID","ResName","select ResID,CONCAT(PostName,' [',PostGroup,']-',Category) as ResName"
					." from ".MySQL_Pre."Posts P,".MySQL_Pre."Categories C,".MySQL_Pre."Reserved R"
					." Where P.PostID=R.PostID AND C.CatgID=R.CatgID Order by ResID"); ?>
			</select>
		</div>
		<div style="clear:both;"></div>
			<input type="submit" value="Submit" name="AppSubmit" />
		<div style="clear:both;"></div>
	</form>
	<?php echo "Fields: ".count($_POST);?>
	<pre><?php print_r($_POST);?></pre>
</div>
<div class="pageinfo"><?php PE\pageinfo(); ?></div>
<div class="footer"><?php PE\footerinfo();?></div>
</body>
</html>
