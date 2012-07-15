<?php
/******************************************/
// db_utf8_fix.php                        //
// Author: J. van Hemert                  //
//                                        //
// Fixes encoding when utf-8-encoded data //
// is stored in tables with other (e.g.   // 
// latin_swedish_ci) encoding.            //
// Will convert all columns in all tables //
// to utf8_general_ci.                    //
// Run from fileadmin folder in TYPO3     //
// installation.                          //
/******************************************/

//Set to TRUE to generate an enormous amount of debug output with
//analysis of table structure.
define("DEBUG", FALSE);
//Set to FALSE to really convert the database
define("SIMULATE", FALSE);


require_once ('../typo3conf/localconf.php');

echo str_repeat(' ', 256);
?>
<html>
<head>
<style type="text/css">
  .normal {
    color: black;
  }
  .okay {
    color: green;
  }
  .label {
    color: blue;
  }
  .error {
    color: red;
  }
</style>
</head>
<body>
<?php

$tables = array();
$typeconv = array(
  "char" => "binary",
  "text" => "blob",
);
$db = mysql_connect($typo_db_host, $typo_db_username, $typo_db_password, TRUE);
if (!is_resource($db)) {
  die("Could not connect to db!: " . mysql_error());
}
if (mysql_select_db($typo_db, $db) === FALSE) {
  die("Could not select database!: " . mysql_error());
}


$sql = "SHOW TABLES;";

$db_res = mysql_query($sql, $db);

if (!is_resource($db_res)) {
  die ("Could not get query result!: " . mysql_error() . "\n" . $sql);
}

while ($row = mysql_fetch_array($db_res, MYSQL_NUM)) {
  if (DEBUG) var_dump($row);
  $tables[] = $row[0];
}

foreach ($tables as $table) {
  echo "<div><span class=\"label\">".$table.": </span><span class=\"normal\">";
  $sql = "SHOW FULL COLUMNS FROM `$table`;";
  $db_res = mysql_query($sql, $db);
  if (!is_resource($db_res)) {
    die ("Could not get table data!: " . mysql_error() . "\n" . $sql);
  }
  $columns = array();
  while ($row = mysql_fetch_assoc($db_res)) {
    if (DEBUG) {
      echo "column: ";
      var_dump($row);
    }
    $columns[] = $row;
  }
  foreach ($columns as $column) {
    set_time_limit(60);
    $oldtype = $column['Type'];
    if (DEBUG) echo "Original: ".$column['Type']."\n";
    $column['Type'] = str_replace(array_keys($typeconv), array_values($typeconv), $column['Type']);
    if (DEBUG) echo "modified: ".$column['Type']."\n";
    if ($column['Type'] != $oldtype) {
      $column['Null'] = (strtolower($column['Null']) == "yes") ? "NULL" : "NOT NULL";
      $column['Default'] = (is_numeric($column['Default'])) ? $column['Default'] : ($column['Default'] === "NULL")? $column['Default'] : "'{$column['Default']}'";
      /*$sql = "ALTER TABLE `$table` MODIFY COLUMN `{$column['Field']}` {$column['Type']} {$column['Null']} DEFAULT {$column['Default']} {$column['Extra']};";
      if (DEBUG) {
        echo "$sql\n";
      } else {
        if (!SIMULATE) {
          $db_res = mysql_query($sql, $db);
          if (!is_resource($db_res) && mysql_errno($db) != 0) {
            echo "Could not execute query!: " . mysql_error($db) . "\n" . $sql;
          }
        }
      }
*/
      $column['Type'] = str_replace(array_values($typeconv), array_keys($typeconv), $column['Type']);
      $sql = "ALTER TABLE `$table` MODIFY COLUMN `{$column['Field']}` {$column['Type']} CHARACTER SET utf8 COLLATE utf8_general_ci {$column['Null']} DEFAULT {$column['Default']} {$column['Extra']};";
      if (DEBUG) {
        echo "$sql\n";
      } else {
        if (!SIMULATE) {
          $db_res = mysql_query($sql, $db);
          if (!is_resource($db_res) && mysql_errno($db) != 0) {
            echo "Could not execute query!: " . mysql_error($db) . "\n" . $sql;
          }
        }
        echo "."; ob_flush(); flush();
      }
    }
  }

  $sql = "ALTER TABLE `$table` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
  if (DEBUG) {
    echo "$sql\n";
  } else {
    if (!SIMULATE) {
      $db_res = mysql_query($sql, $db);
      if (!is_resource($db_res) && mysql_errno($db) != 0) {
        echo "Could not execute query!: " . mysql_error($db) . "\n" . $sql;
      }
    }
    echo "</span><span class=\"okay\"> OK</span></div>"; ob_flush(); flush();
  }

}
echo "<div><span class=\"label\">DATABASE: </span><span class=\"normal\">";
$sql = "ALTER DATABASE `$typo_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
if (DEBUG) {
  echo "$sql\n";
} else {
  if (!SIMULATE) {
    $db_res = mysql_query($sql, $db);
    if (!is_resource($db_res) && mysql_errno($db) != 0) {
      echo "Could not execute query!: " . mysql_error($db) . "\n" . $sql;
    }
  }
  echo "</span><span class=\"okay\"> OK</span></div>"; ob_flush(); flush();
}

echo "<div>finished converting tables</div>";
mysql_close($db);
?>
</body>
</html>
