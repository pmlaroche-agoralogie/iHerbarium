<?php

//phpinfo();

$serveur = '';
$login = 'xxxxx';
$motpasse = 'xxxxx';

$connexion = mysql_connect ($serveur,$login,$motpasse) or die ("accès à la base impossible");

// nom de la base
mysql_select_db("basephotonature");

if ($_SERVER['HTTP_HOST']=="127.0.0.1")  $refsite = substr(substr($_SERVER['REQUEST_URI'],1),0,strpos(substr($_SERVER['REQUEST_URI'],1),"/"));
else  $refsite = $_SERVER['SERVER_NAME'];

switch ($refsite) {

	case 'www.fleurs-des-champs.com' :
	case 'fleursdeschamps' :
		$gamme = "fleurs";
		$apigeoloc = "xxxxx";
		break;
		
	case 'www.champignons-de-france.com' :
	case 'champignonsdefrance' :
		$gamme = "champi";
		$apigeoloc = "xxxxx";
		break;
		
	case 'www.arbres-de-france.com' :
	case 'www.feuilles-d-arbres.net' :
        case 'bd.feuilles-d-arbres.net' :
	case 'arbresdefrance':
		$gamme = "arbre";
		$apigeoloc = "xxxxx";
		break;
		
	default:
		$gamme = "fleurs";
		$apigeoloc = "xxxxx";
		break;
}
	
?>
