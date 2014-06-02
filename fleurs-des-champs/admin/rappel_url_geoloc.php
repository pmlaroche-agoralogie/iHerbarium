<?php

$lienperso = "";
if (isset($_POST['vemail'])) {
	$email = $_POST['vemail'];
	$lienperso = "http://www.fleurs-des-champs.com/geoloc_consultation.php?idt=".$email."&pwd=".MD5(substr($email,5,10));
	$lienxml = "http://www.fleurs-des-champs.com/geoloc_xml.php?idt=".$email."&pwd=".MD5(substr($email,5,10));
}

$formulaire = '<form id="form1" name="form1" method="post" action="">';
$formulaire .= 'Email : <input type="text" name="vemail" id="vemail" /> ';
$formulaire .= '<input type="submit" name="button" id="button" value="OK" />';
$formulaire .= '</form>';

echo ("<h3>Rappel de l'url pour visualiser toutes les observation d'un internaute</h3>");
echo($formulaire);
if ($lienperso != "") {
	echo("<p>Pour visualiser toutes les observations de '".$email."', cliquez sur le lien ci-dessous :<br />");
	echo("<a href='".$lienperso."' target='_blank'>".$lienperso."</a></p>");
	echo("<p>Pour visualiser le fichier XML des geolocalisations de '".$email."', cliquez sur le lien ci-dessous (a faire sur IE) :<br />");
	echo("<a href='".$lienxml."' target='_blank'>".$lienxml."</a></p>");
}

?>