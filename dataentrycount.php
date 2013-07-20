<?php

use PanchayatElection as PE;
use PanchayatElection\DB as DB;

require_once 'FormDatacount.php';
PE\HtmlHeader("Data Entry");
?>
<style type="text/css" media="all" title="CSS By Abu Salam Parvez Alam" >
  <!--
  @import url("css/Style.css");
  -->
</style>
<link type="text/css" href="css/ui-darkness/jquery-ui-1.8.21.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
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
    <form method="post" name="dataPcount" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
      <label for="A"><input type="submit" id="A" name="CmdMode" value="Add New Counting Personnel" /></label>
      <label for="U"><input type="submit" id="U" name="CmdMode" value="Update Existing Counting Personnel" /></label>
    </form>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
          <?php echo (PE\GetVal($_SESSION, 'Step') !== NULL) ? '' : 'style="display:none;"'; ?>>
      <hr />
      <?php if (PE\GetVal($_SESSION, 'Step') === "A") { ?>
        <div class="FieldGroup">
          <h3><?php echo PE\GetColHead('off_code_cp'); ?>:</h3>
          <select name="off_code_cp">
            <?php
            $Data->show_sel("off_code", "off_desc", "select off_code, CONCAT(office, ' - ', off_code) as off_desc "
                    . "from " . MySQL_Pre . "office where blockmuni='" . $_SESSION['BlockCode'] . "'", PE\GetVal($_SESSION['PostData'], 'off_code_cp', TRUE));
            ?>
          </select>
          <?php // echo PE\GetVal($_SESSION['PostData'], 'off_code_cp', TRUE); ?>
        </div>
      <?php } ?>
      <?php if (PE\GetVal($_SESSION, 'Step') === "U") { ?>
        <div class="FieldGroup">
          <h3><?php echo PE\GetColHead('officer_nm'); ?>:</h3>
          <select name="PersSL">
            <?php
            $Data->show_sel("PersSL", "Pofficer_nm", "select PersSL, CONCAT(officer_nm, ' - ', PersSL) as Pofficer_nm "
                    . "from " . MySQL_Pre . "count_personnel where Deleted=0 AND OFFBLOCK_CODE='" . $_SESSION['BlockCode']
                    . "'", PE\GetVal($_SESSION['PostData'], 'PersSL', TRUE));
            ?>
          </select>
          <input type="submit" id="U" name="AppSubmit" value="Show" />
          <input type="submit" id="D" name="AppSubmit" value="Delete" />
        </div>
      <?php } ?>
      <div style="clear:both;"></div>
      <div style="clear:both;"></div><hr />
      <!------------
      ------->
      <div class="FieldGroup">
        <h3><?php echo PE\GetColHead('officer_nm'); ?>:</h3>
        <input type="text" name="officer_nm" size="35" maxlength="50" value="<?php echo PE\GetVal($_SESSION['PostData'], 'officer_nm'); ?>" />
      </div>
      <div class="FieldGroup">
        <h3><?php echo PE\GetColHead('OFF_DESC'); ?>:</h3>
        <input type="text" name="OFF_DESC" size="25" maxlength="80" value="<?php echo PE\GetVal($_SESSION['PostData'], 'OFF_DESC'); ?>" />
      </div>
      <div class="FieldGroup">
        <h3><?php echo PE\GetColHead('STATUS'); ?>:</h3>
        <select name="STATUS">
          <?php
          $Data->show_sel("status", "status_desc", "select status,status_desc "
                  . "from " . MySQL_Pre . "DESG_STATUS", PE\GetVal($_SESSION['PostData'], 'STATUS', TRUE));
          ?>
        </select>
      </div>

      <div class="FieldGroup">
        <h3><?php echo PE\GetColHead('HOMEBLOCK_CODE'); ?>:</h3>
        <select name="HOMEBLOCK_CODE">
          <?php
          $Data->show_sel("block_municd", "block_muni_nm", "select block_municd,block_muni_nm "
                  . "from " . MySQL_Pre . "Block_muni", PE\GetVal($_SESSION['PostData'], 'HOMEBLOCK_CODE', TRUE));
          ?>
        </select>
      </div>

      <div class="FieldGroup">
        <h3><?php echo PE\GetColHead('BASICpay'); ?>:</h3>
        <input type="text" name="BASICpay" size="5" maxlength="5" value="<?php
        $BX = PE\GetVal($_SESSION['PostData'], 'BASICpay');
        if ($BX < 6600) {
          echo "";
        } else {
          echo PE\GetVal($_SESSION['PostData'], 'BASICpay');
        }
        ?>" />
      </div>
      <div class="FieldGroup">
        <h3><?php echo PE\GetColHead('gender'); ?>:</h3>
        <select name="gender">
          <?php
          $Data->show_sel("gender", "gender_desc", "select gender,gender_desc "
                  . "from " . MySQL_Pre . "GENDER", PE\GetVal($_SESSION['PostData'], 'gender', TRUE));
          ?>
        </select>
      </div>
      <div class="FieldGroup">
        <h3><?php echo PE\GetColHead('mobile'); ?>:</h3>
        <input type="text" name="mobile" size="12" maxlength="10" value="<?php echo PE\GetVal($_SESSION['PostData'], 'mobile'); ?>" />
      </div>
      <div style="clear:both;"></div>
      <div style="clear:both;"></div><hr />
      <?php if (PE\GetVal($_SESSION, 'Step') === "A") { ?>
        <input type="submit" value="Save" name="AppSubmit" />
        <?php
      } else {
        //	<input type="submit" value="Save and Show Previous" name="AppSubmit" />
        //  <input type="submit" value="Save and Show Next" name="AppSubmit" />
        ?>
        <input type="submit" value="Update" name="AppSubmit" />
        <!--<input type="submit" value="Delete" name="AppSubmit" />-->
      <?php }
      ?>
    </form>
    <?php
    //echo "Fields: " . count($_SESSION['PostData']);
    //print_r($_SESSION['PostData']);
    ?>
    <pre><?php // print_r($_POST);                     ?></pre>
  </div>
  <div class="pageinfo"><?php PE\pageinfo(); ?></div>
  <div class="footer"><?php PE\footerinfo(); ?></div>
</body>
</html>
