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
  $LINK->setJsonKey("current",json_decode($content)->groups);
  $settings = json_decode($content)->settings;
  SETTINGS::linkSet("group_by", $settings->group_by);
  SETTINGS::linkSet("number", $settings->number);
} else {
  $content = $LINK->getJsonKey("current");

}

if (!$USER->instructor) {
  // Figure out what group this student is in
  $my_group = null;
  foreach ($content as $group) {
    if (in_array($USER->id, $group["ids"])) {
      $my_group = $group;
      $my_group["members"] = [];
      foreach ($group["ids"] as $id) {
        if ($id !== $USER->id) {
          array_push($my_group["members"], $USER::loadUserInfoBypass($id));
        }
      }
    }
  }
  $content = array($my_group);
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
<?php
if ($USER->instructor) {
?>
  <div class="col-md-12">
    <div class="btn-group col-md-4">
<?php
  if (SETTINGS::linkGet("group_by", "groups_by_num") === "groups_by_num"){
?>
    <button type="button" class="btn btn-default col-md-6 group_size_by_btn" value="groups_by_size">Max Group Size</button>
    <button type="button" class="btn btn-default col-md-6 group_size_by_btn active" value="groups_by_num">Number of Groups</button>
<?php
  } else {
?>
    <button type="button" class="btn btn-default col-md-6 group_size_by_btn active" value="groups_by_size">Max Group Size</button>
    <button type="button" class="btn btn-default col-md-6 group_size_by_btn" value="groups_by_num">Number of Groups</button>
<?php
  }
?>
    </div>
    <div class="col-md-2">
      <input class="form-control" id="number" type="number" value="<?= SETTINGS::linkGet("number", 2) ?>" min=2>
    </div>
    <button type="button" class="btn btn-primary col-md-6" id="create_groups">Placeholder...</button>
  </div>
<?php
} else {
?>
<div class="student-group-title">
  <h1 id="student-group-title">(placeholder)</h1>
  <h2 id="student-group-subtitle">Group Members:</h2>
  <h2>
    <a id="email-all" href="">E-mail all group members</a>
  </h2>
</div>
<?php
}
?>
  <div class="col-md-12">
    <div id="group_div_container">No Groups Set...</div>
  </div>
</div>
<?php
if ($USER->instructor) {
?>
  <button type="button" class="btn btn-lg btn-default col-md-12" id="submit_groups" style="display:none;">Confirm Groups</button>
<?php
}

$OUTPUT->footerStart();
$OUTPUT->templateInclude(array('list','group_member'));
?>
<script>
STUDENT_LIST = {}
$(document).ready(function() {
  $.getJSON('<?= addSession('getStudentList.php')?>', function(students) {
    STUDENT_LIST = students;
  }).done(function() {
    // Prime the create button with the proper text
    set_button_text();
<?php
    if ($USER->instructor) {
    // if the user is an instructor, draw all of the groups together
?>
      drawGroups(<?= json_encode($content) ?>);
<?php
    } else {
    // if the user is a student, draw the student's group person by person
?>
      drawStudentGroup(<?= json_encode($content) ?>);
<?php
    }
?>
  }).fail(function(jq, status, error) {
    console.log("I've failed...");
    console.log(jq);
    console.log(status);
    console.log(error);
  });
});
</script>
<script type="text/javascript" src="js/scripts.js"></script>
<?php
$OUTPUT->footerEnd();
?>
