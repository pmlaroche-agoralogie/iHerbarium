<?php

require_once ("configuration/connexion.inc");
require_once ("scripts/gestiontemplates.php");
require_once ("scripts/url_parlante_se.inc");

$refTemplate = initTemplate("templates/systeme_expert.html");

$refDroitsReserves = "templates/texte_droits_reserves.html";
$refScriptsBasPage = "templates/scripts_bas_page_$gamme.html";

$infosPlantes['gamme'] = $gamme;

$infosPlantes['msgdroitsreserves'] = file_get_contents($refDroitsReserves);
$infosPlantes['scriptsbaspage'] = file_get_contents($refScriptsBasPage);

$valeurs_criteres = array(
	"fleur" => array(
		"blanche" => 0,
		"jaune" => 1,
		"rose" => 2,
		"rouge" => 3,
		"verte" => 4,
		"violette" => 5,
		"mauve" => 6,
		"bleue" => 7
	),
	"feuille" => array(
		"Trifoliée" => 5,
		"Allongée" => 1,
		"Pennée" => 4,
		"Lanière" => 0,
		"Découpée" => 3,
		"Entière" => 2
	),
	"milieu" => array(
		"Aquatique" => 0,
		"Humide" => 1,
		"Sec" => 2,
		"Ni sec, ni humide" => 3
	),
	"altitude" => array(
		"Moins de 600 m" => 0,
		"De 600 à 1000 m" => 1,
		"Plus de 1000 m" => 2
	),
	"port" => array(
		"Dressé" => 0,
		"Rampant" => 1,
		"Intermédiaire" => 2
	),
	"taille" => array(
		"0-5" => 2.5,
		"5-10" => 7.5,
		"10-20" => 15,
		"20-40" => 30,
		"40-80" => 60,
		"80-120" => 100,
		"120-160" => 140,
		"160 ou plus" => 190
	)
);

$poids_criteres = array(
	"fleur" => array(
		array(20, 10, 0, 0, 0, 0, 0, 0, 10),
		array(10, 20, 0, 0, 0, 0, 0, 0, 10),
		array(5, 0, 20, 15, 0, 3, 5, 0, 10),
		array(0, 0, 15, 20, 0, 5, 3, 0, 10),
		array(0, 0, 0, 0, 20, 0, 0, 0,10),
		array(0, 0, 3, 5, 0, 20, 15, 12, 10),
		array(0, 0, 5, 3, 0, 15, 20, 12, 10),
		array(0, 0, 0, 0, 0, 12, 12, 20, 10),
		array(10, 10, 10, 10, 10, 10, 10, 10, 10)
	),
	"feuille" => array(
		array(20, 15, 10, 0, 0, 0, 5),
		array(15, 20, 12, 10, 5, 0, 5),
		array(10, 12, 20, 0, 0, 0, 5),
		array(0, 10, 0, 20, 10, 5, 5),
		array(0, 5, 0, 10, 20, 5, 5),
		array(0, 0, 0, 5, 5, 20, 5),
		array(5, 5, 5, 5, 5, 5, 10)
	),
	"milieu" => array(
		array(30, 20, 0, 0),
		array(20, 15, 2, 8),
		array(0, 2, 15, 8),
		array(5, 5, 5, 8),
		array(5, 5, 5, 5)
	),
	"altitude" => array(
		array(12, 0, 10),
		array(6, 6, 10),
		array(0, 15, 10),
		array(6, 6, 6)
	),
	"port" => array(
		array(10, 0, 5, 3),
		array(0, 10, 5, 3),
		array(3, 5, 8, 8),
		array(3, 3, 3, 3)
	)
);


/* init requete generale
------------------------------------------------------------------------------------------------------- */
function fct_maj_etat_criteres_recherche () {
global $valeurs_criteres;
	$etats_criteres = array();
	foreach ($valeurs_criteres as $libelle_critere => $liste_items) {
		$refcritere = substr($libelle_critere, 0, 3);
		// code item
		if (isset($_GET[$refcritere])) $codecritere = $_GET[$refcritere];
		//elseif (isset($mescookies['criteres'][$refcritere]['code'])) $codecritere = $mescookies['criteres'][$refcritere]['code'];
		else $codecritere = -1;
		// libelle item
		if ($codecritere == "") $libelle_item = "";
		else $libelle_item = array_search($codecritere, $liste_items);
		$etats_criteres[$refcritere] = array("code" => $codecritere, "libelle" => $libelle_item);
	}
	return $etats_criteres;
}


