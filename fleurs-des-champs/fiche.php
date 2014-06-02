<?php

require_once ("configuration/connexion.inc");
require_once ("scripts/gestiontemplates.php");

$refDroitsReserves = "templates/texte_droits_reserves.html";
$refScriptsBasPage = "templates/scripts_bas_page_$gamme.html";


// sans réécriture
$mode="";
if (isset($_GET['mode']) || isset($_GET['code'])) {
	if (isset($_GET['mode'])){
		$mode="detail";
		// recupere auteur photo et code plante
		$nsauteur = $_GET['nsauteur'];
		$code = $_GET['code'];
		$posnum = strrpos($nsauteur,"_")+1;
		$idauteur = substr($nsauteur,$posnum);
	}else{
		$code=$_GET['code'];
	}

}else{
	// avec réécriture
	// si ns est defini, recherche le numId de sa fiche puis le mettre dans code 
	if (isset($_GET['ns'])) {
		$ns=$_GET['ns'];
		$req="SELECT numID FROM fiche_$gamme WHERE racinenomfic='$ns'";
		$resultReq = mysql_query($req);
		$infos = mysql_fetch_assoc($resultReq);
		$code=$infos['numID'];
		echo "<!-- $req ns $code -->";
	}else{
		//si nsn est defini, parnre la partie gauche du nom, qui est le ns, recherche le numID...
		// mettre le nsn dans nomauteur
		$mode="detail";
		$nsauteur=$_GET['nsn'];
		
		// on a nsn=artemisia_vulgaris_4 la fonction retournera nsn_sans_num=artemisia_vulgaris et idauteur=4
		$tab=retrouveIDauteur($nsauteur);
		$nsn_sans_num=$tab[0];
		$idauteur=$tab[1];

		$req="SELECT numID FROM fiche_$gamme WHERE racinenomfic='$nsn_sans_num'";
		$resultReq = mysql_query($req);
		$infos = mysql_fetch_assoc($resultReq);
		$code=$infos['numID'];
	}
}

// mise en page d une vignette d une plante
/*$mepUneVignette = "<p><a href='fiche_plante.php?mode=detail&nsauteur={nomFic}&code={code}'>
                   <img src='phototheque/vignette_$gamme/{nomFic}.jpg' border='0'></a></p>";*/
// réécriture
$mepUneVignette = "<p><a href='detail_{nomFic}.html'>
                   <img src='phototheque/vignette_$gamme/{nomFic}.jpg' border='0'></a></p>";
				   
// mise en page commentaires
$mepUnEnsCommentaires = "<p class='commentaire'>
	                     <span class='rubcommentaire'>-- Remarques envoy&eacute;es par des internautes --</span><br>";

// special fleurs : ajoute nom de la famille
$mepFamillePlante = '<tr>
				  <td align="right">Famille</td>
				  <td><span class="nomautre">{newfam}</span></td>
		  		  <td>&nbsp;</td>
				</tr>';

// toutes les rubriques possibles (differentes selon gamme)
$titresRubriques = array(
    "origine"=>"-- Origine du nom -----------------------------------------------------------------------------------------",
	"description"=>"-- Description ----------------------------------------------------------------------------------------------",
	"cycle"=>"-- Cycle -----------------------------------------------------------------------------------------------------",
	"habitat"=>"-- Habitat ---------------------------------------------------------------------------------------------------",
	"medecine"=>"-- M&eacute;decine -----------------------------------------------------------------------------------------------",
	"culinaire"=>"-- Culinaire -----------------------------------------------------------------------------------------------",
	"ecologie"=>"-- Ecologie --------------------------------------------------------------------------------------------------",
	"consommation"=>"-- Consommation -----------------------------------------------------------------------------------------",
	"confusions"=>"-- Confusions ---------------------------------------------------------------------------------------------",
	"liensutiles"=>"-- Liens utiles ------------------------------------------------------------------------------------------"
	);	

