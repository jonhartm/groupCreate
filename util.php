<?php

function getCurrentStudents($context_id, $req_instructor_role=true)
{
  global $CFG, $USER, $LINK, $PDOX;
  if ( ! $USER->instructor && $req_instructor_role) die("Requires instructor role");
  $p = $CFG->dbprefix;

  // Get basic grade data
  $stmt = $PDOX->queryDie(
    "SELECT u.user_id, COALESCE(displayname, 'Anonymous') as displayname, email, role
      FROM lti_user AS u
        JOIN lti_membership AS m ON u.user_id = m.user_id
    WHERE m.context_id = :CID AND m.role = 0",
    array(":CID" => $context_id)
  );
  $row = $stmt->fetchAll();
  return $row;
}

function printJSON($json)
{
  echo("<pre>\n");
  echo(htmlentities(json_encode($json, JSON_PRETTY_PRINT)));
  echo("\n</pre>\n");
}

function printVarDump($var)
{
  echo("<pre>\n");
  var_dump($var);
  echo("\n</pre>\n");
}
