<?php

require_once ("configuration/connexion.inc");
require_once ("scripts/gestiontemplates.php");

$refDroitsReserves = "templates/texte_droits_reserves.html";
$refScriptsBasPage = "templates/scripts_bas_page_$gamme.html";

$email_from = "Nature en France <contact@fleurs-des-champs.com>";
$email_en_copie = "support@ex-algebra.com";

if (isset($_GET['ns'])) {
	$ns=$_GET['ns'];
	//$ns = "acanthus_mollis";
	//$ns = "equisetum_sylvaticum";
	$req="SELECT * FROM fiche_$gamme WHERE racinenomfic='$ns'";
	$resultReq = mysql_query($req);
	$maPlante = mysql_fetch_assoc($resultReq);
	$code = $maPlante['numId'];
	if (!isset($_GET['etat'])) {  // mode saisie geoloc d'une plante
		$refTemplate = initTemplate("templates/geoloc_saisie.html");
		$maPlante['aujourdhui'] = date("d/m/Y");
	} else {  // mode enregistrement geoloc d'une plante
		$refTemplate = initTemplate("templates/geoloc_validation.html");
		$aujourdhui = date("Y-m-d");
		$latitude = $_POST['vlatitude'];
		$latitude_imprecise = fct_coordonnees_imprecises($latitude);
		$longitude = $_POST['vlongitude'];
		$longitude_imprecise = fct_coordonnees_imprecises($longitude);
		$certitude = $_POST['vcertitude'];
		$surface = $_POST['vsurface'];
		$visible = $_POST['vvisible'];
		$masquer_precision = $_POST['vprecision'];
		$nomprenom = trim(addslashes($_POST['vnomprenom']));
		$email = trim($_POST['vemail']);
		$datesaisie = trim($_POST['vdate']);
		$dateobservation = fct_date_format_bdd($datesaisie);
		$commentairepublic = trim(addslashes($_POST['vcommentairepublic']));
		$commentaireprive = trim(addslashes($_POST['vcommentaireprive']));
		$recap_saisie = "<p><strong>Latitude : </strong>".$latitude;
		$recap_saisie .= "<br><strong>Longitude : </strong>".$longitude;
		$recap_saisie .= "<br><strong>Date de l'observation : </strong>".$datesaisie."</p>";
		
		$req = "INSERT INTO observations (numId,base,date_creation,date_observation,qui,qui_email,masquer_precision,lat_reel,lng_reel,lat_flou,lng_flou,visible,certitude_identification,surface_poste,commentaire_public,commentaire_prive) VALUES ($code,'$gamme','$aujourdhui','$dateobservation','$nomprenom','$email',$masquer_precision,$latitude,$longitude,$latitude_imprecise,$longitude_imprecise,$visible,$certitude,$surface,'$commentairepublic','$commentaireprive')";
		$maPlante['msgvalidation'] .= "<br><br>".$req;
		$resu = mysql_query($req);
     	// verif nouvel enregistrement dans la base
     	$nbAjout = mysql_affected_rows();
     	if ($nbAjout>0) { 
			// envoi email à l'internaute pour visualisation de son observation
			$lienperso = "http://".$refsite."/geoloc_consultation.php?idt=".$email."&pwd=".MD5(substr($email,5,10));
			$lienperso_uneobservation = $lienperso."&ns=".$ns;
  			$headers = "From: ".$email_from."\n";
  			$headers .= "Reply-To: ".$email_from."\n";
  			$headers .= "Bcc: ".$email_en_copie."\n";
			$objet = "Confirmation géolocalisation de votre observation";
			$message = "Bonjour"."\r\n";
			$message .= "Nous avons bien enregistré votre observation de '".$maPlante['ns']."'."."\n";
			$message .= "Latitude : ".$latitude."\n";
			$message .= "Longitude : ".$longitude."\n";
			$message .= "Date de l'observation : ".$datesaisie."\r\n";
			$message .= "Pour visualiser votre observation, cliquez sur le lien ci-dessous :"."\n";
			$message .= $lienperso."&ns=".$ns."\n";
	    	$message .= "Pour visualiser toutes les observations que vous avez enregistrées, cliquez sur le lien ci-dessous :"."\n";
	    	$message .= $lienperso."\r\n";
	    	$message .= "Cordialement,"."\n";
	    	$message .= "L'équipe de Nature en France";
  			$envoi = mail($email, $objet, $message, $headers);
			// affichage msg ecran
			$maPlante['msgvalidation'] = "<p><strong>Votre observation a bien été enregistrée.</strong></p>";
			if (envoi) $maPlante['msgvalidation'] .= "<p>Un email vient de vous être envoyé avec les liens ci-dessous vous permettant de visualiser vos observations.</p>";
			else $maPlante['msgvalidation'] .= "<p>Une erreur technique est survenue, nous n'avons pas pu vous envoyer par email les liens ci-dessous vous permettant de visualiser vos observations ; veuillez les notez précieusement afin de pouvoir consulter vos observations.</p>";
			$maPlante['msgvalidation'] .= $recap_saisie;
			$maPlante['msgvalidation'] .= "<p><br />>> Cliquez ici pour <a href='".$lienperso_uneobservation."' target='_blank'>visualiser votre observation</a><br />";
			$maPlante['msgvalidation'] .= ">> Cliquez ici pour <a href='".$lienperso."' target='_blank'>visualiser toutes les observations que vous avez enregistrées</a></p>";
			$maPlante['msgvalidation'] .= "<p><br /><strong>Merci de votre contribution à l'enrichissement de notre base de connaissances.</strong></p>";
		} else {
			$maPlante['msgvalidation'] .= "<p><strong>Problème lors de l'enregistrement de votre observation.</strong></p>";
		} 

	}
} else { // pas trouve $_GET['ns']
	$refTemplate = initTemplate("templates/geoloc_validation.html");
	$maPlante['msgvalidation'] .= "<p><strong>Problème lors de l'identification de la plante à géolocaliser.</strong></p>";
}