// differents traitements selon gamme
switch ($gamme) {
	case "fleurs":
		$listeRub = array("origine", "description", "cycle", "habitat", "medecine", "culinaire", "liensutiles");
		$msg_rappel = '';
		$ref_pub_milieu = "templates/pub_adsense_milieu_fleurs.html";
		$ref_pub_droite = "templates/pub_adsense_droite_fleurs.html";
		break;
	case "champi":
		$listeRub = array("description", "ecologie", "consommation", "confusions", "liensutiles");
		$msg_rappel = '<p class="rappel"> Rappel : ne mangez pas de champignons non examin&eacute;s par un pharmacien. </p>';
		$ref_pub_milieu = "templates/pub_adsense_milieu_champi.html";
		$ref_pub_droite = "templates/pub_adsense_droite_champi.html";
		break;
	case "arbre":
		$listeRub = array("origine", "description", "cycle", "habitat", "medecine", "culinaire", "liensutiles");
		$msg_rappel = '';
		$ref_pub_milieu = "templates/pub_adsense_milieu_arbre.html";
		$ref_pub_droite = "templates/pub_adsense_droite_arbre.html";
		
		break;
}

	
/* MODE FICHE COMPLETE *********************************************************** */
if ($mode=="") {

	$refTemplate = initTemplate("templates/fiche_plante.html");

	// select infos de la plante selectionnee
	$req="SELECT * FROM fiche_$gamme WHERE numId=$code";
	$resultReq = mysql_query($req);
	$nb = mysql_numrows($resultReq);
	if ($nb>0) { 
   		// recupere infos sur la plante
   		$maPlante = mysql_fetch_assoc($resultReq);
		// selectionne tous les commentaires sur la plante dans la base 'fleurs'
		$listCommentaires = INITcommentairesParRub ($code,$mepUnEnsCommentaires,$gamme);
		// verif si les rubriques sont renseignees et rajoute les commentaires eventuels
		$mepRubriques = "";
		$nbligne = 0;
		$nbpubgoogle=0;
		$minnvligne = 2;
		for ($i=0;$i<count($listeRub);$i++) {
			$commentairesRub = verifExistComment ($listeRub[$i],$listCommentaires);
			// rajoute balise a href sur les liens utiles
			//print_r($maPlante);
		/*	if (($maPlante[$listeRub[$i]] != "") && ($listeRub[$i] == "liensutiles")) {
				$liens_propres = str_replace(chr(13), " ", $maPlante[$listeRub[$i]]);
				$liens_propres = str_replace(chr(10), " ", $liens_propres);
				$liste_liens = explode(" ", $liens_propres);
				$liens_utiles = "";
				for ($k=0;$k<count($liste_liens);$k++) {
					$lien_encours = $liste_liens[$k];
					if ($lien_encours != "") {
						$pos_http = strpos($lien_encours, "http");
						if ($pos_http === false) $lien_encours = "http://".$lien_encours;
						if ($liens_utiles != "") $liens_utiles .= "<br />";
						$liens_utiles .= '<a href="'.$lien_encours.'" target="blank">'.$lien_encours.'</a>';
					}
				}
				$maPlante[$listeRub[$i]] = $liens_utiles;
			}*/ 
			//liens utiles se remplit de chiffres à l import d ela base fmp, je le remets à zero 
			//  UPDATE `fiche_fleurs` SET liensutiles = '' WHERE 1  
			$mepRubriques .= INITrubrique ($titresRubriques[$listeRub[$i]],$maPlante[$listeRub[$i]],$commentairesRub,$mepUnEnsCommentaires);
			$nvnbligne = substr_count($mepRubriques ,chr(11))+substr_count($mepRubriques ,"<p>");
			if((($nvnbligne-$nbligne )> $minnvligne) && ($nbpubgoogle == 0)) {
				$nbligne = $nvnbligne;$nbpubgoogle++;
				$aleatoire = rand(0,1);
				$aleatoire = 1;
				$bandeau = file_get_contents($ref_pub_milieu);

				//if ($aleatoire > 0) $bandeau = file_get_contents($ref_pub_milieu);
				//else $bandeau = '<a href="http://lepartiduthe.com" target="blanck"><img src="interface/bandeau_milieu_partiduthe1.jpg" border="0"></a>';
//				else $bandeau = '<a href="http://www.i-love-my-planet.com" target="blanck"><img src="interface/bandeau_milieu_ilovemyplanet.jpg" border="0"></a>';
				$mepRubriques .= "<p>".$bandeau."</p>";
			}
		}
		// memorise la mep des vignette dans la liste des infos de la plante
		$mepRubriques = str_replace(chr(11),"<br>\n",$mepRubriques);
		$maPlante['rubriques'] = $mepRubriques; 
		
		
		// vérifie si on des commentaires pour afficher le lien vers la geoloc
		$req_obs="SELECT * FROM observations WHERE observations.numID=$code AND base='$gamme'";
		$resultReqObs = mysql_query($req_obs);
		$nb_obs = mysql_numrows($resultReqObs);
		if ($nb_obs>0) { 
			$maPlante['lien_vers_carte'] = "<a href='geoloc_consultation.php?ns=".$maPlante['racinenomfic']."' class='nouveau_contenu'><strong>Carte des observations de cette espece</strong></a><br /><br />"; 
		}else{
			$maPlante['lien_vers_carte'] = "";
		}
		
	
	
	}
	
/* MODE DETAIL PHOTO PLANTE *********************************************************** */
} else {

	$refTemplate = initTemplate("templates/detail_plante.html");
	
	// select noms de la plante selectionnee et auteur du detail
	$req="SELECT * FROM auteur t1, fiche_$gamme t2 WHERE id='$idauteur' AND numId=$code";
//	$req="SELECT t1.nom, t2.nc, t2.ns FROM auteur t1, fiche_$gamme t2 WHERE id=$idauteur AND numId=$code";
	$resultReq = mysql_query($req);
	if(!$resultReq)die();
	$nb = mysql_num_rows($resultReq);
	if ($nb>0) {
   		// recupere infos sur la plante
   		$maPlante = mysql_fetch_assoc($resultReq);
   		$maPlante['refdetail'] = $nsauteur.".jpg";
   		$maPlante['code'] = $code;
	}
}

