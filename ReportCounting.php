<?php

use PanchayatElection as PE;

require_once('functions.php');

if (PE\GetVal($_POST, 'CmdSubmit') === "Download Notice") {
  include 'ElectionNotice.php';
}
switch (PE\GetVal($_POST, 'CmdSubmit')) {
  case "Download All Corrigendum Letters":
  case "Office-wise Corrigendum Letters":
  case "Single Corrigendum Letter":
    include 'AppointmentLetter.php';
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
<link type="text/css" href="css/ui-darkness/jquery-ui-1.8.21.custom.css"
      rel="Stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript">
  $(function() {
    $(".datepick").datepicker({minDate: "-43Y",
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
          $Qry = "select off_code,CONCAT(office,' - ',off_code) as office from " . MySQL_Pre . "office where blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "'";
          $Data->show_sel("off_code", "office", $Qry, PE\GetVal($_POST, 'off_code'));
          ?>
        </select>
        <input type="submit" name="CmdSubmit" value="Show Data"/>
       <!-- <input type="submit" name="CmdSubmit" value="Download Notice"/>

        <input type="submit" name="CmdSubmit" value="Office-wise Corrigendum Letters"/>

        <label for="ChkShow"><input type="checkbox" name="ChkShow" id="ChkShow" value="NOT" checked="checked"/>Hide Deleted Data</label> -->
      </div>
      <div style="clear:both;"></div>
      <hr />
      <!--input type="submit" name="CmdQuery" value="Ofice wise Others Count"/>
      <input type="submit" name="CmdQuery" value="Scalewise Personnel Count"/>
      <input type="submit" name="CmdQuery" value="Error B.Pay"/>
      <input type="submit" name="CmdQuery" value="Unmatched Scale"/>
      <input type="submit" name="CmdQuery" value="No Staff"/>
      <input type="submit" name="CmdQuery" value="All P4"/>
      <input type="submit" name="CmdQuery" value="Invalid DOB"/>
      <input type="submit" name="CmdQuery" value="Invalid B.Pay"/>
      <input type="submit" name="CmdQuery" value="Invalid Scale"/-->
      <!--input type="submit" name="CmdSubmit" value="Download All Corrigendum Letters"/>
      <input type="text" name="PerCode" value="Personnel ID"/>
      <input type="submit" name="CmdSubmit" value="Single Corrigendum Letter"/>
      <input type="submit" name="CmdSubmit" value="Party vs PS"/>
      <input type="submit" name="CmdSubmit" value="PS vs Party"/-->
      <hr />
      <div style="clear:both;"></div>
    </form>
    <div style="clear:both;"></div>
    <br/>
    <?php
    $ShowDelete = " AND " . PE\GetVal($_POST, 'ChkShow') . " Deleted";
    echo "<h3>" . PE\GetVal($_POST, 'CmdQuery') . "</h3>";
    switch (TRUE) {
      case (PE\GetVal($_POST, 'CmdQuery') === "Ofice wise Others Count"):
        $Qry = "Select O.off_code,O.office,count(per_code) as `Total Staff` "
                . " from " . MySQL_Pre . "office O INNER JOIN (Select * from " . MySQL_Pre . "personnel where HB='0bm') P ON (O.off_code=P.off_code)"
                . "  Where NOT Deleted AND O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "' Group By O.off_code";
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Scalewise Personnel Count"):
        $Qry = "Select description, count(per_code) as `Actual Count` "
                . " from " . MySQL_Pre . "office O INNER JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted) P "
                . "ON (O.off_code=P.off_code) INNER JOIN " . MySQL_Pre . "scale S ON S.scalecode=P.scalecode Where O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "' Group By P.scalecode";
        PE\ShowData($Qry);
        //$_SESSION['Msg']=$Qry;
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Error B.Pay"):
        $Qry = "Select P.per_code,P.officer_nm,DATE_FORMAT(date_ob,'%d/%m/%Y') as date_ob,pay "
                . " from " . MySQL_Pre . "office O INNER JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted AND pay<6600) P "
                . "ON (O.off_code=P.off_code) Where O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "'";
        PE\ShowData($Qry);
        //$_SESSION['Msg']=$Qry;
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Unmatched Scale"):
        $Qry = "Select O.off_code,P.per_code,P.officer_nm,DATE_FORMAT(date_ob,'%d/%m/%Y') as date_ob,pay,S.description "
                . " from " . MySQL_Pre . "office O INNER JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted) P "
                . "ON (O.off_code=P.off_code) INNER JOIN " . MySQL_Pre . "scale S ON (S.scalecode=P.scalecode) Where P.scalecode<14 AND O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "'";
        PE\ShowData($Qry);
        //$_SESSION['Msg']=$Qry;
        break;
      case (PE\GetVal($_POST, 'CmdSubmit') === "Show Data"):
        $Qry = "Select P.PersSL,P.officer_nm,P.OFF_DESC,M.gender_desc,P.BASICpay,N.status_desc,"
                . " P.mobile,X.block_muni_nm "
                . " from " . MySQL_Pre . "office O JOIN " . MySQL_Pre . "count_personnel P ON (O.off_code=P.off_code_cp) JOIN " . MySQL_Pre . "GENDER M on (P.gender=M.gender)"
                . " JOIN " . MySQL_Pre . "DESG_STATUS N on (P.status=N.status)"
                . " JOIN " . MySQL_Pre . "Block_muni X on (P.HOMEBLOCK_CODE=X.block_municd)"
                . " where P.Deleted=0 AND O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "' AND P.off_code_cp='" . PE\GetVal($_POST, 'off_code') . "' ";
        echo "<B>Office: </b>"
        . $Data->do_max_query("Select CONCAT('[',off_code,'] - [',office,'] - [',address1,']') "
                . "from " . MySQL_Pre . "office Where off_code='" . PE\GetVal($_POST, 'off_code') . "'") . "<br/>";
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "No Staff"):
        $Qry = "Select O.off_code,office,address1,count(per_code) as `Staff Count` "
                . " from " . MySQL_Pre . "office O LEFT JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted) P ON (O.off_code=P.off_code) "
                . " Where O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "' Group By O.off_code having count(per_code)<1";
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "All P4"):
        $Qry = "Select O.off_code,office,totstaff,count(PostStatus) as `P4 Count` "
                . " from " . MySQL_Pre . "office O LEFT JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted) P ON (O.off_code=P.off_code) "
                . "left join " . MySQL_Pre . "scale S on (S.scalecode=P.scalecode and S.PostStatus='P4')"
                . " Where O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "' Group By O.off_code having (totstaff=count(PostStatus) and count(PostStatus)>0)";
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Invalid DOB"):
        $Qry = "Select O.off_code,office,per_code,P.officer_nm,date_ob"
                . " from " . MySQL_Pre . "office O LEFT JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted) P ON (O.off_code=P.off_code) "
                . " Where O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "' and (date_ob='1970-01-01' or date_ob<='1953-07-01')";
        PE\ShowData($Qry);
        //$_SESSION['Msg']=$Qry;
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Invalid B.Pay"):
        $Qry = "Select O.off_code,office,per_code,P.officer_nm,pay "
                . " from " . MySQL_Pre . "office O LEFT JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted) P ON (O.off_code=P.off_code) "
                . " Where O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "' and (pay<4000 or pay>40000)";
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Invalid Scale"):
        $Qry = "Select O.off_code,office,per_code,P.officer_nm,description,pay"
                . " from " . MySQL_Pre . "office O LEFT JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted) P ON (O.off_code=P.off_code) "
                . "left join " . MySQL_Pre . "scale S on (S.scalecode=P.scalecode)"
                . " Where O.blockmuni='" . PE\GetVal($_SESSION, 'BlockCode') . "' and S.PostStatus='P0'";
        //$_SESSION['Msg']=$Qry;
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdSubmit') === "PS vs Party"):
        $Qry = "Select LPAD(groupid,3,'0') as `Party No`,CONCAT('[',pscd,'] - ',psnm) as `Polling Station` from  " . MySQL_Pre . "PS "
                . " Where assembly='" . PE\GetVal($_SESSION, 'BlockCode') . "' Order By pscd";
        $_SESSION['Msg'] = "<b>" . PE\GetVal($_SESSION, 'BlockCode') . ":</b> Update Complete";
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdSubmit') === "Party vs PS"):
        $Qry = "Select LPAD(groupid,3,'0') as `Party No`,CONCAT('[',pscd,'] - ',psnm) as `Polling Station` from  " . MySQL_Pre . "PS "
                . " Where assembly='" . PE\GetVal($_SESSION, 'BlockCode') . "' Order By groupid";
        $_SESSION['Msg'] = "<b>" . PE\GetVal($_SESSION, 'BlockCode') . ":</b> Update Complete";
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
