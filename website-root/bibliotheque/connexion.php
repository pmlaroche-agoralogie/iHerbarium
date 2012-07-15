<?php

function bd_connect(){
  //$serveur = mysql_connect("localhost","_SHELL_REPLACED_USER_TEST","_SHELL_REPLACED_PWD_TEST"); // TEST
  $serveur = mysql_connect("localhost","_SHELL_REPLACED_USER_PROD","_SHELL_REPLACED_PWD_PROD"); // PRODUCTION
  if (!$serveur)
    {
      if($debug_level>0)echo mysql_error();
      die('');
    }
  
  //$bd = mysql_select_db('_SHELL_REPLACED_DATABASE_TEST', $serveur); // TEST
  $bd = mysql_select_db('_SHELL_REPLACED_DATABASE_PROD', $serveur); // PRODUCTION
  if (!$bd)
    {
      if($debug_level>0)echo mysql_error();
      die ('');
    }
}
?>
