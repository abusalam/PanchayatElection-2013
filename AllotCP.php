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

  class FilterSame {

    private $Value;
    private $Key;

    public function __construct($Key, $Value) {
      $this->Key = $Key;
      $this->Value = $Value;
    }

    public function IsSame($SearchArray) {
      if ($SearchArray[$this->Key] === $this->Value) {
        return TRUE;
      } else {
        return FALSE;
      }
    }

  }

  //require_once("topmenu.php");

  function PrintArr($Arr) {
    echo '<pre>';
    print_r($Arr);
    echo '</pre>';
  }

  function ChildCount($Tree, $Node) {
    $Count = 0;
    foreach ($Tree as $value) {
      if ($Node === $value['P']) {
        $Child = $value['C'];
        foreach ($Tree as $value) {
          if ($Child === $value['P']) {
            $GrandChild = $value['C'];
            foreach ($Tree as $value) {
              if ($GrandChild === $value['P']) {
                $Count++;
              }
            }
          }
        }
      }
    }
    return $Count;
  }

  function FindSameAdjCount($AdjCount, $DistCP) {
    $i = 0;
    while ($i < count($AdjCount)) {
      $HaveSameAdjCount = array_filter($AdjCount, array(new FilterSame('AdjCount', $AdjCount[$i]['AdjCount']), 'IsSame'));
      $SameAdjCount = count($HaveSameAdjCount);
      if ($SameAdjCount > 1) { // Found more than one assembly have same number of adjacent blocks
        foreach ($HaveSameAdjCount as $key => $value) {
          $HaveSameAdjCount[$key]['AdjCount']+=ChildCount($DistCP, $value['Assembly']);
        }
        SortByCount($HaveSameAdjCount, 'AdjCount');
        FindSameAdjCount($HaveSameAdjCount, $DistCP);
      } else {
        $_SESSION['FinalAdjCount'] = array_merge($_SESSION['FinalAdjCount'], $HaveSameAdjCount);
      }
      $i+=$SameAdjCount;
    }
  }

  function SortByCount(&$data, $Key) {
    // Obtain a list of columns
    foreach ($data as $key => $row) {
      //$Assembly[$key] = $row['Assembly'];
      $AdjCount[$key] = $row[$Key];
    }
    array_multisort($AdjCount, SORT_ASC, $data);
    //PrintArr($data);
  }

  function DeployCP() {
    $Data = new MySQLiDBHelper(HOST_Name, MySQL_User, MySQL_Pass, MySQL_DB);
    $DistCPQry = 'Select Assembly as P,Block as C from ' . MySQL_Pre . 'CP_Distribution';
    $DistCP = $Data->query($DistCPQry);
    $_SESSION['FinalAdjCount'] = array();
    $MinAdjCountQry = 'Select Assembly,count(Block) as AdjCount from ' . MySQL_Pre . 'CP_Distribution Group By Assembly Order By count(Block)';
    $AdjCount = $Data->query($MinAdjCountQry);
    FindSameAdjCount($AdjCount, $DistCP);

    //PrintArr($_SESSION['FinalAdjCount']);
    foreach ($_SESSION['FinalAdjCount'] as $key => $value) {
      $DeployFrom = array_filter($DistCP, array(new FilterSame('P', $value['Assembly']), 'IsSame'));
      $DeployOrder = array();
      $i = 0;
      //@todo Find the AdjCount as per FinalAdjCount and sort by Count
      foreach ($DeployFrom as $value) {
        $DeployOrder[$i]['Assembly'] = $value['C'];
        $AdjCountKey = '';
        foreach ($_SESSION['FinalAdjCount'] as $AsmKey => $Assembly) {
          if ($Assembly['Assembly'] === $DeployOrder[$i]['Assembly']) {
            $DeployOrder[$i]['AdjCount'] = $Assembly['AdjCount'];
            $AdjCountKey = $AsmKey;
          }
        }
        if ($AdjCountKey === '') { // For Municipality should be deployed at last
          $DeployOrder[$i]['AdjCount'] = 999;
        }
        $i++;
        echo 'Found In: ' . $AdjCountKey . '<br>';
      }
      echo 'Deploy: ' . $_SESSION['FinalAdjCount'][$key]['Assembly'] . '<br>';
      SortByCount($DeployOrder, 'AdjCount');
      $_SESSION['FinalAdjCount'][$key]['OrderBy'] = $DeployOrder;
      //PrintArr($DeployFrom);
      //$FoundFrom = array();
      //$FoundFrom = array_intersect($_SESSION['FinalAdjCount'], $DeployOrder);
    }
    PrintArr($_SESSION['FinalAdjCount']);
    //SortByCount($FoundFrom,'AdjCount' );
    unset($Data);
  }
  ?>
  <div class="content">
    <h2>Allotment</h2>
    <?php
    DeployCP();

    //PrintArr($AdjCount);
    ?>
  </div>
  <div class="pageinfo">
    <?php //PE\pageinfo();                     ?>
  </div>
  <div class="footer">
    <?php PE\footerinfo(); ?>
  </div>
</body>
</html>
