<?php

require_once ("configuration/connexion.inc");
require_once ("scripts/gestiontemplates.php");

$var['gamme'] = $gamme;

$rubFleurs = array("origine", "description", "cycle", "habitat", "medecine", "culinaire", "liensutiles");
$rubChampi = array("description", "ecologie", "consommation", "confusions", "liensutiles");
$rubArbres = array("origine", "description", "cycle", "habitat", "medecine", "culinaire", "liensutiles");

if (!isset($_GET['mode'])) $var['mode'] = "init";
else $var['mode'] = $_GET['mode'];

if (isset($_GET['code']) && isset($_GET['ns'])) {
	$var['code'] = $_GET['code'];
	$var['ns'] = $_GET['ns'];
	// si 1ere visite, on est en mode saisie; sinon, mode enregistrement
	if (!isset($_GET['mode'])) {  
		$refTemplate = initTemplate("templates/commentaires_saisie.html");
		$var['mode'] = "save";
		// liste rubriques selon gamme
		switch ($gamme) {
	  		case "fleurs":
				$listeRub = $rubFleurs;
				break;
	  		case "champi":
				$listeRub = $rubChampi;
				break;
	  		case "arbre":
				$listeRub = $rubArbres;
				break;
		}
		$var['rubriques'] = "";
		for ($i=0;$i<count($listeRub);$i++) {
			$var['rubriques'] .= '<option value="'.$listeRub[$i].'">'.$listeRub[$i].'</option>';
		}
	} else {
     	$refTemplate = initTemplate("templates/commentaires_validation.html");
		$nom =  addslashes($_POST['nom']);
		$email = $_POST['email'];
		$sujet = addslashes($_POST['sujet']);
		$commentaire = addslashes($_POST['commentaire']);
     	// enregistrement des commentaires dans la base
     	$aujourdhui = time();
     	//j'enleve le champs date dans lequel on mettait la date d'enregistrement
     	$req = "INSERT INTO commentaire (numId,base,champs,letexte,lenom,lemail) VALUES (".$var['code'].", '$gamme','$sujet','$commentaire','$nom','$email')";
		$resu = mysql_query($req);
     	// verif nouvel enregistrement dans la base
     	$nbAjout = mysql_affected_rows();
     	if ($nbAjout>0) {  
			$var['msg'] = "Votre commentaire a bien été enregistré.";
			
			// envoi du mail
			$req_select = "select nc from fiche_".$gamme." WHERE numId=".$var['code'];
			$resu_select = mysql_query($req_select);
			$rangee = mysql_fetch_assoc($resu_select);
			$le_nc = $rangee['nc'];
			$corpsmail = "Gamme : ".$gamme."\n";
			$corpsmail .= "Sujet : ".$sujet."\n";
			$corpsmail .= "NumId de la plante : ".$var['code']."\n";
	    	$corpsmail .= "Nom commun : ".$le_nc."\n";
			$corpsmail .= "Nom scientifique : ".$var['ns']."\n";
			$corpsmail .= "Commentaire : "."\n";
			$corpsmail .= stripslashes($commentaire);
	    	$envoiok = mail("georgeslaroche@free.fr", "Nouveau commentaire fleurs", $corpsmail);

     	} else { 
			$var['msg'] = "Problème lors de l'enregistrement de votre commentaire.";
		}
	}
	$mepCommentaires = renseigneModeleMep($var,$refTemplate);
	affichageTempSimple($mepCommentaires);
}

?>