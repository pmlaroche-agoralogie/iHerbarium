<?php

require_once ("configuration/connexion.inc");
require_once ("scripts/gestiontemplates.php");

$refTemplate = initTemplate("templates/liste_plantes.html");

$refDroitsReserves = "templates/texte_droits_reserves.html";
$refScriptsBasPage = "templates/scripts_bas_page_$gamme.html";

$infosPlantes['gamme'] = $gamme;

$infosPlantes['msgdroitsreserves'] = file_get_contents($refDroitsReserves);
$infosPlantes['scriptsbaspage'] = file_get_contents($refScriptsBasPage);

// critere lettre alphabetique
if (!isset($_GET['lettre']))  $infosPlantes['premierelettre'] = "a";
else  $infosPlantes['premierelettre'] = $_GET['lettre'];

// critere tri
if (!isset($_GET['tri']))  $infosPlantes['ordretri'] = "nc";
else  $infosPlantes['ordretri'] = $_GET['tri'];

$sql="SELECT fiche_$gamme.ns, fiche_$gamme.nc, fiche_$gamme.numId, fiche_$gamme.racinenomfic FROM fiche_$gamme WHERE visible_index = 1 AND ( fiche_$gamme.".$infosPlantes['ordretri']." LIKE '".$infosPlantes['premierelettre']."%' OR fiche_$gamme.".$infosPlantes['ordretri']." LIKE '".strtoupper($infosPlantes['premierelettre'])."%') ORDER BY ".$infosPlantes['ordretri'];
$result = mysql_query($sql, $connexion);
if ($result) {
	$nbtrouve = mysql_num_rows($result);
	if ($nbtrouve > 0) {
		$infosPlantes['plantes'] = "";
		$numLi = 1;
		while ($unePlante = mysql_fetch_assoc($result)) {
			if ($numLi == 1)  $infosPlantes['plantes'] .= '<tr class="ligne_fonce">';
			else  $infosPlantes['plantes'] .= '<tr class="ligne_claire">';
	    	//$infosPlantes['plantes'] .= '<td><a href="fiche.php?code='.$unePlante['numId'].'">'.$unePlante['nc'].'</a></td>';
			$infosPlantes['plantes'] .= '<td><a href="fiche_'.$unePlante['racinenomfic'].'.html">'.$unePlante['nc'].'</a></td>';
			$infosPlantes['plantes'] .= '<td><a href="fiche_'.$unePlante['racinenomfic'].'.html" class="libellens">'.$unePlante['ns'].'</a></td>';
			$infosPlantes['plantes'] .= '</tr>';
			$numLi = abs($numLi-1);
		}
	} else {
		$infosPlantes['plantes'] = '<tr><td colspan="2" align="center"><br /><strong>Aucune plante ne correspond à votre recherche.</strong><br /></td></tr>';
	}
} else {
	$infosPlantes['plantes'] = '<tr><td colspan="2" align="center"><br /><strong>Aucune plante ne correspond à votre recherche.</strong><br /><br /></td></tr>';
}

$mepDetail = renseigneModeleMep($infosPlantes,$refTemplate);
affichageTempSimple($mepDetail);

?>
