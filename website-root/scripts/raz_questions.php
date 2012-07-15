<?php 

echo "Liste de taches videe"; 
include("../bibliotheque/common_functions.php");
bd_connect();

$sql_ref = "TRUNCATE `iherba_task`";
$result_ref = mysql_query($sql_ref)or die ('Erreur SQL !'.$sql_ref.'<br />'.mysql_error());

?>