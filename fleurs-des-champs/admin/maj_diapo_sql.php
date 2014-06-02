<?php
require_once ("../configuration/connexion.inc");

if (!isset($_GET['gamme'])) {
	$msg = "<p>Nom de table &agrave; mettre à jour ind&eacute;termin&eacute;</p>";
} else {
	$gamme = $_GET['gamme'];

	$sql = "DELETE FROM diapo_".$gamme;
	$result = mysql_query($sql, $connexion); 
		
	$d = dir("../phototheque/vignette_".$gamme);
	echo "R&eacute;pertoire utilis&eacute; : ".$d->path."<br>\n";
	while($entry=$d->read()) {
		if(($entry!=".")and($entry!=".."))
			{
			$nom = substr($entry,0,strlen($entry)-4);
			$pos = strrpos($nom,"_");

			$fin= substr($nom,$pos+1,strlen($nom)-$pos);
			if($pos!=0)$debut =substr($nom,0,$pos); else $debut=$nom; 
			
			if($gamme=="arbre")
				{
				//enleve le genre de photo (feuille ecorce etc..)
				
				echo "a couper :$debut ";
				$pos = strrpos($debut,"_");
				$fin= substr($debut,$pos+1,strlen($debut)-$pos);
				if($pos!=0)$debut =substr($debut,0,$pos); 
				}
			
			$sql = "INSERT INTO diapo_".$gamme." (numDiapo, nomFic, nomRacine, auteur) VALUES ('', '$nom','$debut','$fin')";
			$result = mysql_query($sql, $connexion); 

			echo $entry."<br>\n";
			$sql = "select * from fiche_".$gamme." where racinenomfic ='$debut'";
			$result = mysql_query($sql, $connexion); 

			$nb = mysql_numrows($result);
			if($nb==0)echo "<b>$nom debut du nom de fichier = $debut SANS FICHE DIAPO</b><br>";
		}
	}
	$d->close();
}      
?>

