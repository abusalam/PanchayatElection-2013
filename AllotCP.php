<?php

use PanchayatElection as PE;

require_once('functions.php');
require_once 'class.MySQLiDBHelper.php';

PE\srer_auth();
PE\HtmlHeader("Allotment");
?>
<style type="text/css" media="all" title="CSS By Abu Salam Parvez Alam">
  <!--
  @import url("css/Style.css");
  -->
</style>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.10.2.custom.min.css"
      rel="Stylesheet" />
<script type="text/JavaScript" src='js/contact.js'></script>
<script
type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script
type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
<script>
  $(function() {
    $("#HelpLineNotes").accordion({
      heightStyle: "content",
      collapsible: true
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
  <div class="Header"></div>
  <?php
  require_once("topmenu.php");

  function PrintArr($Arr) {
    echo '<pre>';
    print_r($Arr);
    echo '</pre>';
  }

  function ChildCount($Tree, $Node) {
    $Count = 0;
    foreach ($Tree as $value) {
      if ($Node === $value['P']) {
        $Count++; // .= $value['C'] . ', ';
        if ($_SESSION['Nest'] <= 1) {
          $_SESSION['Nest']++;
          //ChildCount($Tree, $value['C'], $Count);
        }
      }
    }
    return '(' . $Node . ':' . $Count . ')';
  }
  ?>
  <div class="content">
    <h2>Allotment</h2>
    <?php
    $Data = new MySQLiDBHelper(HOST_Name, MySQL_User, MySQL_Pass, MySQL_DB);
    $DistCPQry = 'Select Assembly as P,Block as C from ' . MySQL_Pre . 'CP_Distribution';
    $DistCP = $Data->query($DistCPQry);
    $MinAdjCountQry = 'Select Assembly,count(Block) from ' . MySQL_Pre . 'CP_Distribution Group By Assembly Order By count(Block)';
    $AdjCount = $Data->query($MinAdjCountQry);

    for ($i = 0; $i < count($AdjCount); $i++) { //Runs for each Block
      $CountFrom = '';
      foreach ($DistCP as $value) {
        if ($value['P'] == $AdjCount[$i]['Assembly']) {
          $_SESSION['Nest'] = 0;
          $CountFrom .= ChildCount($DistCP, $value['C'], $CountFrom) . ',';
        }
      }
      echo $AdjCount[$i]['Assembly'] . ': ' . $CountFrom . '<br>';
      $CountFrom = '';
    }

    //PrintArr($AdjCount);
    unset($Data);
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
