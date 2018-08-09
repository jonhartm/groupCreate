<?php
require_once "../config.php";
require_once "util.php";

use \Tsugi\Util\LTI;
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;

$LTI = LTIX::requireData();

// Handle the POST Data
$p = $CFG->dbprefix;
if ( count($_POST) > 0 ) {
  header( 'Location: '.addSession('index.php') ) ;
}

$new_users = getRandomUsers($_GET['num']);

foreach ($new_users as $user) {
  addStudentToDB($user);
}

// Create the view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();

printVarDump($new_users);

$OUTPUT->footerStart();
$OUTPUT->footerEnd();

function parse_name_list($file) {
  $names = array();
  $handle = fopen($file, "r");
  if ($handle) {
    while (($line = fgets($handle)) !== false) {
      $split = preg_split('@ @', $line, NULL, PREG_SPLIT_NO_EMPTY);
      array_push($names,$split[0]);
    }
  }
  return $names;
}

function getRandomUsers($count) {
  $users = array();
  $first_names = parse_name_list("dummydatagen/first_names.txt");
  $last_names = parse_name_list("dummydatagen/last_names.txt");

  for ($i=0; $i < $count; $i++) {
    $user = array();

    $first = ucfirst(strtolower(getRandomFromArray($first_names)));
    $last = ucfirst(strtolower(getRandomFromArray($last_names)));

    $user['name'] = $first." ".$last;
    $user['email'] = strtolower($first[0].$last.mt_rand(10,99)."@ischool.edu");
    $user['sha256'] = hash('sha256', $user['name'].time());
    $user['created_at'] = time();

    array_push($users, $user);
    sleep(1);
  }
  return $users;
}

function getRandomFromArray($arr) {
  return $arr[mt_rand(0,sizeof($arr)-1)];
}

function addStudentToDB($user) {
  global $CFG, $PDOX;
  $sql = "INSERT INTO {$CFG->dbprefix}lti_user
    (user_sha256, key_id, displayname, email, created_at) VALUES
    (:sha,1,:name,:email,:now)";
  $values = array(
      ':sha' => $user['sha256'],
      ':name' => $user['name'],
      ':email' => $user['email'],
      ':now' => time()
  );
  $PDOX->queryDie($sql, $values);

  $user_id = $PDOX->lastInsertId();

  $sql = "INSERT INTO {$CFG->dbprefix}lti_membership
    (context_id, user_id, deleted, role, created_at) VALUES
    (1,:user_id,0,0,:now)";
  $values = array(
      ':user_id' => $user_id,
      ':now' => time()
  );
  $PDOX->queryDie($sql, $values);
}

?>
