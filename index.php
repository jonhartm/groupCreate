<?php
require_once "../config.php";
require_once "util.php";

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;
use \Tsugi\UI\SettingsForm;

$LTI = LTIX::requireData();

// Handle the POST Data
$p = $CFG->dbprefix;

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
    <div id="class_list"></div>
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
    <div id="group_container"></div>
  </div>
</div>

<?php
$OUTPUT->footerStart();
$OUTPUT->templateInclude('list');
?>
<script>
STUDENT_LIST = {}
$(document).ready(function() {
  $.getJSON('<?= addSession('getStudentList.php')?>', function(students) {
    STUDENT_LIST = students;
  }).done(function() {
    console.log(STUDENT_LIST);
    context = {};
    context.students = STUDENT_LIST;
    $('#class_list').append(tsugiHandlebarsRender('list', context));
  });
});
</script>
<?php
$OUTPUT->footerEnd();
?>
