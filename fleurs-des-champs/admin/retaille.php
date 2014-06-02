<?
require_once ("../configuration/connexion.inc");


if (!isset($_GET['gamme'])) {
	$msg = "<p>Nom de table à mettre à jour indéterminé</p>";
	$dir_src_upload = "";
} else {
	$gamme = $_GET['gamme'];
	$dir_src_upload = "../phototheque/source_".$gamme."/";
}



function fct_resize_image ($src_image,$new_nom_image,$taille_max,$dir_save,$watermark) {
	global  $dir_src_upload ;
  //$img_watermark, 
  $epaisseur_contour = 1;
  $contour_r= $contour_v= $contour_b=0;
  // src fichier upload
  $file_upload = $dir_src_upload.$src_image;
  // taille image upload
  $srctaille = getimagesize($file_upload); 
  $srcheight = $srctaille[1]; 
  $srcwidth = $srctaille[0]; 
  // verification taille de l image a copier
  $ratio_img = $srcwidth/$srcheight;
  if ($ratio_img < 1) {
    // image verticale
    $newsrcwidth = $taille_max*$ratio_img;
	$newsrcheight = $taille_max;
  } else {
    // image horizontale ou carree
	$newsrcheight = $taille_max*(1/$ratio_img);
    $newsrcwidth = $taille_max;
  }     
  // enregistrement de l image redimensionnee
  $file_save = $dir_save.$new_nom_image;
  $temp_img = imagecreatefromjpeg($file_upload); 
  if ($temp_img != "") {
    $dst_img = imagecreatetruecolor($newsrcwidth,$newsrcheight);
    // dessine contour de la couleur definie plus haut
    $couleur = imagecolorallocate ($dst_img, $contour_r, $contour_v, $contour_b);
	imagefill($dst_img,0,0,$couleur); 
	// redimensionne l image et copie dans image avec couleur contour
    $largeur_sans_contour = $newsrcwidth - ($epaisseur_contour * 2);
    $hauteur_sans_contour = $newsrcheight - ($epaisseur_contour * 2);
   // imagecopyresized($dst_img,$temp_img,$epaisseur_contour,$epaisseur_contour,0,0,$largeur_sans_contour,$hauteur_sans_contour,$srcwidth,$srcheight);
    imagecopyresampled($dst_img, $temp_img, $epaisseur_contour,$epaisseur_contour, 0, 0, $largeur_sans_contour, $hauteur_sans_contour, $srcwidth, $srcheight);
	if ($watermark) {
	  watermark($dst_img, $img_watermark, $newsrcwidth, $newsrcheight, $file_save);
	} else {
      imagejpeg($dst_img,$file_save,100);
      //imagepng($dst_img,$file_save.".png");
	}
    imagedestroy($temp_img);
    imagedestroy($dst_img);
	// verif creation img
    if (file_exists($file_save)) {
      return 1;
    } else {
	  return 0;
	}
  } else {
    return 0;
  }
}


if ($dir_src_upload != ""){

	if ($handle = opendir($dir_src_upload)) {	
		$img_a_traiter = 0;
		$img_traitement_reussi = 0;
	  
		while (false != ($fichier_img = readdir($handle))) {
		
			if (($fichier_img != ".") && ($fichier_img != "..") && ($fichier_img != ".svn")) {
		
				$img_a_traiter = $img_a_traiter + 1;
				// nom qui sera donne a l'image apres traitements (sans extension)
				$nouveau_nom = strtolower($fichier_img);
				// verifie fichier upload >= $taille_max_zoom
				$taille_img = getimagesize($dir_src_upload.$fichier_img); 
				if ($taille_img !== false) {
					$img_height = $taille_img[1]; 
					$img_width = $taille_img[0]; 
					//$taille_max_sans_contour = $taille_max_zoom - (2 * $epaisseur_contour);
					/*   if (($img_height < $taille_max_sans_contour) && ($img_width < $taille_max_sans_contour)) {
					// envoie email pour demander de renvoyer un fichier mais genere quand meme les 3 formats dimage
				
					}*/
					// creation zoom image avec watermark
					$resize_reussi = fct_resize_image ($fichier_img,$nouveau_nom,500,"../phototheque/detail_".$gamme."/",0);
					$resize_reussi = fct_resize_image ($fichier_img,$nouveau_nom,150,"../phototheque/vignette_".$gamme."/",0);
				}
			} else {  
				$msg_erreur = "impossible de connaitre la taille du fichier ".$dir_src_upload.$fichier_img;
			}
		}
		closedir($handle);
	}
}

?>
