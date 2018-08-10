<?php
require_once "../config.php";
require_once "util.php";

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;
use \Tsugi\UI\SettingsForm;

$LTI = LTIX::requireData();

// Handle the POST Data
$p = $CFG->dbprefix;

$student_list = getCurrentStudents($LTI->context->id);

// Create the view
$OUTPUT->header();
?>
<link rel="stylesheet" type="text/css" href="css/style.css">
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();
?>

<div class="container col-md-12">
  <div class="col-md-2">
    <ul class="list-group">
      <?php
      foreach ($student_list as $student) {
        echo("<li class='list-group-item'>{$student['displayname']}</li>");
      }
      ?>
    </ul>
  </div>
  <div class="col-md-2">
    <div class="vcenter">
      <div class="btn-group col-md-12 bot5 no-pad">
        <button type="button" class="btn btn-default col-md-6">Size</button>
        <button type="button" class="btn btn-default col-md-6">Number</button>
      </div>
      <input class="col-md-6" id="number" type="number" value="2">
      <button type="button" class="btn btn-primary col-md-12 top5">Create 2 groups ></button>
    </div>
  </div>
  <div class="col-md-8">
    3 of 3
  </div>
</div>

<?php
$OUTPUT->footerStart();
$OUTPUT->footerEnd();
?>
