<?php

use PanchayatElection as PE;

require_once('functions.php');
session_start();
PE\srer_auth();
if ($_SESSION['UserName'] !== "Admin") {
  header("HTTP/1.0 404 Not Found");
  exit;
}
$Data = new PE\DB();
switch (PE\GetVal($_POST, 'CmdSubmit')) {
  case 'Create Office':
    $Qry = 'Insert into ' . MySQL_Pre . 'office (off_code,Office,blockmuni)'
            . ' Values(\'' . PE\GetVal($_POST, 'off_code') . '\',\''
            . PE\GetVal($_POST, 'Office') . '\',\'' . PE\GetVal($_POST, 'BlockCode') . '\')';
    $Data->do_ins_query($Qry);
    //PE\ShowData($Qry);
    break;
}
PE\HtmlHeader("Office");
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
</head>
<body>
  <div class="TopPanel">
    <div class="LeftPanelSide"></div>
    <div class="RightPanelSide"></div>
    <h1>
      <?php echo AppTitle; ?>
    </h1>
  </div>
  <div class="Header"></div>
  <?php
  require_once("topmenu.php");
  ?>
  <div class="content">
    <h2>Personnel Report</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <div class="FieldGroup">
        <h3>Block Code: <?php echo $_SESSION['BlockCode']; ?></h3>
        <select name="BlockCode" id="BlockCode">
          <?php
          $_SESSION['BlockCode'] = PE\GetVal($_POST, 'BlockCode');
          $Qry = "select block_municd,CONCAT(block_municd,' - ',block_muni_nm) as block_muni_nm from " . MySQL_Pre . "Block_muni";
          $Data->show_sel("block_municd", "block_muni_nm", $Qry, PE\GetVal($_POST, 'BlockCode'));
          ?>
        </select><input type="submit" name="CmdSubmit" value="Show Offices"/>
        <h3>Office Code:</h3>
        <input type="text" name="off_code" size="10"
               value="<?php
               echo $Data->do_max_query('Select Max(off_code)+1 from ' . MySQL_Pre . 'office '
                       . 'Where blockmuni=\'' . PE\GetVal($_SESSION, 'BlockCode') . '\'');
               ?>" />
        <input type="text" name="Office" size="150" />
        <input type="submit" name="CmdSubmit" value="Create Office"/>
      </div>
    </form>
    <br/>
    <?php
    if (PE\GetVal($_SESSION, 'ShowQuery') == 1)
      $_SESSION['Msg'] = $Qry;

    PE\ShowMsg();
    $Qry = 'Select off_code,Office,blockmuni from ' . MySQL_Pre . 'office'
            . ' Where blockmuni=\'' . PE\GetVal($_SESSION, 'BlockCode') . '\' Order By off_code DESC';
    PE\ShowData($Qry);
    ?>
  </div>
  <div class="pageinfo">
    <?php PE\pageinfo(); ?>
  </div>
  <div class="footer">
    <?php PE\footerinfo(); ?>
  </div>
</body>
</html>
