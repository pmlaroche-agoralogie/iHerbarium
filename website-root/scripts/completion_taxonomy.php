<?php
// use http://wiki.openstreetmap.org/wiki/Nominatim#Example

require_once("../bibliotheque/common_functions.php");

function dieanddebug()
{ echo "die";
 die(mysql_error());
 //die();
}

bd_connect();
if(!isset($_GET['debut']))die();
$partie = $_GET['debut'];
$partie = mysql_real_escape_string($partie);

if(strlen($partie)>3){
	header('Content-Type: text/xml;charset=utf-8');
	echo(utf8_encode("<?xml version='1.0' encoding='UTF-8' ?><options>"));

	$partie = utf8_decode($partie);
		$sql = "SELECT * FROM  `iherba_ref_taxonomique` WHERE  `nom_scientifique` LIKE  '%$partie%' ORDER BY nom_scientifique  ";

  	$possibilities = mysql_query($sql) or dieanddebug();
  	$nbTrouve = mysql_num_rows($possibilities);

  	if ($nbTrouve > 0) {   
    	while ($fiche = mysql_fetch_assoc($possibilities)) {
			echo(utf8_encode('<option class="liste_completion">'.str_replace('&','-',$fiche['nom_scientifique']). " ( ".str_replace('&','-',$fiche['family']). ":".str_replace('&','-',$fiche['kingdom']) .") / ".$fiche['reftaxonomiqueplusid'].'</option>')); 
		}
  	}

  	echo("</options>");
  	die();
}
	

?>