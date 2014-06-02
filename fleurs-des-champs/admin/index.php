<?php

echo ("<h3>Administration des sites www.fleurs-des-champs.com, www.champignons-de-france.com et www.arbres-de-france.com</h3>");

echo("<h4>Mise &agrave; jour de la table 'diapo_...' &agrave; partir des photos pr&eacute;sentes dans la photot&egrave;que</h4>");
echo("<p><a href='maj_diapo_sql.php?gamme=fleurs'>Mettre à jour la table 'diapo' de \"Fleurs des Champs\"</a><br />");
echo("<a href='maj_diapo_sql.php?gamme=champi'>Mettre à jour la table 'diapo' de \"Champignons de France\"</a></p>");
echo("<a href='maj_diapo_sql.php?gamme=arbre'>Mettre à jour la table 'diapo' de \"Arbre de France\"</a></p>");

echo("<h4>Mise a jour du champ 'visible_index' dans la table 'fiche_...' selon si la plante possede au moins une diapo dans la table 'diapo_...'</h4>");
echo("<p><a href='maj_index.php?gamme=fleurs'>Mettre à jour l'index \"Fleurs des Champs\"</a><br />");
echo("<a href='maj_index.php?gamme=champi'>Mettre à jour l'index \"Champignons de France\"</a></p>");
echo("<a href='maj_index.php?gamme=arbre'>Mettre à jour l'index \"Arbres de France\"</a></p>");


echo("<h4>Calcul des différents formats d'images à partir d'une photo source</h4>");
echo("<p><a href='retaille.php?gamme=fleurs'>Calcul des images de \"Fleurs des Champs\"</a><br />");
echo("<a href='retaille.php?gamme=champi'>Calcul des images de \"Champignons de France\"</a></p>");
echo("<a href='retaille.php?gamme=arbre'>Calcul des images de \"arbres de France\"</a></p>");

echo("<h4>Rappel de l'url pour visualiser toutes les observations d'un internaute</h4>");
echo("<p><a href='rappel_url_geoloc.php'>Calcul de l'url et visualisation du fichier XML des observations</a><br />");

echo("<h4>Envoi email rappel codes de connection a tous les internautes ayant fait une observation</h4>");
echo("<p><a href='envoi_mailing_codes_geoloc.php'>Cliquer ici pour accéder à l'interface d'envoi des emails (ce lien ne lance pas le mailing)</a><br />");

?>