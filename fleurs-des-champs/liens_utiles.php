<?php

require_once ("configuration/connexion.inc");
require_once ("scripts/gestiontemplates.php");

$refTemplate = initTemplate("templates/liens_utiles_$gamme.html");

// pour affichage de la pub google au milieu
$ref_pub_milieu = "templates/pub_adsense_milieu_".$gamme.".html";
$infos['pub_milieu'] = "<p>".file_get_contents($ref_pub_milieu)."<p>";

$refDroitsReserves = "templates/texte_droits_reserves.html";
$refScriptsBasPage = "templates/scripts_bas_page_$gamme.html";

$infos['gamme'] = $gamme;
$infos['msgdroitsreserves'] = file_get_contents($refDroitsReserves);
$infos['scriptsbaspage'] = file_get_contents($refScriptsBasPage);

$mepIndex = renseigneModeleMep($infos,$refTemplate);
affichageTempSimple($mepIndex);

?>