$maPlante['gamme'] = $gamme;
$maPlante['msgdroitsreserves'] = file_get_contents($refDroitsReserves);
$maPlante['scriptsbaspage'] = file_get_contents($refScriptsBasPage);

switch ($gamme) {
	case "fleurs":
  		$famille_plante = renseigneModeleMep($maPlante,$mepFamillePlante);
		$maPlante['libelle_geoloc'] = "J ai observé cette fleur (géolocalisation)";
		break;
	case "champi":
		$famille_plante = "";
		$maPlante['newfam'] = "champignon";
		$maPlante['libelle_geoloc'] = "G&eacute;olocaliser ce champignon";
		break;
	case "arbre":
		$famille_plante = "";
		$maPlante['newfam'] = "arbre";
		$maPlante['libelle_geoloc'] = "G&eacute;olocaliser cet arbre";
		break;
}

// selectionne les vignettes de la plante
$nomPlante = $maPlante["racinenomfic"] ;
$image='SELECT * FROM diapo_'.$gamme.' WHERE nomFic LIKE "'.$nomPlante.'%"';
$listimage=mysql_query($image);
$nbimage = mysql_numrows($listimage);

if ($nbimage>0) {
	// boucle sur le modele de mise en page defini par la variable $mepUneVignette
	$mepImage = "";
	while ($uneImage = mysql_fetch_assoc($listimage)) {
		$uneImage['code'] = $code;
		$mepImage .= renseigneModeleMep($uneImage,$mepUneVignette);
	}
	// memorise la mep des vignette dans la liste des infos de la plante
	$maPlante['vignettes'] = $mepImage;
} else {
	$maPlante['vignettes'] = "";
}

