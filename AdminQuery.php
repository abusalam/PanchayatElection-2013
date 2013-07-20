<?php

use PanchayatElection as PE;

require_once('functions.php');
session_start();
PE\srer_auth();
if ($_SESSION['UserName'] !== "Admin") {
  header("HTTP/1.0 404 Not Found");
  exit;
}
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
    <h2>Personnel Report</h2>
    <?php
    if (PE\GetVal($_POST, 'off_code') !== NULL) {
      $_SESSION['SubDivn'] = substr(PE\GetVal($_POST, 'off_code'), 0, 4);
    }
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <div class="FieldGroup">
        <h3>Block Code: <?php echo $_SESSION['BlockCode']; ?></h3>
        <select name="BlockCode" id="BlockCode">
          <?php
          $Data = new PE\DB();
          $Qry = "select block_municd,CONCAT(block_municd,' - ',block_muni_nm) as block_muni_nm from " . MySQL_Pre . "Block_muni";
          $Data->show_sel("block_municd", "block_muni_nm", $Qry, PE\GetVal($_POST, 'BlockCode'));
          ?>
        </select>
        <input type="submit" name="CmdSubmit" value="Show Office"/>
        <input type="submit" name="CmdSubmit" value="Show Groups"/>
        <h3>Office Code:</h3>
        <select name="off_code" id="off_code">
          <?php
          $Qry = "select off_code,CONCAT(off_code,' - ',office) as office from " . MySQL_Pre . "office where blockmuni='" . PE\GetVal($_POST, 'BlockCode') . "'";
          $Data->show_sel("off_code", "office", $Qry, PE\GetVal($_POST, 'off_code'));
          ?>
        </select>
        <label for="ChkShow"><input type="checkbox" name="ChkShow" id="ChkShow" value="NOT" checked="checked"/>Hide Deleted Data</label>
        <input type="submit" name="CmdSubmit" value="Show Data"/>
      </div>
      <div style="clear:both;"></div>
      <hr />
      <input type="submit" name="CmdQuery" value="Block wise Personnel Count"/>
      <input type="submit" name="CmdQuery" value="Block wise Others Count"/>
      <input type="submit" name="CmdQuery" value="Block wise Office Count"/>
      <input type="submit" name="CmdQuery" value="Total Personnel Count"/>
      <input type="submit" name="CmdQuery" value="Office Count"/>
      <input type="submit" name="CmdQuery" value="Total Office"/>
      <input type="submit" name="CmdQuery" value="Show Groups"/>
      <input type="submit" name="CmdQuery" value="Error B.Pay"/>
      <input type="submit" name="CmdQuery" value="Error B.Pay Count"/>
      <input type="submit" name="CmdQuery" value="Unmatched Scale"/>
      <input type="submit" name="CmdQuery" value="No Staff"/>
      <input type="submit" name="CmdQuery" value="All P4"/>
      <input type="submit" name="CmdQuery" value="Invalid DOB"/>
      <input type="submit" name="CmdQuery" value="Invalid B.Pay"/>
      <input type="submit" name="CmdQuery" value="Invalid Scale"/>
      <input type="submit" name="CmdQuery" value="Block wise Counting Personnel Count"/>
      <hr />
      <div style="clear:both;"></div>
    </form>

    <br/>
    <?php
    $ShowDelete = PE\GetVal($_POST, 'ChkShow');
    switch (TRUE) {
      case ((PE\GetVal($_POST, 'off_code')) && (PE\GetVal($_POST, 'CmdSubmit') === "Show Data")):
        $Qry = "Select P.per_code,P.officer_nm,DATE_FORMAT(date_ob,'%d/%m/%Y') as date_ob,present_ad1,pay,description,epic,"
                . " P.mobile,rem_desc,assembly_temp,assembly_off,HB "
                . " from " . MySQL_Pre . "office O LEFT JOIN (Select * from " . MySQL_Pre . "personnel Where {$ShowDelete} Deleted) P ON (O.off_code=P.off_code) "
                . " LEFT JOIN " . MySQL_Pre . "scale S ON (S.scalecode=P.scalecode) "
                . " LEFT JOIN " . MySQL_Pre . "remarks R ON (R.remarks=P.remarks) "
                . " where O.blockmuni='" . PE\GetVal($_POST, 'BlockCode') . "' AND P.off_code='" . PE\GetVal($_POST, 'off_code') . "'";
        echo "<B>Office: </b>" . $Data->do_max_query("Select CONCAT('[',off_code,'] - [',OldOffCode,'] - [',office,'] - [',address1,'] - [',totstaff,']') "
                . "from " . MySQL_Pre . "office Where off_code='" . PE\GetVal($_POST, 'off_code') . "'") . "<br/>";
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdSubmit') === "Show Data"):
        $Qry = "Select P.per_code,P.officer_nm,DATE_FORMAT(date_ob,'%d/%m/%Y') as date_ob,present_ad1,pay,description,epic,"
                . " P.mobile,rem_desc,assembly_temp,assembly_off,HB "
                . " from " . MySQL_Pre . "office O LEFT JOIN " . MySQL_Pre . "personnel P ON (O.off_code=P.off_code) "
                . " LEFT JOIN " . MySQL_Pre . "scale S ON (S.scalecode=P.scalecode) "
                . " LEFT JOIN " . MySQL_Pre . "remarks R ON (R.remarks=P.remarks) "
                . " where O.blockmuni='" . PE\GetVal($_POST, 'BlockCode') . "' AND P.off_code='" . PE\GetVal($_POST, 'off_code') . "' AND {$ShowDelete} Deleted";
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdSubmit') === "Show Office"):
        $Qry = "Select O.off_code,office,address1,totstaff,count(per_code) as `Actual Count` "
                . " from " . MySQL_Pre . "office O LEFT JOIN (Select * from " . MySQL_Pre . "personnel Where {$ShowDelete} Deleted) P ON (O.off_code=P.off_code) "
                . "Where O.blockmuni='" . PE\GetVal($_POST, 'BlockCode') . "' Group By O.off_code";
        PE\ShowData($Qry);
        $_SESSION['Msg'] = $Qry;
        $_SESSION['BlockCode'] = PE\GetVal($_POST, 'BlockCode');
        break;
      case (PE\GetVal($_POST, 'CmdSubmit') === "Show Groups"):
        $Qry = "Select description, count(per_code) as `Actual Count` "
                . " from " . MySQL_Pre . "office O INNER JOIN (Select * from " . MySQL_Pre . "personnel Where Deleted=0 AND remarks='99' and date_ob>'1953-07-01') P "
                . "ON (O.off_code=P.off_code) INNER JOIN " . MySQL_Pre . "scale S ON (S.scalecode=P.scalecode AND S.PostStatus!='P0') Where O.blockmuni='"
                . PE\GetVal($_POST, 'BlockCode') . "' Group By P.scalecode";
        PE\ShowData($Qry);
        //$_SESSION['Msg']=$Qry;
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Block wise Personnel Count"):
        $Qry = "Select O.blockmuni,B.block_muni_nm,count(per_code) as `Total Staff` "
                . " from " . MySQL_Pre . "office O LEFT JOIN " . MySQL_Pre . "personnel P ON (O.off_code=P.off_code)"
                . " LEFT JOIN " . MySQL_Pre . "Block_muni B ON (B.block_municd=O.blockmuni) Where {$ShowDelete} Deleted Group By O.blockmuni";
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Block wise Counting Personnel Count"):
        $Qry = "Select O.blockmuni,B.block_muni_nm,count(PersSL) as `Total Staff` "
                . " from " . MySQL_Pre . "office O LEFT JOIN " . MySQL_Pre . "count_personnel P ON (O.off_code=P.off_code_cp)"
                . " LEFT JOIN " . MySQL_Pre . "Block_muni B ON (B.block_municd=O.blockmuni) Where {$ShowDelete} Deleted Group By O.blockmuni";
        //$_SESSION['Msg'] = $Qry;
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Block wise Others Count"):
        $Qry = "Select O.blockmuni,B.block_muni_nm,count(per_code) as `Total Staff` "
                . " from " . MySQL_Pre . "office O INNER JOIN (Select * from " . MySQL_Pre . "personnel where HB='0bm') P ON (O.off_code=P.off_code)"
                . " INNER JOIN " . MySQL_Pre . "Block_muni B ON (B.block_municd=O.blockmuni)  Where {$ShowDelete} Deleted Group By O.blockmuni";
        PE\ShowData($Qry);
        //$_SESSION['Msg']=$Qry;
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Block wise Office Count"):
        $Qry = "Select O.blockmuni,B.block_muni_nm,count(DISTINCT O.off_code) as `Total Staff` "
                . " from " . MySQL_Pre . "office O LEFT JOIN " . MySQL_Pre . "personnel P ON (O.off_code=P.off_code)"
                . " LEFT JOIN " . MySQL_Pre . "Block_muni B ON (B.block_municd=O.blockmuni) Where {$ShowDelete} Deleted Group By O.blockmuni";
        PE\ShowData($Qry);
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Total Personnel Count"):
        $Qry = "Select count(per_code) as `Total Staff` "
                . " from " . MySQL_Pre . "personnel Where {$ShowDelete} Deleted";
        echo "<p>Total Personnel: " . $Data->do_max_query($Qry) . "</p>";
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Office Count"):
        $Qry = "Select count(distinct O.off_code) "
                . " from " . MySQL_Pre . "office O INNER JOIN " . MySQL_Pre . "personnel P ON (O.off_code=P.off_code) "
                . " Where {$ShowDelete} Deleted AND O.blockmuni!='0bm'";
        echo "<p>Total Offices having at least one personnel: " . $Data->do_max_query($Qry) . "</p>";
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Total Office"):
        $Qry = "Select count(*)"
                . "  from " . MySQL_Pre . "office Where {$ShowDelete} OffDeleted";
        echo "<p>Total Offices: " . $Data->do_max_query($Qry) . "</p>";
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Show Groups"):
        $Qry = "Select sdiv_cd, PostStatus, count(per_code) as `Actual Count` "
                . " from ((Select off_code,blockmuni from " . MySQL_Pre . "office where blockmuni!='0bm') O INNER JOIN " . MySQL_Pre . "Block_muni B ON (B.block_municd=O.blockmuni)) "
                . "INNER JOIN ((Select per_code,off_code,scalecode from " . MySQL_Pre . "personnel Where Deleted=0 AND remarks='99' and date_ob>'1953-07-01') P INNER JOIN "
                . MySQL_Pre . "scale S ON (S.scalecode=P.scalecode AND S.PostStatus!='P0')) "
                . "ON (O.off_code=P.off_code) Group By sdiv_cd,PostStatus";
        PE\ShowData($Qry);
        $_SESSION['Msg'] = $Qry;
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Error B.Pay"):
        $Qry = "Select P.per_code,P.officer_nm,DATE_FORMAT(date_ob,'%d/%m/%Y') as date_ob,pay "
                . " from " . MySQL_Pre . "office O INNER JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted AND pay<6600) P "
                . "ON (O.off_code=P.off_code) ";
        PE\ShowData($Qry);
        //$_SESSION['Msg']=$Qry;
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Error B.Pay Count"):
        $Qry = "Select O.blockmuni,count(P.per_code) as `Actual Count` "
                . " from " . MySQL_Pre . "office O INNER JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted AND pay<6600) P "
                . "ON (O.off_code=P.off_code) Group by O.blockmuni";
        PE\ShowData($Qry);
        //$_SESSION['Msg']=$Qry;
        break;
      case (PE\GetVal($_POST, 'CmdQuery') === "Unmatched Scale"):
        $Qry = "Select O.off_code,P.per_code,P.officer_nm,DATE_FORMAT(date_ob,'%d/%m/%Y') as date_ob,pay,S.description "
                . " from " . MySQL_Pre . "office O INNER JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted) P "
                . "ON (O.off_code=P.off_code) INNER JOIN " . MySQL_Pre . "scale S ON (S.scalecode=P.scalecode) Where P.scalecode<14";
        PE\ShowData($Qry);
        //$_SESSION['Msg']=$Qry;
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
        $Qry = "Select O.off_code,office,O.blockmuni,per_code,P.officer_nm,date_ob"
                . " from " . MySQL_Pre . "office O LEFT JOIN (Select * from " . MySQL_Pre . "personnel Where NOT Deleted) P ON (O.off_code=P.off_code) "
                . " Where O.blockmuni!='0bm' and (date_ob='1970-01-01' or date_ob<='1953-07-01')";
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
        $_SESSION['Msg'] = $Qry;
        PE\ShowData($Qry);
        break;
    }
    if (PE\GetVal($_SESSION, 'ShowQuery') == 1)
      $_SESSION['Msg'] = $Qry;

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
