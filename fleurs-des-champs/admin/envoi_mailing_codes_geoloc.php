<?php

require_once ("../configuration/connexion.inc");


if (isset($_GET['mode'])) $mode = $_GET['mode'];
else $mode = "init";

$message_ecran = "";

$message_envoye = "Bonjour,
Nous vous écrivons aujourd'hui car vous avez déjà déposé au moins une observation sur notre site www.fleurs-des-champs.com.
La fonction géolocalisation est en place depuis un an, et nous avons corrigé depuis plusieurs bugs, liés aux navigateurs web ou à des caractéristiques de GoogleMap. Nous pensons que le système est maintenant beaucoup plus fiable.
Nous vous renvoyons un lien permettant de voir toutes vos observations :
###lienperso###
Si vous aviez le moindre souci avec ce lien, n'hésitez pas à nous en faire part (en nous donnant si possible le navigateur web que vous utilisez).

Nous envisageons de vous donner la possibilité de rajouter vos photos, qui seront liées à une observation, merci de nous dire si vous seriez intéressé par cette fonctionnalité (un mail succinct de réponse suffit).

Cordialement,
L'équipe de Fleurs des champs";


if ($mode != "init") {
	$sql = "SELECT DISTINCT (qui_email) FROM observations WHERE base = 'fleurs' ORDER BY qui_email";
//	$sql = "SELECT DISTINCT (qui_email) FROM observations WHERE base = 'fleurs' AND qui_email = 'plaroche@ex-algebra.com' ORDER BY qui_email";
	$result = mysql_query($sql, $connexion); 
	if (!$result) {
		$message_ecran = "<p><strong>Erreur technique :</strong> la requete de selection des adresses emails dans la table 'observations' a echouee.</p>";
	} else {
		$nbemails = mysql_num_rows($result); 
		$liste_emails_envoyes = "<strong>Nb total d'emails a envoyer : ".$nbemails."</strong><br>";
		while ($uninternaute = mysql_fetch_assoc($result)) {
			$lienperso = "http://www.fleurs-des-champs.com/geoloc_consultation.php";
			$lienperso .= "?idt=".$uninternaute['qui_email']."&pwd=".MD5(substr($uninternaute['qui_email'],5,10));
			$message_perso = str_replace("###lienperso###",$lienperso,$message_envoye);
			$sendto = $uninternaute['qui_email'];
			$liste_emails_envoyes .= "<br>".$sendto."<br>lienperso : ";
			$liste_emails_envoyes .= '<a href="'.$lienperso.'" target="_blank">'.$lienperso."</a><br>";
			if ($mode == "envoyer") {
				$email_expediteur = "Fleurs des champs <contact@fleurs-des-champs.com>";
				$email_cc = "support@ex-algebra.com";
				$headers = "From: ".$email_expediteur."\n";
  				$headers .= "Reply-To: ".$email_expediteur."\n";
  				$headers .= "Bcc: ".$email_cc."\n";
				//$headers .= "MIME-version: 1.0\n";
				//$headers .= "Content-type: text/plain; charset= utf8\n";
				$objet = "L'herbier virtuel de Fleurs des champs.com";
				if ($_SERVER['HTTP_HOST'] != "localhost") $reussi = mail($sendto, $objet, $message_perso, $headers); //"cas envoi reel, desactive pour le moment";
				else $reussi = "pas d'envoi de mail en local";
				$liste_emails_envoyes .= " => ".$reussi."<br>";
			}
		}
	}
}


$contenu = "<h3>Envoi email rappel codes de connection a tous les internautes ayant fait une observation</h3>";
$contenu .= "<p>MODE TESTER : ".'<a href="envoi_mailing_codes_geoloc.php?mode=tester">Cliquer ici pour afficher dans cette page la liste des mails à envoyer</a>'."</p>";
$contenu .= "<p>MODE ENVOYER : ".'<a href="envoi_mailing_codes_geoloc.php?mode=envoyer">Cliquer ici pour envoyer le mail aux internautes</a>'."</p>";
$contenu .= "<p><strong>--- RAPPEL DU MAIL ENVOYE ---------------------------------------------------------------</strong></p>";
$contenu .= "<p>".$message_envoye."</p>";
$contenu .= "<p><strong>-----------------------------------------------------------------------------------------</strong></p>";
$contenu .= "<p><br><br></p>";
if ($message_ecran != "") $contenu .= $message_ecran;
if ($liste_emails_envoyes != "") {
	$contenu .= "<p><strong>>> MODE ".strtoupper($mode);
	if ($mode == "envoyer") $contenu .= " (les emails ont ete envoyes aux internautes)</strong></p>";
	else $contenu .= " (aucun mail envoye)</strong></p>";
	$contenu .= "<p>".$liste_emails_envoyes."</p>";
	$contenu .= "<p><br><br><strong> >> FIN DU MAILING</strong></p>";
}

echo($contenu);


?>