<?php
require_once "../config.php";
require_once "util.php";

use \Tsugi\Core\LTIX;
use \Tsugi\UI\Output;

$LTI = LTIX::requireData();
$gift = $LINK->getJson();

Output::headerJson();

echo(json_encode(getCurrentStudents($LTI->context->id), JSON_PRETTY_PRINT));