// mise en page d une vignette d une plante
$mepUneVignette = "<p><a href='detail_{nomFic}.html'>
                   <img src='phototheque/vignette_$gamme/{nomFic}.jpg' border='0'></a></p>";
				   
// special fleurs : ajoute nom de la famille
$mepFamillePlante = '<tr>
				  <td align="right">Famille</td>
				  <td><span class="nomautre">{newfam}</span></td>
		  		  <td>&nbsp;</td>
				</tr>';

	
$maPlante['gamme'] = $gamme;
$maPlante['apigeoloc'] = $apigeoloc;
$maPlante['msgdroitsreserves'] = file_get_contents($refDroitsReserves);
$maPlante['scriptsbaspage'] = file_get_contents($refScriptsBasPage);

// infos differentes selon fleurs ou champi
switch ($gamme) {
	case "fleurs":
		$msg_rappel = '';
		$ref_pub_milieu = "templates/pub_adsense_milieu_fleurs.html";
		$ref_pub_droite = "templates/pub_adsense_droite_fleurs.html";
  		$famille_plante = renseigneModeleMep($maPlante,$mepFamillePlante);
		break;
	case "champi":
		$msg_rappel = '<p class="rappel"> Rappel : ne mangez pas de champignons non examin&eacute;s par un pharmacien. </p>';
		$ref_pub_milieu = "templates/pub_adsense_milieu_champi.html";
		$ref_pub_droite = "templates/pub_adsense_droite_champi.html";
		$famille_plante = "";
		$maPlante['newfam'] = "champignon";
		break;
	case "arbre":
		$msg_rappel = '';
		$ref_pub_milieu = "templates/pub_adsense_milieu_arbre.html";
		$ref_pub_droite = "templates/pub_adsense_droite_arbre.html";
		$famille_plante = "";
		$maPlante['newfam'] = "arbre";
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



/* ************************************************************************************************ */
/* FONCTIONS                                                                                        */
/* ************************************************************************************************ */


/* ajoute un coefficient d'imprecision aux coordonnees de geoloc                              */
/* ------------------------------------------------------------------------------------------ */
function fct_coordonnees_imprecises ($coordonnee) {
	$coordonnee_imprecise = ((rand(1,60) - 30) * 0.001) + $coordonnee;
	return $coordonnee_imprecise;
}


/* renvoie la date saisie au format attendu dans la BDD (aaaammjj)                            */
/* ------------------------------------------------------------------------------------------ */
function fct_date_format_bdd ($date) {
	$date_liste = explode("/", $date);
	// jour
	if ($date_liste[0] < 10) $date_liste[0] = "0".($date_liste[0]*1); 
	// mois
	if ($date_liste[1] < 10) $date_liste[1] = "0".($date_liste[1]*1); 
	// date au format aaaammjj
	$newdate = $date_liste[2]."-".$date_liste[1]."-".$date_liste[0];
	return $newdate;
}


?>
