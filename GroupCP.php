<?php

use PanchayatElection as PE;

require_once('functions.php');
require_once 'class.MySQLiDBHelper.php';

//PE\srer_auth();
session_start();
PE\HtmlHeader("Allotment");
?>
<style type="text/css" media="all" title="CSS By Abu Salam Parvez Alam">
  <!--
  @import url("css/Style.css");
  -->
  table {
    border-collapse: collapse;
  }
  th, td {
    padding: 4px;
  }
  tr,th{
    border: 2px solid gray;
  }
  tfoot tr td{
    padding: 4px;
  }
</style>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.10.2.custom.min.css"
      rel="Stylesheet" />
<script type="text/JavaScript" src='js/contact.js'></script>
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
</head>
<body>
  <div class="TopPanel">
    <div class="LeftPanelSide"></div>
    <div class="RightPanelSide"></div>
    <h1><?php echo AppTitle; ?></h1>
  </div>
  <div class="Header"></div>
  <?php

  function PrintArr($Arr) {
    echo '<pre>';
    print_r($Arr);
    echo '</pre>';
  }

  function MakeGroupCP($CountingTables, $Post) {
    $Data = new MySQLiDBHelper(HOST_Name, MySQL_User, MySQL_Pass, MySQL_DB);
    foreach ($CountingTables as $Block) {
      $CP_PoolQry = 'Select PersSL from ' . MySQL_Pre . 'CP_Pool';
      $Data->where('AssemblyCode', $Block['Assembly']);
      $Data->where('`Post`', $Post);
      $GroupCP = $Data->query($CP_PoolQry);
      shuffle($GroupCP);
      $GroupID = 1;
      $Reserve = '';
      foreach ($GroupCP as $PersCP) {
        if ($GroupID > $Block['Tables']) {
          $Reserve = 'R';
          if (((count($GroupCP) / 2) > $GroupID) && ($Post === 2)) {
            break;
          }
        }
        $RandCP['PersSL'] = $PersCP['PersSL'];
        $RandCP['GroupID'] = $Reserve . $GroupID;
        $RandCP['AssemblyCode'] = $Block['Assembly'];
        $Data->insert(MySQL_Pre . 'CP_Groups', $RandCP);
        $GroupID++;
      }
    }
  }
  ?>
  <div class="content">
    <h2>Counting Personnel Randomization</h2>
    <table id="GroupCP">
      <thead>
        <tr>
          <th>Assembly Name</th>
          <th>Assembly Code</th>
          <th>Counting Officer</th>
          <th>Counting Assistant</th>
          <th>Reserve Counting Officer</th>
          <th>Reserve Counting Assistant</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="DetailCP">
      </tbody>
      <tfoot>
        <tr><td colspan="7"><input type="button" value="Start Randomization"/></td></tr>
      </tfoot>
    </table>
    <?php
    $Data = new MySQLiDBHelper(HOST_Name, MySQL_User, MySQL_Pass, MySQL_DB);
    $CountingTablesQry = 'Select Assembly,Tables from ' . MySQL_Pre . 'CP_CountingTables';
    $CountingTables = $Data->query($CountingTablesQry);
    MakeGroupCP($CountingTables, 1);
    MakeGroupCP($CountingTables, 2);
    MakeGroupCP($CountingTables, 2);
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
