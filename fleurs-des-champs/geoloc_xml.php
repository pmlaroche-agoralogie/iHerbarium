<?php

require_once ("configuration/connexion.inc");

$erreur = false;
$where = "obs.base = '$gamme'";

// visualisation d'une plante precise
if (isset($_GET['ns'])) {
	$ns = $_GET['ns'];
	// recupere code plante
	$req="SELECT * FROM fiche_$gamme WHERE racinenomfic='$ns'";
	$resultReq = mysql_query($req);
	if ($resultReq) {
		$maPlante = mysql_fetch_assoc($resultReq);
		$code = $maPlante['numId'];
		// ajoute parametre requete pour geoloc
		$where .= " AND obs.numId = $code";
	} else {
		$erreur = true;
	}
}

// visualisation privee : acces avec login / password
if ((isset($_GET['idt'])) && (isset($_GET['pwd']))) { 
	$consultation_privee = true;
	$login = $_GET['idt'];
	$password = $_GET['pwd'];
	if (MD5(substr($login,5,10)) == $password)  $where .= " AND obs.qui_email = '$login'";
	else  $erreur = true;
} else {
	$consultation_privee = false;
}

// recupere observations
$xml_geoloc = '<?xml version="1.0" encoding="UTF-8"?>';  //ISO-8859-1 ou UTF-8
$xml_geoloc .= '<markers>';
if (!$erreur) {
	$sql="SELECT DISTINCT * FROM fiche_$gamme fic, observations obs WHERE fic.numId = obs.numId AND ".$where;
	$result = mysql_query($sql, $connexion);
	if ($result) {
		while ($uneObservation = mysql_fetch_assoc($result)) {
			$affiche_marker = false;
			if (($consultation_privee) || ($uneObservation['visible'] == 1))  $affiche_marker = true;
			if ($affiche_marker) {
				$xml_geoloc .= '<marker';
				$xml_geoloc .= ' ns="'.utf8_encode($uneObservation['ns']).'"';
				$xml_geoloc .= ' nc="'.utf8_encode($uneObservation['nc']).'"';
				$xml_geoloc .= ' base="'.$uneObservation['base'].'"';
				$xml_geoloc .= ' dateobs="'.$uneObservation['date_observation'].'"';
				$xml_geoloc .= ' qui="'.utf8_encode($uneObservation['qui']).'"';
				$commentaire_public = str_replace('"',"''",$uneObservation['commentaire_public']);
				$xml_geoloc .= ' commentairepublic="'.utf8_encode(stripslashes($commentaire_public)).'"';
				if ($consultation_privee) { // visualisation privee
					$commentaire_prive = str_replace('"',"''",$uneObservation['commentaire_prive']);
					$xml_geoloc .= ' commentaireprive="'.utf8_encode(stripslashes($commentaire_prive)).'"';
					$xml_geoloc .= ' lat="'.$uneObservation['lat_reel'].'"';
					$xml_geoloc .= ' lng="'.$uneObservation['lng_reel'].'"';
				} else { // visualisation publique
					$xml_geoloc .= ' commentaireprive=""';
					if ($uneObservation['masquer_precision']) {
						$xml_geoloc .= ' lat="'.$uneObservation['lat_flou'].'"';
						$xml_geoloc .= ' lng="'.$uneObservation['lng_flou'].'"';
					} else {
						$xml_geoloc .= ' lat="'.$uneObservation['lat_reel'].'"';
						$xml_geoloc .= ' lng="'.$uneObservation['lng_reel'].'"';
					}
				}
				$xml_geoloc .= ' />';
			}
		}
	}
}
$xml_geoloc .= "</markers>";

// ecriture XML pour recuperation en javascript
$xml_geoloc = str_replace(urldecode("%0B"), " ",$xml_geoloc);

echo($xml_geoloc);

?>
