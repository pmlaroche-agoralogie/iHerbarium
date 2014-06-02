<?php

require_once ("configuration/connexion.inc");
require_once ("scripts/gestiontemplates.php");

$refTemplate = initTemplate("templates/liste_plantes.html");

$refDroitsReserves = "templates/texte_droits_reserves.html";
$refScriptsBasPage = "templates/scripts_bas_page_$gamme.html";

$infosPlantes['gamme'] = $gamme;

$infosPlantes['msgdroitsreserves'] = file_get_contents($refDroitsReserves);
$infosPlantes['scriptsbaspage'] = file_get_contents($refScriptsBasPage);

if (!isset($_GET['tri']))  $ordretri = "nc";
else  $ordretri = $_GET['tri'];
$sql="SELECT fiche_$gamme.ns, fiche_$gamme.nc, fiche_$gamme.numId FROM fiche_$gamme WHERE visible_index = 1 ORDER BY $ordretri";
$result = mysql_query($sql, $connexion);
if ($result) {
	$infosPlantes['plantes'] = "";
	$numLi = 1;
	while ($unePlante = mysql_fetch_assoc($result)) {
		if ($numLi == 1)  $infosPlantes['plantes'] .= '<tr class="ligne_fonce">';
		else  $infosPlantes['plantes'] .= '<tr class="ligne_claire">';
	    $infosPlantes['plantes'] .= '<td><a href="fiche.php?code='.$unePlante['numId'].'">'.$unePlante['nc'].'</a></td>';
		$infosPlantes['plantes'] .= '<td>'.$unePlante['ns'].'</td>';
		$infosPlantes['plantes'] .= '</tr>';
		$numLi = abs($numLi-1);
	}
} else {
	$infosPlantes['plantes'] = "";
}

$mepDetail = renseigneModeleMep($infosPlantes,$refTemplate);
affichageTempSimple($mepDetail);

?>
