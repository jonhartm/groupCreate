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
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();

?>
<a href="createDummyData.php?num=0">Create Dummy Data</a>
<?php

printVarDump(getCurrentStudents($LTI->context->id));

$OUTPUT->footerStart();
$OUTPUT->footerEnd();
?>
