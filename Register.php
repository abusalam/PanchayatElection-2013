<?php
use PanchayatElection as PE;
require_once('functions.php');
session_start();
$Data=new PE\DB();
PE\HtmlHeader("Register");
?>
<style type="text/css" media="all" title="CSS By Abu Salam Parvez Alam">
<!--
@import url("css/Style.css");
-->
</style>
<script type="text/JavaScript" src='js/contact.js'></script>
</head>
<body>
	<div class="TopPanel">
		<div class="LeftPanelSide"></div>
		<div class="RightPanelSide"></div>
		<h1><?php echo AppTitle;?></h1>
	</div>
	<div class="Header"></div>
<div class="MenuBar"></div>
	<div class="content">
		<h2>User Registration</h2>
		<?php 
		require_once '../captcha/securimage.php';
		$img = new Securimage();
		$valid = $img->check(PE\GetVal($_POST,'code'));
		if(!$valid)
		{
		?>
			<form name="feed_frm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" >
			<div class="FieldGroup">
				<h3>Block Name:</h3>
				<select name="PartMapID">
				<?php 
					$Data->show_sel("PartMapID", "UserName","select PartMapID,UserName "
									. "from " . MySQL_Pre . "Users Where PartMapID>0 AND NOT Registered", PE\GetVal($_POST, 'PartMapID',TRUE));
				?>
				</select>
				</div>
				<div class="FieldGroup">
				<h3>Official E-Mail:</h3>
				<input size="35" maxlength="35"	type="text" name="v_email" value="<?php echo htmlspecialchars(PE\GetVal($_POST,'v_email')); ?>" />
				</div>
				<div style="clear:both;"></div>
				<div class="FieldGroup">
				<h3>Password:</h3>
				<input type="password" id="UserPass" name="UserPass" autocomplete="off"/><br />
				<h3>Confirm Password:</h3>
				<input type="password" id="CnfUserPass" name="CnfUserPass" autocomplete="off"/><br />
				<input type="hidden" name="LoginToken" value="<?php echo $_SESSION['Token'];?>" />
				</div>
				<div class="FieldGroup">
				<h3> Secure Image:</h3>
				<!-- pass a session id to the query string of the script to prevent ie caching -->
				<img id="siimage" style="margin-top: 5px;"
					src="../../captcha/securimage_show.php?sid=<?php echo md5(time()) ?>" />
				<a style="margin-top: 42px; margin-left: 10px;" tabindex="-1"
					style="border-style: none" href="#" title="Refresh Image"
					onClick="document.getElementById('siimage').src = '../../captcha/securimage_show.php?sid=' + Math.random(); return false;">
					<img src="../../captcha/images/refresh.gif" alt="Reload Image"
					border="0" onClick="this.blur();" align="bottom" />
				</a> <br /> Image Code:
				<!-- NOTE: the "name" attribute is "code" so that $img->check($_POST['code']) will check the submitted form field -->
				<input type="text" name="code" size="12" />
				<input style="width:80px;" type="submit" value="Register" 
					onClick="document.getElementById('UserPass').value=MD5(document.getElementById('UserPass').value);"/>
				</div>
			</form>
		<?php
		}
		else
		{
			$email=$Data->SqlSafe($_POST['v_email']);
			$Pass=$Data->SqlSafe($_POST['UserPass']);
			$PartMapID=$Data->SqlSafe($_POST['PartMapID']);
			if(strlen($_POST['feed_txt'])<=1024 && strlen($_POST['v_email'])<=50 && strlen($_POST['v_name'])<=50){
				$Qry="Update ".MySQL_Pre."Users SET UserID='{$email}',UserPass='{$Pass}',Registered=1 "
					." Where Registered=0 AND Activated=0 AND PartMapID='{$PartMapID}'";
				$Submitted=$Data->do_ins_query($Qry);
				$_SESSION['Msg']=$Qry;
			}
			if($Submitted>0)
				echo '<h3>You have registered yourself successfully.</h3>';
			else
				echo "<h3>Unable to send request.</h3>";
		}
		?>
		<div style="clear:both;"></div>
	</div>
	<div class="pageinfo">
		<?php PE\pageinfo(); ?>
	</div>
	<div class="footer">
		<?php PE\footerinfo();?>
	</div>
</body>
</html>
