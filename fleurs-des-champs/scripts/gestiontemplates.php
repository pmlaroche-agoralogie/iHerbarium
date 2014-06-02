<?php

/* ********************************************************************** */
/* Definition des fonctions pour la gestion d une template                */
/* ********************************************************************** */

// ouverture et memorisation du fichier template
function initTemplate ($vpathtemplate) {
  $modele = fopen ($vpathtemplate, 'rb');
  $refTemplate = fread ($modele, filesize($vpathtemplate));
  fclose ($modele);
  return $refTemplate;
}

// attribution des valeurs des variables dans un modele de mise en page
function renseigneModeleMep ($vInfos,$vMep) {
  foreach($vInfos as $key=>$value) {
    $result = ereg_replace ("{".$key."}", $value, $vMep);
    $vMep = $result;	
  }
  return $vMep;
}

// affichage de la template simple (page html avec variables)
function affichageTempSimple ($vRefTemplate) {
  echo $vRefTemplate; 
}
?>