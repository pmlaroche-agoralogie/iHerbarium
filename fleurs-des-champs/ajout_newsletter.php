<?php
include("phototheque/scripts/connexion.php");
	$lemail=$_POST["lemail"];

	 if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
     $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) {
     $realip = $_SERVER["HTTP_CLIENT_IP"];
    } else {
     $realip = $_SERVER["REMOTE_ADDR"];
    }
	
     $req = "insert into newsletter (email,code_ip,code_geoloc) values ('$lemail', '$realip','NA')";
     $resu = mysql_query($req);

?>
<HTML>
<HEAD>
<TITLE>Les fleurs sauvages les plus courantes.</TITLE>
<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="ROBOTS" content="All">
<meta http-equiv="Content-Language" content="fr">
<meta name="Keywords" content="fleur, plante, photo,dessins, identification,fiche, information, utilisation, medicine,culinaire">
<meta name="Description" content="Fiches descriptives, dessins et photos sur 400 plantes sauvages couramment rencontrees. Un programme vous aide a identifier une plante trouvee.">
<link href="styles_accueil.css" rel="stylesheet" type="text/css" />

</HEAD>
<BODY>
<div id="centrer">
	<div id="site">
		<div id="header">
			<div id="header_gauche"></div>
			<div id="header_droit"></div>
		</div>
		<div id="main">
		  <div id="infos">
		    Votre email a bien été enregistré.<br />
	      Vous recevrez la prochaine Newsletter de Fleurs des champs.<br /><br />
		  <form name="form1" method="post" action="http://www.fleurs-des-champs.com/index.htm">
		    <input type="submit" name="Submit" value="Retour au site">
	      </form>
			</div>
		</div>
	</div>
</div>
</BODY>
</HTML>