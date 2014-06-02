<?php

require_once ("configuration/connexion.inc");
require_once ("scripts/gestiontemplates.php");

$refTemplate = initTemplate("templates/geoloc_consultation.html");

$refDroitsReserves = "templates/texte_droits_reserves.html";
$refScriptsBasPage = "templates/scripts_bas_page_$gamme.html";

$infosPlantes['gamme'] = $gamme;
$infosPlantes['apigeoloc'] = $apigeoloc;

$infosPlantes['msgdroitsreserves'] = file_get_contents($refDroitsReserves);
$infosPlantes['scriptsbaspage'] = file_get_contents($refScriptsBasPage);

// recupere nom scientifique si visualisation d'une plante précisément
if (isset($_GET['ns'])) {
	$ns = $_GET['ns'];
	$req="SELECT * FROM fiche_$gamme WHERE racinenomfic='$ns'";
	$resultReq = mysql_query($req);
	if ($resultReq) {
		$maPlante = mysql_fetch_assoc($resultReq);
		$nom_plante = $maPlante['ns'];
	} else {
		$nom_plante = "";
	}
} else {
	$ns = "";
}
$infosPlantes['geoloc'] = "param_ns = '".$ns."'; ";

// ESPACE PRIVE : ACCES AVEC LOGIN / PASSWORD
if ((isset($_GET['idt'])) && (isset($_GET['pwd']))) { 
	$login = $_GET['idt'];
	$password = $_GET['pwd'];
	if ($ns == "")  $infosPlantes['titregeoloc'] = "G&eacute;olocalisation de toutes vos observations";
	else  $infosPlantes['titregeoloc'] = "G&eacute;olocalisation de vos observations de <em>".$nom_plante."</em>";
	if (MD5(substr($login,5,10)) == $password) { // identification reussie
		$infosPlantes['geoloc'] .= "param_idt = '".$login."'; ";
		$infosPlantes['geoloc'] .= "param_pwd = '".$password."'; ";
		// msg perso pour signifier espace prive
		$sql="SELECT DISTINCT qui FROM observations WHERE qui_email = '$login'";
		$result = mysql_query($sql, $connexion);
		if ($result) {
			$observateur = mysql_fetch_assoc($result);
			$infosPlantes['msgperso'] = "<p>Bienvenue <strong>".$observateur['qui']."</strong> dans votre espace privé de consultation de vos observations.<br /><br />Nous vous rappelons que vous seul avez accès à vos observations aux latitude / longitude exactes que vous avez indiquées. Les observations pour lesquelles vous avez souhaité qu'elles ne soient pas visibles aux autres internautes seront masquées dans l'espace public de géolocalisation ; celles que vous souhaitiez montrer de manière imprécise seront géolocalisées à des latitude / longitude recalculées.</p>";
		} else {
			$infosPlantes['msgperso'] = "";
		}
	} else { // echec identification
		$infosPlantes['msgperso'] = "<p>Votre identification a échouée ; veuillez utiliser le lien qui vous a été envoyé par email pour être identifié correctement et accéder à la géolocalisation de vos observations.</p>";
	}
// ESPACE PUBLIC : ACCES SANS LOGIN / PASSWORD
} else { 
	$infosPlantes['geoloc'] .= "param_idt = ''; ";
	$infosPlantes['geoloc'] .= "param_pwd = ''; ";
	if ($ns == "")  $infosPlantes['titregeoloc'] = "G&eacute;olocalisation de toutes les observations";
	else  $infosPlantes['titregeoloc'] = "G&eacute;olocalisation des observations de <em>".$nom_plante."</em>";
	$infosPlantes['msgperso'] = "";
}

$mepDetail = renseigneModeleMep($infosPlantes,$refTemplate);
affichageTempSimple($mepDetail);

?>