/* affichage moteur de recherche
------------------------------------------------------------------------------------------------------- */
function fct_moteur_recherche ($mescriteres) {
global $valeurs_criteres;
	$moteur = "";
	foreach ($valeurs_criteres as $libelle_critere => $liste_items) {
		$liste_critere = "";
		$refcritere = substr($libelle_critere, 0, 3);
		foreach ($liste_items as $libelle_item => $valeur_item) {
			$mescriteres_encours = $mescriteres;
			if ($libelle_critere == "feuille") $img_critere = "feuille_".strtolower(fct_remove_accents($libelle_item));
			elseif ($libelle_critere == "fleur") $img_critere = "fleur_".strtolower(fct_remove_accents($libelle_item));
			if (($mescriteres[$refcritere]['code'] >= 0) && ($mescriteres[$refcritere]['code'] == $valeur_item)) {
				$class_lien = ' class="criselect"';
				$mescriteres_encours[$refcritere]['code'] = -1;
				if (($libelle_critere == "feuille") || ($libelle_critere == "fleur")) $img_critere .= "_r";
			} else {
				$class_lien = '';
				$mescriteres_encours[$refcritere]['code'] = $valeur_item;
			}
			$url_lien = fct_code_lien_url ($mescriteres_encours);
			if (($libelle_critere == "feuille") || ($libelle_critere == "fleur")) $libelle_item = '<img src="interface/'.$img_critere.'.gif" border="0">';
			$liste_critere .= '<li'.$class_lien.'><a href="'.$url_lien.'">'.$libelle_item.'</a></li>'."\n";
		}
		if ($libelle_critere == "taille") $libelle_critere .= ' (cm)';
		if ($libelle_critere == "feuille") $moteur .= '<ul class="feuille">'."\n";
		elseif ($libelle_critere == "fleur") $moteur .= '<ul class="fleur">'."\n";
		else $moteur .= '<ul>'."\n";
		$moteur .= '<li class="libelle">'.ucfirst($libelle_critere).'</li>'."\n";
		$moteur .= $liste_critere;
		$moteur .= '</ul>'."\n";
	}
	return $moteur;
}


