<?php

require_once ("configuration/connexion.inc");
require_once ("scripts/gestiontemplates.php");

$refTemplate = initTemplate("templates/index_$gamme.html");

$nomSite['fleurs'] = "Fleurs des champs";
$nomSite['champi'] = "Champignons de France";
$nomSite['arbres'] = "Arbres de France";

$lemail=$_POST["lemail"];

// verif si deja inscrit
$req="SELECT email FROM newsletter WHERE email = '$lemail' AND base = '$gamme'";
$resultReq = mysql_query($req);
$nb = mysql_num_rows($resultReq);
if ($nb>0) {
	$infos['msgnewsletter'] = "<p class='msg_newsletter'>Vous êtes déjà inscrit à la newsletter de ".$nomSite[$gamme].".</p>";
} else {

	// si pas deja inscrit
	if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
     	$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) {
     	$realip = $_SERVER["HTTP_CLIENT_IP"];
	} else {
     	$realip = $_SERVER["REMOTE_ADDR"];
	}
	
	$req = "INSERT INTO newsletter (email,base,code_ip,code_geoloc) VALUES ('$lemail','$gamme','$realip','NA')";
	$resu = mysql_query($req);

	if ($resu) {
		$infos['msgnewsletter'] = "<p class='msg_newsletter'>Votre email a bien été enregistré.<br />";
		$infos['msgnewsletter'] .= "Vous recevrez la prochaine Newsletter de ".$nomSite[$gamme].".</p>";
	} else {
		$infos['msgnewsletter'] = "<p class='msg_newsletter'>Une erreur technique est survenue ; votre inscription à la newsletter a échouée.<br />";
		$infos['msgnewsletter'] .= "Veuillez réessayer ultérieurement.</p>";
	}
}

$mepIndex = renseigneModeleMep($infos,$refTemplate);
affichageTempSimple($mepIndex);

?>