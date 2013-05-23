<?php
use PanchayatElection as PE;
use PanchayatElection\DB as DB;
require_once 'FormData.php';
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
 <h1><?php echo AppTitle; ?></h1>
</div>
<div class="Header">
</div>
<?php
require_once("topmenu.php");
?>
<div class="content">
	<h2><?php echo $_SESSION['StepDesc']; ?></h2>
	<?php PE\ShowMsg(); ?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
	<label for="A"><input type="submit" id="A" name="CmdMode" value="Add New Personnel" /></label>
	<label for="U"><input type="submit" id="U" name="CmdMode" value="Update Existing Personnel" /></label>
	</form>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" 
			<?php echo (PE\GetVal($_SESSION,'Step')!==NULL)?'':'style="display:none;"'; ?>>
	<hr />
	<?php if (PE\GetVal($_SESSION, 'Step') === "A") { ?>
		<div class="FieldLabel"><?php echo PE\GetColHead('off_code'); ?>:</div>
		<span class="ShowField"><?php echo $_SESSION['SubDivn']; ?>
		<input type="text" name="off_code" size="3" maxlength="4" style="border: none;" 
				value="<?php echo substr(PE\GetVal($_SESSION['PostData'],'off_code'),-4); ?>" /></span>
		<?php } else { ?>
		<div class="FieldLabel"><?php echo PE\GetColHead('per_code'); ?>:</div>
		<span class="ShowField"><?php echo $_SESSION['SubDivn']; ?>
		<input type="text" name="per_code" size="4" maxlength="5" style="border: none;" 
				value="<?php echo substr(PE\GetVal($_SESSION['PostData'],'per_code'),-5);?>" /></span>
		<?php } ?>
		<input type="submit" name="AppSubmit" value="Show" />
		<span class="Notice"><b>Office Name:</b><?php echo PE\GetVal($_SESSION['PostData'],'office'); ?></span>
		<hr />
		<div class="FieldGroup">
			<h3><?php echo PE\GetColHead('HB'); ?>:</h3>
			<select name="HB">
			<?php 
			$Data->show_sel("block_municd", "block_muni_nm","select block_municd,block_muni_nm "
								. "from " . MySQL_Pre . "Block_muni", PE\GetVal($_SESSION['PostData'], 'HB',TRUE));
			?>
			</select>
		</div>		
		<div class="FieldGroup">
			<h3><?php echo PE\GetColHead('scalecode'); ?>:</h3>
			<select name="scalecode">
			<?php 
			$Data->show_sel("scalecode", "description","select scalecode,description "
								. "from " . MySQL_Pre . "scale", PE\GetVal($_SESSION['PostData'], 'scalecode',TRUE));
			?>
			</select>
		</div>
		<div class="FieldGroup">
			<h3><?php echo PE\GetColHead('pay'); ?>:</h3>
			<input type="text" name="pay" size="5" maxlength="5" value="<?php echo PE\GetVal($_SESSION['PostData'],'pay'); ?>" />
		</div>
		<div class="FieldGroup">
			<h3><?php echo PE\GetColHead('date_ob'); ?>:</h3>
			<input class="datepick" type="text" size="10" name="date_ob" readonly="readonly" 
					value="<?php echo (PE\GetVal($_SESSION['PostData'],'date_ob')!==NULL)?date("Y-m-d",strtotime(PE\GetVal($_SESSION['PostData'],'date_ob'))):""; ?>" />
		</div>
		<div class="FieldGroup">
		<h3>EPIC:</h3>
			<input type="text" name="epic" size="20" maxlength="20" value="<?php echo PE\GetVal($_SESSION['PostData'],'epic'); ?>" />
		</div>
		<div class="FieldGroup">
			<h3><?php echo PE\GetColHead('mobile'); ?>:</h3>
			<input type="text" name="mobile" size="12" maxlength="10" value="<?php echo PE\GetVal($_SESSION['PostData'],'mobile'); ?>" />
		</div>
		<div style="clear:both;"></div>
		<hr />
		<div class="FieldGroup">
			<h3><?php echo PE\GetColHead('officer_nm'); ?>:</h3>
			<input type="text" name="officer_nm" size="35" maxlength="50" value="<?php echo PE\GetVal($_SESSION['PostData'],'officer_nm'); ?>" />
			<h3><?php echo PE\GetColHead('remarks'); ?>:</h3>
			<select name="remarks">
			<?php
			$Data->show_sel("remarks", "rem_desc","select remarks,rem_desc "
								. "from " . MySQL_Pre . "remarks", PE\GetVal($_SESSION['PostData'], 'remarks',TRUE));
			?>
			</select>
		</div>
		<div class="FieldGroup">
			<h3><?php echo PE\GetColHead('assembly_temp'); ?>:</h3>
			<select name="assembly_temp">
			<?php $Data->show_sel("assembly_cd", "assembly", "select assembly_cd,CONCAT(assembly_cd,' - ',assembly) as assembly "
									. "from " . MySQL_Pre . "assembly ORDER BY assembly_cd", PE\GetVal($_SESSION['PostData'],'assembly_temp',TRUE));
			?>
			</select>
		</div>
		<div class="FieldGroup">
			<h3><?php echo PE\GetColHead('assembly_off'); ?>:</h3>
			<select name="assembly_off">
			<?php $Data->show_sel("assembly_cd", "assembly", "select assembly_cd,CONCAT(assembly_cd,' - ',assembly) as assembly "
									. "from " . MySQL_Pre . "assembly ORDER BY assembly_cd", PE\GetVal($_SESSION['PostData'],'assembly_off',TRUE));
			?>
			</select>
		</div>
		<div class="FieldGroup">
			<h3><?php echo PE\GetColHead('present_ad1'); ?>:</h3>
				<textarea rows="5" cols="30" name="present_ad1" maxlength="100"><?php echo PE\GetVal($_SESSION['PostData'],'present_ad1'); ?></textarea>
		</div>
		<div style="clear:both;"></div><hr />
		<?php 
		if (PE\GetVal($_SESSION, 'Step') === "A") {?>
			<input type="submit" value="Save" name="AppSubmit" />
		<?php 
		} 
		else {
			//	<input type="submit" value="Save and Show Previous" name="AppSubmit" />
			//  <input type="submit" value="Save and Show Next" name="AppSubmit" />
			?>
			<input type="submit" value="Update" name="AppSubmit" />
			<input type="submit" value="Delete" name="AppSubmit" />
		<?php 
		}?>
	</form>
	<?php// echo "Fields: " . count($_POST); ?>
	<pre><?php// print_r($_POST); ?></pre>
</div>
<div class="pageinfo"><?php PE\pageinfo(); ?></div>
<div class="footer"><?php PE\footerinfo(); ?></div>
</body>
</html>
