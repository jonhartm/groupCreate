<?php
require_once "../config.php";
require_once "util.php";

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;
use \Tsugi\UI\SettingsForm;

$LTI = LTIX::requireData();

// Handle the POST Data
$p = $CFG->dbprefix;

$content = file_get_contents("php://input");
if (isset($content) && $content != '') {
  $LINK->setJson($content);
}

// Create the view
$OUTPUT->header();
?>
<link rel="stylesheet" type="text/css" href="css/style.css">
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();
?>

<div class="container">
  <div class="col-md-12">
    <div class="btn-group col-md-4">
      <button type="button" class="btn btn-default col-md-6 group_size_by_btn" value="groups_by_size">Max Group Size</button>
      <button type="button" class="btn btn-default col-md-6 group_size_by_btn active" value="groups_by_num">Number of Groups</button>
    </div>
    <div class="col-md-2">
      <input class="form-control" id="number" type="number" value="2" min=0>
    </div>
    <button type="button" class="btn btn-primary col-md-6" id="create_groups">Placeholder...</button>
  </div>
  <div class="col-md-12">
    <div id="group_div_container"></div>
  </div>
</div>

<button type="button" class="btn btn-lg btn-default col-md-12" id="submit_groups" style="display:none;">Confirm Groups</button>

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
    // Prime the create button with the proper text
    set_button_text();
  });
});
</script>
<script type="text/javascript" src="js/scripts.js"></script>
<?php
$OUTPUT->footerEnd();
?>
