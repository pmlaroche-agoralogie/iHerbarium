<?php

require_once ("../configuration/connexion.inc");


if (!isset($_GET['gamme'])) {
	$msg = "<p>Nom de table � mettre � jour ind�termin�</p>";
} else {
	$gamme = $_GET['gamme'];
	// remet toutes les fiches invisibles avant maj
	$sql = "UPDATE fiche_$gamme SET visible_index = 0";
    $result = mysql_query($sql, $connexion); 
	// rend visible des fiches pour lesquelles nous avons des photos
	$sql = "UPDATE fiche_$gamme, diapo_$gamme SET fiche_$gamme.visible_index = 1 WHERE fiche_$gamme.racinenomfic = diapo_$gamme.nomRacine";
    $result = mysql_query($sql, $connexion); //or die('Erreur dans la requ�te DONNEE : ' . mysql_error());
  	if ($result) {
    	$sql = "SELECT count(numId) FROM fiche_$gamme WHERE visible_index = 1";
    	$result = mysql_query($sql, $connexion); 
    	$nb = mysql_fetch_row($result);
    	$msg = "<p>nb de fiches <strong>$gamme</strong> � afficher dans l'index : <strong>".$nb[0]."</strong></p>";
	} else {
    	$msg = "<p><strong>Probl�me mise � jour fiches visibles dans l'index.</strong></p>";
	}
}

echo ("<h3>Mise � jour des index</h3>");
echo($msg);
echo("<p><a href='index.php'>Retour � l'accueil</a></p>");


?>