/* liste des 20 fleurs les plus pertinentes pour recherche en cours
------------------------------------------------------------------------------------------------------- */
function fct_liste_fleurs ($mescriteres) {
global $connexion, $poids_criteres, $gamme;
	$sql = "SELECT * FROM fiche_".$gamme." WHERE visible_index = 1 AND taillelmax > 0";
    $result = mysql_query($sql, $connexion);
	if (!$result) {
		$liste_fleurs = "<p><strong>La liste des fleurs n'est pas disponible pour le moment ; veuillez réessayer ultérieurement.</strong></p>";
	} else {
		$nbTrouve = mysql_num_rows($result);
		if ($nbTrouve <= 0) {
			$liste_fleurs = "<p><strong>La liste des fleurs n'est pas disponible pour le moment ; veuillez réessayer ultérieurement.</strong></p>";
		} else {
			$poids_toutes_fleurs = array();
			$infos_toutes_fleurs = array();
			while ($unefleur = mysql_fetch_assoc($result)) {
				$poids_fleur = 0;
				$unefleur['debbug_poids'] = "";
				// calcul poids de chaque critere
				foreach ($poids_criteres as $libelle => $poids) {
					$refcritere = substr($libelle, 0, 3);
					$codecritere = $mescriteres[$refcritere]['code'];
					if ($codecritere >= 0) {
						switch ($libelle) {
							case "fleur":
								$valcritere = ($poids[$codecritere][$unefleur['kod_coul_fleurs']]) * 1.3;
								$poids_fleur = $poids_fleur + $valcritere;
								$unefleur['debbug_poids'] = "fle=".$valcritere;
								break;
							case "feuille":
								$valcritere = $poids[$codecritere][$unefleur['kod_feuille']];
								$poids_fleur = $poids_fleur + $valcritere;
								$unefleur['debbug_poids'] .= ", feu=".$valcritere;
								break;
							case "milieu":
								$valcritere = $poids[$codecritere][$unefleur['kod_milieu']];
								$poids_fleur = $poids_fleur + $valcritere;
								$unefleur['debbug_poids'] .= ", mil=".$valcritere;
								break;
							case "altitude":
								$valcritere = $poids[$codecritere][$unefleur['kod_alt']];
								$poids_fleur = $poids_fleur + $valcritere;
								$unefleur['debbug_poids'] .= ", alt=".$valcritere;
								break;
							case "port":
								$valcritere = $poids[$codecritere][$unefleur['kod_port']];
								$poids_fleur = $poids_fleur + $valcritere;
								$unefleur['debbug_poids'] .= ", por=".$valcritere;
								break;
						}
					}
				}
				// poids critere taille
				$codecritere = $mescriteres['tai']['code'];
				$poids_taille = fct_calcul_proximite_taille($unefleur['taillemin'], $unefleur['taillelmax'], $codecritere );
				$poids_fleur = $poids_fleur + $poids_taille;
				$unefleur['debbug_poids'] .= ", tai=".floor($poids_taille);
				// poids critere invisible mois floraison
				$mois_observation = date("n");
				$intervalle = $unefleur['kod_mois_flore2'] - $unefleur['kod_moisfolre1'];
				$poids_mois = 0;
				if ($intervalle > 0) {
					// mois observation comprise entre debut et fin de floraison
					if (($mois_observation >= $unefleur['kod_moisfolre1']) && ($mois_observation <= $unefleur['kod_mois_flore2'])) {
						$poids_mois = 3;
					} else { // mois observation = 1 mois precedent ou 1 mois suivant période de floraison
						$mois_suivant = fmod(($unefleur['kod_mois_flore2']+1), 12);
						$mois_precedent = fmod((12+$unefleur['kod_moisfolre1']-1), 12);
						if ($mois_precedent == 0) $mois_precedent = 12;
						if (($mois_observation == $mois_precedent) || ($mois_observation == $mois_suivant)) {
							$poids_mois = 2;
						} else { // mois observation = 2 mois precedent ou 2 mois suivant période de floraison
							$mois_suivant = fmod(($unefleur['kod_mois_flore2']+2), 12);
							$mois_precedent = fmod((12+$unefleur['kod_moisfolre1']-2), 12);
							if ($mois_precedent == 0) $mois_precedent = 12;
							if (($mois_observation == $mois_precedent) || ($mois_observation == $mois_suivant)) $poids_mois = 1;
						}
					}
				}
				$poids_fleur = $poids_fleur + $poids_mois;
				$unefleur['debbug_poids'] .= ", mois=".floor($poids_mois);
				// enregistre
				$poids_toutes_fleurs[$unefleur['numId']] = $poids_fleur;
				$infos_toutes_fleurs[$unefleur['numId']] = $unefleur;
			} // fin while
			// trie selon le poids le plus lourd
			arsort($poids_toutes_fleurs);
			if (count($poids_toutes_fleurs) > 18) $nbaffichage = 18;
			else $nbaffichage = count($poids_toutes_fleurs);
			$liste_fleurs = "";
			$liste_encours = "";
			$cpt = 0;
			foreach ($poids_toutes_fleurs as $ref => $poids) {
				$infos_fleur_encours = $infos_toutes_fleurs[$ref];
				// recupere photo
				$sqlphoto = 'SELECT * FROM diapo_'.$gamme.' WHERE nomFic LIKE "'.$infos_fleur_encours['racinenomfic'].'%" ORDER BY auteur';
				$result = mysql_query($sqlphoto, $connexion);
				$photofleur = mysql_fetch_assoc($result);
				$lien_fiche = '<a href="fiche_'.$infos_fleur_encours['racinenomfic'].'.html">';
				$liste_encours .= '<div class="unefleur">'."\n";
				$liste_encours .= '<a href="fiche_'.$infos_fleur_encours['racinenomfic'].'.html">';
				$liste_encours .= '<img src="phototheque/vignette_'.$gamme.'/'.$photofleur['nomFic'].'.jpg" border="0"><br />'."\n";
				$liste_encours .= $infos_fleur_encours['nc']."<br /> (".$infos_fleur_encours['ns'].")"."\n";
				$liste_encours .= '</a>';
				// pour activer le systeme de debbug des poids des criteres
				// if ($infos_fleur_encours['debbug_poids'] != "") $liste_encours .= '<br />----------<br />'.$infos_fleur_encours['debbug_poids'];
				$liste_encours .= '</div>'."\n";
				$cpt ++;
				if (fmod($cpt, 3) == 0) {
					$liste_fleurs .= '<div class="lignefleurs">'."\n";
					$liste_fleurs .= $liste_encours;
					$liste_fleurs .= '</div>'."\n";
					$liste_encours = "";
				}
				if ($cpt >= $nbaffichage) break;
			}
		}
	}
	return $liste_fleurs;
}


/* *********************************************************************************************************
** FONCTIONS GENERALES
** ********************************************************************************************************* */

