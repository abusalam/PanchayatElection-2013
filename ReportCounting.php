<?php

use PanchayatElection as PE;

require_once('functions.php');

switch (PE\GetVal($_POST, 'CmdSubmit')) {
  case "Download All Appointment Letters":
  case "Office-wise Appointment Letters":
  case "Single Appointment Letter":
    include 'CountingAppLetter.php';
    break;
  case 'Office-wise Scroll':
    include 'CountingOffScroll.php';
    break;
  case 'Counting Hall-wise Scroll':
    include 'CountingHallScroll.php';
    break;
  case 'Counting Personnel Decoding List':
    include 'HallScrollCP.php';
    break;
}
PE\srer_auth();
PE\HtmlHeader("Report");
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
      <?php echo AppTitle; ?>
    </h1>
  </div>
  <div class="Header"></div>
  <?php
  require_once("topmenu.php");
  ?>
  <div class="content">
    <h2>Counting Personnel Report</h2>
    <?php
    PE\ShowMsg();
    $Data = new PE\DB();
    $Qry = "Select count(*) as `Total Staff` "
            . " from " . MySQL_Pre . "office O INNER JOIN " . MySQL_Pre . "count_personnel P ON (O.off_code=P.off_code_cp) "
            . " Where P.Deleted=0 AND O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "'";
    echo "<p><b>Total Personnel: </b>" . $Data->do_max_query($Qry) . "</p>";
    if (PE\GetVal($_POST, 'OB') !== NULL)
      $_SESSION['BlockCode'] = PE\GetVal($_POST, 'OB');
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <div class="FieldGroup">
        <h3>Office Code:</h3>
        <select name="off_code" id="off_code">
          <?php
          $Qry = "select off_code,CONCAT(off_code,' - ',office) as office from " . MySQL_Pre . "office where blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "'";
          $Data->show_sel("off_code", "office", $Qry, PE\GetVal($_POST, 'off_code'));
          ?>
        </select>
        <input type="submit" name="CmdSubmit" value="Show Data"/>
        <input type="submit" name="CmdSubmit" value="Office-wise Appointment Letters"/>

        <label for="ChkShow">Hide Deleted Data</label>
        <input type="checkbox" name="ChkShow" id="ChkShow" value="NOT" checked="checked"/>
      </div>
      <div style="clear:both;"></div>
      <hr />
      <input type="submit" name="CmdSubmit" value="Download All Appointment Letters"/>
      <input type="text" name="PerCode" placeholder="Personnel ID"/>
      <input type="submit" name="CmdSubmit" value="Single Appointment Letter"/>
      <input type="submit" name="CmdSubmit" value="Counting Personnel Scroll"/>
      <input type="submit" name="CmdSubmit" value="Office-wise Scroll"/>
      <input type="submit" name="CmdSubmit" value="Counting Hall-wise Scroll"/>
      <input type="submit" name="CmdSubmit" value="Counting Personnel Decoding List"/>
      <hr />
    </form>
    <div style="clear:both;"></div>
    <br/>
    <?php
    $ShowDelete = " AND " . PE\GetVal($_POST, 'ChkShow') . " Deleted";
    echo "<h3>" . PE\GetVal($_POST, 'CmdSubmit') . "</h3>";
    switch (TRUE) {
      case (PE\GetVal($_POST, 'CmdSubmit') === "Counting Personnel Scroll"):
        $Qry = "Select `PerCode`,`Post Status`,`Officer Name`,`Mobile No`,`Block`,`Office`,`Office Address`,`Post Office`"
                . " from " . MySQL_Pre . "CP_AsmScroll"
                . "  Where AssemblyCode='" . PE\GetVal($_SESSION, 'BlockCode') . "'";
        //$_SESSION['Msg'] = $Qry;
        echo '<b>Please Note:</b> Post Status (1:Counting Officer, 2:Counting Assistant).<br>';
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdSubmit') === "Show Data"):
        $Qry = "Select P.PersSL,P.officer_nm,P.OFF_DESC,M.gender_desc,P.BASICpay,N.status_desc,"
                . " P.mobile,X.block_muni_nm "
                . " from " . MySQL_Pre . "office O JOIN " . MySQL_Pre . "count_personnel P ON (O.off_code=P.off_code_cp)"
                . " JOIN " . MySQL_Pre . "GENDER M on (P.gender=M.gender)"
                . " JOIN " . MySQL_Pre . "DESG_STATUS N on (P.status=N.status)"
                . " JOIN " . MySQL_Pre . "Block_muni X on (P.HOMEBLOCK_CODE=X.block_municd)"
                . " where P.Deleted=0 AND O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode')
                . "' AND P.off_code_cp='" . PE\GetVal($_POST, 'off_code') . "' {$ShowDelete}";
        echo "<B>Office: </b>"
        . $Data->do_max_query("Select CONCAT('[',off_code,'] - [',office,'] - [',address1,']') "
                . "from " . MySQL_Pre . "office Where off_code='" . PE\GetVal($_POST, 'off_code') . "'") . "<br/>";
        PE\ShowData($Qry);
        break;
      default:
        PE\ShowData("Select O.off_code,office,address1,count(PersSL) as `Counting P Count` "
                . " from " . MySQL_Pre . "office O  JOIN " . MySQL_Pre . "count_personnel P ON (O.off_code=P.off_code_cp) "
                . " Where O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "' Group By O.off_code");
    }
    if (PE\GetVal($_SESSION, 'ShowQuery') == 1)
      PE\ShowMsg();
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
