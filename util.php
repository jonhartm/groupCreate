<?php

function printJSON($json)
{
  echo("<pre>\n");
  echo(htmlentities(json_encode($json, JSON_PRETTY_PRINT)));
  echo("\n</pre>\n");
}