$maPlante['famille_plante'] = $famille_plante;
$maPlante['msg_rappel'] = $msg_rappel;
$maPlante['pub_droite'] = file_get_contents($ref_pub_droite);

$mepPlante = renseigneModeleMep($maPlante,$refTemplate);
affichageTempSimple($mepPlante);



/* ******************************************************************************* */
/* creation d un tableau associatif : pour chaque rubrique, ses commentaires       */
/* ******************************************************************************* */
function INITcommentairesParRub ($IDplante,$mepEnsComment,$base) {
  $req = "select * from commentaire where numId = $IDplante and base = '$base' order by champs,  valeurtemps  desc";
  $result = mysql_query($req);
  $nb = mysql_numrows($result);
  if ($nb>0)
  {  $listCommentaires = array();
     $commentairesRub = "";
     $currentRub = "";
     while ($unCommentaire = mysql_fetch_assoc($result))
	 {  $unCommentaire['champs'] = strtolower(trim($unCommentaire['champs']));
	    if (($currentRub == "") || ($unCommentaire['champs'] <> $currentRub))
	    {  if ($currentRub <> "") { $listCommentaires[$currentRub] = $mepEnsComment.$commentairesRub."</p>"; }
		   $currentRub = $unCommentaire['champs'];
		   if ($unCommentaire['lemail'] <> "") { $unCommentaire['lemail'] = " (".substr($unCommentaire['lemail'],0,7)."...) "; }
			 $unCommentaire['lemail'] = "";//plus de mail
		   $commentairesRub = $unCommentaire['lenom'].$unCommentaire['lemail']." : ".$unCommentaire['letexte']."<br>";
	    } else {
		   if ($unCommentaire['lemail'] <> "") { $unCommentaire['lemail'] = " (".substr($unCommentaire['lemail'],0,7)."...) "; }
			$unCommentaire['lemail'] = "";//plus de mail
	   	   $commentairesRub .= $unCommentaire['lenom'].$unCommentaire['lemail']." : ".$unCommentaire['letexte']."<br>";
        }
	 }
	 $listCommentaires[$currentRub] = $mepEnsComment.$commentairesRub."</p>";
	 return $listCommentaires;
  } else {
     return array();
  }
}


/* ****************************************************************************************** */
/* init le contenu d'une rubrique (description dans la base + commentaires associés)          */
/* ****************************************************************************************** */
function INITrubrique ($titreRub,$contenuRub,$commentaires,$mepCommentaires) {
  $rubrique = "";
  if (($contenuRub <> "") || ($commentaires <> ""))
  {  //if ($contenuRub <> "") { $contenuRub = $contenuRub."<br>"; }
     $rubrique = "<p><span class='rubrique'>".$titreRub."</span><br>".$contenuRub.$commentaires."</p>";
  }
  return $rubrique;
}


/* ****************************************************************************************** */
/* verifie s'il existe des commentaires associés à cette rubrique                             */
/* ****************************************************************************************** */
function verifExistComment ($rubrique,$listComment) {
  if (array_key_exists($rubrique, $listComment)) {
    $commentRub = $listComment[$rubrique];
  } else {
    $commentRub = "";
  }
  return $commentRub;
}


function retrouveIDauteur($nsn){
	$nsn_init=$nsn;
	$continue=true;
	while ($continue==true){
		$trouve=strpos($nsn, "_");
		if ($trouve==false){
			$continue=false;
		}else{
			$nsn=substr($nsn,$trouve+1);
		}
	}
	$num=$nsn;
	$nsn_sans_num=substr($nsn_init,0,strlen($nsn_init)-(strlen($nsn)+1));

	$tab = array();
	array_push ($tab,$nsn_sans_num,$num);
	return $tab;
}
?>