/* liste des 20 fleurs les plus pertinentes pour recherche en cours
------------------------------------------------------------------------------------------------------- */
function fct_calcul_proximite_taille($tmini, $tmaxi, $taille_observee) {
	if ($taille_observee <= 0) { 
		$valtaille = 0;
	} else {
    	if ($taille_observee < $tmini) { 
        	$valtaille = log(4 * ($taille_observee / ($tmini * 1.01))) * 10;
    	} else {
      		if ($taille_observee < $tmaxi) $valtaille = log(4) * 10;
      		else $valtaille = log(4 * ($tmaxi / ($taille_observee * 1.01))) * 10;
		}
    	if ($taille_observee < ($tmini / 2))  $valtaille = $valtaille - 20;
 	    if ($taille_observee < ($tmini / 3))  $valtaille = $valtaille - 30;
   	 	if ($taille_observee > ($tmaxi * 1.5)) $valtaille = $valtaille - 10;
   	 	if ($taille_observee > ($tmaxi * 2))  $valtaille = $valtaille - 25;
    	if ($taille_observee > ($tmaxi * 3))  $valtaille = $valtaille - 45;
    }
	if ($valtaille < 0) $valtaille = 0;
	return $valtaille;
}


/* *********************************************************************************************************
** FONCTIONS URL PARLANTES
** ********************************************************************************************************* */

/* réecriture url pour referencement google
------------------------------------------------------------------------------------------------------- */
function fct_code_lien_url ($mescriteres) {
// recup parametres par defauts (pour eviter afficher tri et affichage si valeurs par defaut)
global $url_parlante; 
    $url_construite = "";
	// CRITERES de selection en mode catalogue
	foreach ($mescriteres as $ref => $infoscritere) {
		$section = $ref."~".$infoscritere['code'];
  		if (array_key_exists($section,$url_parlante)) $section = $url_parlante[$section];
 		if ($infoscritere['code'] >= 0) $url_construite .= "_".$section;       
	}
	if ($url_construite == "") $url_construite = "reconnaitre-une-fleur";
	else $url_construite = "fleurs".$url_construite;
	// REECRITURE URL
 	$url_code = "systexpert.php?originalurl=".$url_construite;
	if ($_SERVER['HTTP_HOST'] != "127.0.0.1")  $url_code = $url_construite.".html";
	return $url_code;
}


/* decode url avant affichage de la page (appel dans localconf)
------------------------------------------------------------------------------------------------------- */
function fct_decode_lien_url ($urlpage) {
global $url_parlante;
	// recupere ligne de produits et criteres (separes par _)
	$pos = strpos($urlpage, "_");
	if ($pos === false) {
		$gauche = $urlpage;
	} else {
		$gauche= substr($urlpage,0,$pos);
		$droite = substr($urlpage,$pos+1);
		if (substr($droite,-5) == ".html") $droite = substr($droite,0,-5);
	}
	// remplace libelles criteres par ref et code critere (ex: tho = 00801)
	if (isset($droite)) {
		$paramd = explode("_", $droite);
		foreach ($paramd as $libelle) {	
			$code_paire = array_search($libelle, $url_parlante);
			if ($code_paire === false) $code_paire = $libelle;
			$pos = strpos($code_paire,"~"); 
			$couple = explode("~",$code_paire);
			$_GET[$couple[0]] = $couple[1]; 
		}
	}
}


/* construction du titre de la page
------------------------------------------------------------------------------------------------------- */
function fct_titre_page ($mescriteres) {
	$titre_page = "";
	foreach ($mescriteres as $ref => $infoscritere) {
		if (($infoscritere['code']  >= 0) && ($infoscritere['libelle'] != "")) {
			if ($titre_page != "") $titre_page .= ", ";
			$titre_page .= $infoscritere['libelle'];
		}
	}
	if ($titre_page == "") $titre_page = "Un expert vous aide à identifier une fleur";
	else $titre_page = "Fleurs correspondant à : ".$titre_page;
	return $titre_page;
}


/* efface tous les accents                                                                  
-------------------------------------------------------------------------------------------------- */
function fct_remove_accents ($chaine) {
   return ( strtr( $chaine, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"));
}	



/* *********************************************************************************************************
** MAIN
** ********************************************************************************************************* */

$urlpage = $_SERVER["REQUEST_URI"];
fct_decode_lien_url($urlpage);
$mescriteres = fct_maj_etat_criteres_recherche ();

$infosPlantes['titrepage'] = fct_titre_page ($mescriteres);
$infosPlantes['moteur_recherche'] = fct_moteur_recherche ($mescriteres);
$infosPlantes['liste_fleurs'] = fct_liste_fleurs ($mescriteres);

$mepDetail = renseigneModeleMep($infosPlantes,$refTemplate);
affichageTempSimple($mepDetail);

?>