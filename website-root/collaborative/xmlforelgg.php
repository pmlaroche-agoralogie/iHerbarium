<?php
namespace iHerbarium;

$cookieName = 'idCollaborative';

if ( isset($_COOKIE[$cookieName] )) {

  $beenHereBefore = true;
  $internautId = $_COOKIE[$cookieName];

} else { 

  $beenHereBefore = false;
  $internautId = time(). "-" .rand(1000, 9900); // value

  $domains = array(
		   ".iherbarium.fr",
		   ".iherbarium.org"
		   );

  foreach($domains as $domain) {
    setcookie(
	      $cookieName,                  // cookie name
	      $internautId,                 // cookie value
	      time() + (60 * 60 * 24 * 30), // expires: in 30 days
	      "/",                          // path: available in entire domain
	      $domain);                     // domain
  }
  
}


/*echo '<head>';
echo '<link rel="stylesheet" type="text/css" href="questionstyle.css" media="all" />';
echo '</head>';
*/

// Require! START

$balPath = "../boiteauxlettres/";
$require_once = 
  function($x) use ($balPath) { require_once($balPath . $x); };

$require_once("myPhpLib.php");

$require_once("debug.php");
$require_once("config.php");
$require_once("logger.php");

$require_once("transferableModel.php");
$require_once("question.php");
$require_once("dbConnection.php");

$require_once("persistentObject.php");

Debug::init("myTest", false);

// Require! END


echo '<question>';

// Action
$action = 'NextTask';

if( isset($_POST['questionType']) )
  $action = 'HandleAnswer';

if( isset($_GET['action']) )
  $action = $_GET['action'];


/*
// Little debug
echo "<p>beenHereBefore: " . ($beenHereBefore ? "yup!" : "naah...") . "</p>";
echo "<p>internautId: " . $internautId . "</p>";
echo "<p>action: " . $action . "</p>";
*/

// Database...
$local = LocalTypoherbariumDB::get();

// Protocol
$p = DeterminationProtocol::getProtocol("Standard");

switch($action) {

case 'HandleAnswer':

  $ah = new AnswerHandler();
  $answer = $ah->receiveAnswer();
  $local->saveAnswer($answer);
  //echo "<p>" . $answer . "</p>";

  $p->answerReceived($answer);

case 'NextTask':
    
  $task = $local->loadNextTask();

  if($task == NULL) {
    //echo "<p>No Tasks left!</p>";
    echo file_get_contents("notask_fr.html");
    break;
  }

  // Skin
  $s = new TypoherbariumSkin('fr');
  
  // View
  $qv = new QuestionView();
    
  //echo "<p>" . $task . "</p>";  

  // Extract ROI.
  $roi = $task->context;

  $taskCcq = $task->makeClosedChoiceQuestion($s);
  
  // Ask Log.
  $ask = new TypoherbariumAskLog();
  
  $ask
    ->setQuestionType($taskCcq->getType())
    ->setQuestionId($taskCcq->getId())
    ->setContext($roi->id)
    ->setLang($s->lang)
    ->setInternautIp($_SERVER['REMOTE_ADDR'])
    ->setInternautId($internautId);
  
  $ask = $local->logQuestionAsked($ask);
  //echo "<p>" . $ask . "</p>";


  // ClosedChoiceQuestion.
  $taskCcq->setAskLog($ask);
  

  // View.
  $content = '<div>' . $qv->viewQuestion($taskCcq, $roi, $s) . '</div>';
  echo $content;

  break;

case 'GenerateQuestions':

  // Add Observation
  if( isset($_GET['obsId']) )
    $obsId = $_GET['obsId'];
  else {
    echo "<p>No obsId!</p>";
    break;
  }

  $obs = $local->loadObservation($obsId);

  $p->addedObservation($obs);

  echo "<p>Question Tasks for Observation $obsId have been (re)generated!</p>";

  /*
  echo "<p>" . "Added Tasks:" . "</p>";

  array_iter(
	     function(TypoherbariumTask $task) {
	       echo ("<p>" . $task . "</p>");
	     },
	     $tasks);
  */
  
  break;

case 'GenerateComparisons':

  // Observation
  if( isset($_GET['obsId']) )
    $obsId = $_GET['obsId'];
  else {
    echo "<p>No obsId!</p>";
    break;
  }

  $obs = $local->loadObservation($obsId);
  $tasks = $p->generateComparisonsForObservation($obs, 5);

  // Add Comparison Tasks
  array_iter(
	     array($local, "addTask"),
	     $tasks
	     );

  break;

case 'CleanAnswers':

  // Observation
  if( isset($_GET['obsId']) )
    $obsId = $_GET['obsId'];
  else {
    echo "<p>No obsId!</p>";
    break;
  }
  
  $obs = $local->loadObservation($obsId);

  $extractId = function($obj) { return $obj->id; };

  array_iter(
	     function($roi) use ($local, $extractId) {
	       
	       $local->deleteTasksForROI($roi);

	       array_iter( 
			  array($local, "deleteAnswer"),
			  array_map($extractId, $roi->answers)
			   );
	       
	       array_iter( 
			  array($local, "deleteAnswersPattern"),
			  array_map($extractId, $roi->answersPatterns)
			   );
	       
	     },
	     $obs->getROIs()
	     );

  echo "<p>Deleted all Tasks, Answers and AnswersPatterns for Observation $obsId!</p>";
  
  break;
  
}

echo "</body>";

die();




// ============= OLD SCRIPT ===============


include("../common_functions.php");
$debug_reponse=0;

if (isset($_COOKIE['idcollaboratif'])) {
	$gateau = $_COOKIE['idcollaboratif'];
	}
	else	{ 
	$gateau = time()."-".rand(1000,9900);
	setcookie('idcollaboratif',$gateau,time()+888777,"/",'.iherbarium.org');//,'.iherbarium.org'
	}

// affiche et traite les questions "simples" posées par iframe aux internautes
$legende99 = array('fr'=>"Question non adapt&eacute;e &agrave; la photo", 'en' => "Not a meaningful question for this picture", 'de' => "i don't know", 'pt' => "i don't know");
$legendeabus = array('fr'=>"Avertir le mod&eacute;rateur", 'en' => "Report Abuse", 'de' => "Report Abuse", 'pt' => "Report Abuse");


$modeledessins = '<div class="question-image">
			<div onClick="document.getElementById('."'reponse_cachee'".').value=valeurreponse;document.getElementById('."'questionform'".').submit();"> <img class="questionpic" alt="altdescription"  src="srcimagette"></div>
			<a href="licenceimagette" target="_blank"><img class="questionclip" src="Magnify-clip-tiny_bottom-right-white-gradient.png"></a></div>';

$modeletexte = '<div class="question-texte" style="background:yellow">
			<div onClick="document.getElementById('."'reponse_cachee'".').value=valeurreponse;document.getElementById('."'questionform'".').submit();">laquestion</div>
			</div>';

			
/*Cette fonction permet de poser les questions pour une zone d'intérêt pour lesquelles il n'existe pas encore de lignes de synthèse dans la 
 * table iherba_roi_answers_pattern.
 * Lorsqu'on répond à une question impliquant des sous questions, un certain nombre de fois, 
 * en fonction de la réponse la plus apportée à cette question
 * on posera désormais la sous question correspondante*/
function choisir_question($langue,$sourceanswer){
	global $gateau;
	$choixpriorite = " iherba_observations.priorite = ( SELECT max( priorite ) FROM iherba_observations WHERE iherba_observations.determination_achevee=0) ";
	if(isset($_GET['numobs']))
		$choixpriorite = " iherba_observations.idobs = ".$_GET['numobs']." ";
	bd_connect();
	$sql_questions_sans_pattern="SELECT iherba_roi.id as id_roi,iherba_question.id_question ,iherba_photos.nom_photo_final
	FROM iherba_observations, iherba_photos, iherba_roi, iherba_roi_tag, iherba_question
	WHERE iherba_observations.determination_achevee=0
	AND $choixpriorite
	AND iherba_observations.idobs = iherba_photos.id_obs
	AND iherba_roi.id_photo = iherba_photos.idphotos
	AND iherba_roi.id = iherba_roi_tag.id_roi
	AND iherba_question.disabled = 0
	AND iherba_question.id_tag_necessaire = iherba_roi_tag.id_tag
	and iherba_question.id_langue='$langue' and iherba_question.id_question_necessaire=0
	and not exists (select * from iherba_roi_answers_pattern
	where iherba_roi_answers_pattern.id_roi=iherba_roi.id
	and iherba_roi_answers_pattern.id_question=iherba_question.id_question and iherba_roi_answers_pattern.source =  '$sourceanswer')
	UNION
	select iherba_roi.id, iherba_question.id_question,iherba_photos.nom_photo_final
	from iherba_observations,iherba_question,iherba_roi_answers_pattern,iherba_roi,iherba_photos
	where iherba_question.id_question_necessaire=iherba_roi_answers_pattern.id_question
	and iherba_roi.id=iherba_roi_answers_pattern.id_roi
	AND iherba_roi.id_photo = iherba_photos.idphotos
	AND iherba_observations.idobs = iherba_photos.id_obs
	AND $choixpriorite
	AND iherba_question.disabled = 0
	and iherba_question.id_reponse_necessaire=iherba_roi_answers_pattern.id_answer_most_common and id_langue='$langue'
	and not exists (select * from iherba_roi_answers_pattern
	where iherba_roi_answers_pattern.id_roi=iherba_roi.id
	and iherba_roi_answers_pattern.id_question=iherba_question.id_question and iherba_roi_answers_pattern.source = '$sourceanswer')
	 ORDER BY Rand() limit 1"; //
	//echo $sql_questions_sans_pattern;
	$sql_comparaison = "select *
	from iherba_roi_comparaison
	where iherba_roi_comparaison.disabled = 0
	ORDER BY Rand() limit 1";
	
	//favoriser questions simples (8 sur 10) et seulement quelques comparaisons (2/10)
	// PL : plus que des questions ! <11
	if(time()%10<11)
	{
	$result_questions_sans_pattern= mysql_query($sql_questions_sans_pattern) or die ();
	if($row_quest= mysql_fetch_assoc($result_questions_sans_pattern) ){
			$resultat[]= "question";
			$resultat[]=$langue;
			$resultat[]=$row_quest['id_roi'];
			
			if(!isset($_GET['id_question']))
				$resultat[]=$row_quest['id_question'];
				else
				$resultat[]=$_GET['id_question']; // permet de forcer la question (attention la roi peut ne pas etre en rapport)
			
			$resultat[]=$row_quest['nom_photo_final'];
			
			$sql_log_question = "INSERT INTO `typoherbarium`.`iherba_log_questions` ( `id_roi` , `id_question` , `questiontype` ,`identifiant_internaute`, `ipinternaute` , `langue`  )
VALUES (".$row_quest['id_roi'].",".$row_quest['id_question'].",'question','$gateau','".$_SERVER['REMOTE_ADDR']."','".$langue."')";
			$enregistre_log_question= mysql_query($sql_log_question)or die ();
			return $resultat;
			
		}
		else {
			return "0";
			}
	}
	else {
	$result_comparaison= mysql_query($sql_comparaison) or die ();
	if($row_quest= mysql_fetch_assoc($result_comparaison) ){
			$resultat[]= "comparaison";
			$resultat[]=$langue;
			$resultat[]=$row_quest['id_roi_target'];
			$resultat[]=$row_quest['id_roi_comp'];
			
			$sql_log_question = "INSERT INTO `typoherbarium`.`iherba_log_questions` ( `id_roi` , `id_question` ,`questiontype` , `identifiant_internaute` , `ipinternaute`, `langue`  )
VALUES (".$row_quest['id_roi_target'].",".$row_quest['id_roi_comp'].",'comparaison','$gateau','".$_SERVER['REMOTE_ADDR']."','".$langue."')";
			$enregistre_log_question= mysql_query($sql_log_question)or die ();
			return $resultat;
			
		}
		else {
			return "0";
			}
	}
	
}

/*Une fois qu'une question à été tirée aléatoirement, il faut l'afficher avec les réponses possibles correspondantes.
 * Pour cela, on va générer un formulaire HTML*/
 
function affichage_question($langue){
global $modeledessins,$modeletexte,$legende99,$legendeabus;
global $debug_reponse;
	$contenu='<form method="post"  id="questionform" enctype="multipart/form-data" action="#">';
	
	if(isset($_GET['answeringuser']))
		$sourceanswer = $_GET['answeringuser']; 
		else 
		$sourceanswer = "network"; 
	if($debug_reponse==1)echo "source de la question : $sourceanswer <br>";
	$tab=choisir_question($langue,$sourceanswer);
	if($tab=="0"){
		return "<!--pas de question-->";
	}
	$typequestion = $tab[0];
	$langue=$tab[1]; //on récupère la langue utilisée, soit 'fr' pour le français soit 'en' pour l'anglais
	$id_roi=$tab[2]; //on récupère l'identifiant de la zone sur laquelle on va poser la question
	$id_question=$tab[3]; //on récupère la question ou la comparaison à poser 
			
	$nom_image ="roi_".$id_roi.".jpg"; //nom de l'image dans le répertoire roi_vigettes, il s'agit de son id_roi
	$contenu.='<a href="/scripts/large.php?name='.$nom_image.'" target="_blank"><img src="'.vignettes_questions."/".$nom_image.'" border="0"></a>'."<br/>"; //on affiche l'image sur laquelle on va poser une question
	$contenu.= "\n".'<font size="-2"><a href="rapport.php?id_roi='.$id_roi.'">'.$legendeabus[$langue].'</a></font>'."<br/><br/>\n"; //
			
	if($typequestion=="comparaison"){
		$utiliseimage=1;
		$sqlcomp="select * from iherba_roi_comparaison where id_roi_comp=$id_question ";
		$resultcomp = mysql_query($sqlcomp)or die ();
		if(!($lacomparaison = mysql_fetch_assoc($resultcomp)))return "";
		$contenu.= "Quelle photo ci-dessous est la plus proche de la photo ci-dessus ?";
		$tab_reponses=explode("!",$lacomparaison['list_candidates']); //chaque réponse est séparée par un "!" 
		$numero_reponse=0;
		foreach ($tab_reponses as $reponse){ //définition des boutons radio et donc des réponses possibles */
				$lillustration="roi_".$reponse.".jpg";
				$cas = str_replace("srcimagette","/medias/roi_vignettes/".$lillustration,$modeledessins);
				$cas = str_replace("altdescription","illustration for a roi",$cas);
				$cas = str_replace("licenceimagette","/scripts/large.php?name=".$lillustration,$cas);
				$cas = str_replace("valeurreponse",$numero_reponse,$cas);
				$contenu.=$cas."<br/>\n";
				$numero_reponse++; 
		}
		
	} else {
		/* on sélectionne le texte de la question et les réponses possibles pour la question d'id_question indiqué  */
		$sqlq="select texte_question,textes_reponses,choice_detail from iherba_question where id_question=$id_question and id_langue='$langue'";
		$sqlq="select * from iherba_question where id_question=$id_question and id_langue='$langue'";
		$result2 = mysql_query($sqlq)or die ();
		if(!($laquestion = mysql_fetch_assoc($result2)))return "";
		$tab_reponses=explode("!",$laquestion['textes_reponses']); //chaque réponse est séparée par un "!" 
		
		$contenu.=$laquestion['texte_question']."<br/>\n";
		$reponsespossibles = explode("!",$laquestion['choice_detail']);
		
		$numero_reponse=0;
		$utiliseimage=0;
		foreach ($tab_reponses as $reponse){ //définition des boutons radio et donc des réponses possibles */
			
			if((!(strpos($reponse,"jpg")===false))||(!(strpos($reponse,"png")===false))){
				$utiliseimage=1;
				$cas = str_replace("srcimagette","../dessins/w130/".$reponse,$modeledessins);
				$cas = str_replace("altdescription","illustration for ".$reponsespossibles[$numero_reponse],$cas);
				$cas = str_replace("licenceimagette","/scripts/licence.php?name=".$reponse,$cas);
				$cas = str_replace("valeurreponse",$numero_reponse,$cas);
				//$cas.'<input type="radio" name="reponse"  value="'.$numero_reponse.'" />'.
				$contenu.=$cas.' '."\n";
			}
			else{
				/* autre question sans schéma, sous forme de réponses textes*/
				$cas = str_replace("valeurreponse",$numero_reponse,$modeletexte);
				$cas = str_replace("laquestion",$reponse,$cas);
				
				//$contenu.=$reponse.'<input type="radio" name="reponse"  value="'.$numero_reponse.'"/>'."<br/>\n";
				$contenu.=$cas.'<font size="-4"><br/></font>'."\n";
				
			}
			$numero_reponse++; 
		}
	}
	if($utiliseimage){
		$cas = str_replace("srcimagette","../dessins/w130/99_nsp.jpg",$modeledessins);
		$cas = str_replace("altdescription",$legende99[$langue],$cas);
		$cas = str_replace("licenceimagette","/scripts/licence.php?name=99_nsp.jpg",$cas);
		$cas = str_replace("valeurreponse",99,$cas);
		}
		else {
		$cas = str_replace("valeurreponse",99,$modeletexte);
		$cas = str_replace("laquestion",$legende99[$langue],$cas);
				
				
		}
	$contenu.=$cas."<br/>\n";
	
	if(isset($_POST['referrant']))
		$referrant = $_POST['referrant'];
		else
		{
		if(isset($_SERVER['HTTP_REFERER']))
			$referrant = $_SERVER['HTTP_REFERER'];
			else
			$referrant = "";
		}
	$contenu.=
		'<input type="hidden" name="questiontype" value="'.$typequestion.'">
		<input type="hidden" name="id_roi" value="'.$id_roi.'">
		<input type="hidden" name="retour_suite_form" value="1">
		<input id="reponse_cachee" name="reponse_cachee" value="-1" type="hidden">
		<input type="hidden" name="id_question" value="'.$id_question.'">
		<input type="hidden" name="referrant" value="'.$referrant.'">
		<input type="hidden" name="sourceanswer" value="'.$sourceanswer.'">';
		
	if($utiliseimage==0)$contenu.='<input type="submit" value="Valider">';
	$contenu.='</form>';
	
	if(isset($_GET['id_question']))
		{
		$reponsespossibles = explode("!",$laquestion['choice_detail']);
		for($numrep=0;$numrep<count($reponsespossibles);$numrep++)
			{
			$ligne1 = $laquestion['choice_explicitation_one'];
			$ligne2 = $laquestion['choice_explicitation_two_seldom'];
			$ligne3 = $laquestion['choice_explicitation_two_often'];
			$bloc = " $ligne1 <br> $ligne2 <br> $ligne3 <br>";
			$bloc = str_replace("#1",$reponsespossibles[$numrep],$bloc);
			$autrerep = $numrep+1;if($autrerep==count($reponsespossibles))$autrerep = 0;
			$bloc = str_replace("#2",$reponsespossibles[$autrerep],$bloc).' <hr noshade width="300" size="3" align="left">';
			$contenu.= $bloc;
			}
		}
	return $contenu;
}

/*Cette fonction permet de remplir la table sql iherba_roi_answer à partir des champs obtenus grâce aux deux fonctions précédentes.
 * Cette table contient les champs : id_roi, id_question, answer( qui est le numéro de la réponse de l'utilisateur, obtenu par le biais du formulaire)
 * date(date du jour)et le champs identifiant_internaute qui est l'adresse IP de l'utilisateur */
function remplir_tables_reponses(){
	global $gateau;
	global $debug_reponse;
	$id_question=$_POST['id_question'];
	$questiontype= $_POST['questiontype'];
	$id_roi=$_POST['id_roi'];
	$val_reponsecache=$_POST['reponse_cachee'];
	if($val_reponsecache>=0)
		$reponse=$val_reponsecache;
		else
		{if(!(isset($_POST['reponse'])))return; //formulaire validé sans valeur
		$reponse=$_POST['reponse'];
		}
	$adresse_ip=$_SERVER['REMOTE_ADDR']; //adresse IP de l'utilisateur
	$sourceanswer = $_POST['sourceanswer'];
	$referrant = $_POST['referrant'];
	/* On insère dans la table iherba_roi_answer la réponse à la question d'identifiant $id_question pour la zone d'identifiant $id_zone 
	 * en précisant la réponse cochée, c'est-à-dire la réponse de numéro $reponse*/
	bd_connect();
	$nomduchamps = "id_".$questiontype;
	$sql_roi_answer="insert into iherba_roi_answer(id_roi,$nomduchamps,answer,identifiant_internaute,ipinternaute,source,referrant)values('$id_roi','$id_question','$reponse','$gateau','$adresse_ip','$sourceanswer','$referrant')";
	$result = mysql_query($sql_roi_answer)or die ();
	echo "<!-- $sql_roi_answer -->";
	/* reaction différentes selon l'origine de la réponse */
	if($questiontype=="question")
		{
		if($sourceanswer=="network")
			$nbsignificatif= 4;
			else
			// cas intervention directe, par exemple utilisateur, autre procedure
			$nbsignificatif= 0;
		}
	if($questiontype=="comparaison")
		{
		if($sourceanswer=="network")
			$nbsignificatif= 1; // donc on s'arrete apres la deuxieme
			else
			// cas intervention directe, par exemple utilisateur, autre procedure
			$nbsignificatif= 0;
		}
	// nombre de réponses déjà existantes pour la zone de numéro $id_roi et pour la question $id_question
	$sql_nombres_reponses="select id_roi,id_question, id_comparaison,count(answer)from iherba_roi_answer where id_roi=$id_roi and $nomduchamps=$id_question and source = '$sourceanswer' group by id_roi,$nomduchamps";
if($debug_reponse==1)	echo $sql_nombres_reponses;
	$result_nombres_reponses = mysql_query($sql_nombres_reponses)or die (mysql_error ( ));
	if($reponses = mysql_fetch_assoc($result_nombres_reponses) ){
		$nb_reponses=$reponses['count(answer)'];
		if( $nb_reponses > $nbsignificatif){ // if enough answers
				reponses_frequentes($reponses['id_roi'],$reponses[$nomduchamps],$questiontype,$sourceanswer);
				}
		}
	
}

/* cette fonction utilise une requête sql permettant de sélectionner les réponses les plus fréquemment choisies parmis
* l'ensemble des réponses obtenues pour une zone et une question précise */
function reponses_frequentes($id_roi,$id_question,$questiontype,$source){
	// si la ligne de pattern n'existe pas encore
	$nomduchamps = "id_".$questiontype;
	$sql_si_pattern_existe_deja="select id_roi from iherba_roi_answers_pattern where id_roi=$id_roi and $nomduchamps=$id_question and source= '$source' ";
	
	$result_si=mysql_query($sql_si_pattern_existe_deja)or die (mysql_error ( ));
	$num_rows_si= mysql_num_rows($result_si);

	if($num_rows_si>0)return; //ligne answer deja créée

	bd_connect();
	$textecomparaison="";$tablecomparaison="";$wherecomparaison="";
	if($questiontype=="comparaison")
		{
		$textecomparaison = ",list_candidates ";
		$tablecomparaison=" , iherba_roi_comparaison ";
		$wherecomparaison=" AND id_roi_comp = id_comparaison ";
		}
	$liste_reponse="select id_roi ,id_question,id_comparaison $textecomparaison ,answer,count(answer) as nb_reponses
	from iherba_roi_answer  $tablecomparaison
	where id_roi=$id_roi and $nomduchamps=$id_question and source = '$source' $wherecomparaison
	group by id_roi,id_question,answer
	order by nb_reponses desc ";
	
	$result2= mysql_query($liste_reponse)or die ();
	$nb_total_reponses=0;
	while($ligne_reponse = mysql_fetch_assoc($result2) ){
	//print_r($ligne_reponse);
	if($questiontype=="comparaison")
					{
					$tableaureponse = explode("!",$ligne_reponse['list_candidates']);
					//print_r($tableaureponse);
					$reponse[]=$tableaureponse[$ligne_reponse['answer']]; //numéro de la réponse
					}
					else
					$reponse[]=$ligne_reponse['answer']; //numéro de la réponse
		$nb_reponses[]=$ligne_reponse['nb_reponses']; //nombre de réponses
		$nb_total_reponses +=$ligne_reponse['nb_reponses']; //verifier si toujours 1 ???
	}
	//print_r($reponse);
	//calcul hyper simplifié de la qualité de la reposne. 
	
	if($nb_total_reponses>8)$valeur_qualite_reponse=1;else
		if($nb_total_reponses>4)$valeur_qualite_reponse=0.8;else
			$valeur_qualite_reponse=0.7;
			
	$pattern_answers = "";
	for($i=0;$i<count($nb_reponses);$i++)
		{
		$probreponse[$i] = round(($nb_reponses[$i]/$nb_total_reponses),2);
		$pattern_answers .= $reponse[$i].":".$probreponse[$i] .";";
		}
	
	$prob_most_common=$probreponse[0]*100;
	if(isset($reponse[1]))
		{
		$prob_just_less = $probreponse[1]*100;
		$secondanswer = $reponse[1];
		}
		else 
		{
		$prob_just_less =0;
		$secondanswer = 0;
		}
		
		
	$sql_insertion_table="insert into iherba_roi_answers_pattern(id_roi,$nomduchamps,id_answer_most_common,id_just_less_common,prob_most_common,
			prob_just_less,pattern_answers,qualite_reponse,source)
			values('$id_roi','$id_question','$reponse[0]','$secondanswer','$prob_most_common','$prob_just_less','$pattern_answers',$valeur_qualite_reponse, '$source')";
	$result_insertion_table= mysql_query($sql_insertion_table)or die ();

		
	/*maintenant on reagrde s'il reste des questions posables pour cette observation , meme requete avec idobs forcé */
	$sql_idobs="
	select iherba_photos.id_obs 
	from iherba_roi,iherba_photos
	where iherba_roi.id=$id_roi and iherba_roi.id_photo=iherba_photos.idphotos";
	$result_idobs= mysql_query($sql_idobs)or die ();
	if($row_idobs= mysql_fetch_assoc($result_idobs) ){
		$id_obs=$row_idobs['id_obs'];
		if($questiontype=="question")
				{
				$langue = "fr";//attention
				$sql_questions_sans_pattern="SELECT iherba_roi.id as id_roi,iherba_question.id_question ,iherba_photos.nom_photo_final
	FROM iherba_observations, iherba_photos, iherba_roi, iherba_roi_tag, iherba_question
	WHERE iherba_observations.determination_achevee=0
	and iherba_observations.idobs = $id_obs
	AND iherba_observations.idobs = iherba_photos.id_obs
	AND iherba_roi.id_photo = iherba_photos.idphotos
	AND iherba_roi.id = iherba_roi_tag.id_roi
	AND iherba_question.disabled = 0
	AND iherba_question.id_tag_necessaire = iherba_roi_tag.id_tag
	and iherba_question.id_langue='$langue' and iherba_question.id_question_necessaire=0
	and not exists (select * from iherba_roi_answers_pattern
	where iherba_roi_answers_pattern.id_roi=iherba_roi.id
	and iherba_roi_answers_pattern.id_question=iherba_question.id_question and iherba_roi_answers_pattern.source =  '$source')
	UNION
	select iherba_roi.id, iherba_question.id_question,iherba_photos.nom_photo_final
	from iherba_question,iherba_roi_answers_pattern,iherba_roi,iherba_photos
	where iherba_question.id_question_necessaire=iherba_roi_answers_pattern.id_question
	and iherba_roi.id=iherba_roi_answers_pattern.id_roi
	AND iherba_roi.id_photo = iherba_photos.idphotos
	AND iherba_photos.id_obs = $id_obs
	AND iherba_question.disabled = 0
	and iherba_question.id_reponse_necessaire=iherba_roi_answers_pattern.id_answer_most_common and id_langue='$langue'
	and not exists (select * from iherba_roi_answers_pattern
	where iherba_roi_answers_pattern.id_roi=iherba_roi.id
	and iherba_roi_answers_pattern.id_question=iherba_question.id_question and iherba_roi_answers_pattern.source = '$source')
	ORDER BY Rand() limit 1";
	//echo $sql_questions_sans_pattern;
	$result_questions_sans_pattern= mysql_query($sql_questions_sans_pattern) or die ();
	$nbquestion = mysql_num_rows($result_questions_sans_pattern);
	
	$thishost  = $_SERVER['HTTP_HOST'];
	$thisuri  = rtrim($_SERVER['PHP_SELF'], "/\\");

   
	$webroot= "http://$thishost$thisuri";


	if($nbquestion==0 ){
					// plus de questions posables
					// provoque le passage à l'etat comparaison pour cette observation
					$res = file("$webroot/collaborative/fin_questions_pour_une_obs.php?numobs=$id_obs");
					}
				}
				
		}
}


if(isset($_POST['retour_suite_form'])){
	//si le fichier est appelé après le POST d'un formulaire
	// on traite les données avant  de poser une nouvelle question
	remplir_tables_reponses();
}

$langue=choisir_langue();

$question_poser =affichage_question($langue);
$modele = file_get_contents('template_question.html');
$aide = "Please help us to identify this picture:";
if($langue=="fr")$aide = "Merci de nous aider &agrave; identifier :";
if($langue=="de")$aide = "Danke für ihre Mitthilfe, diese Abbildungen zu identifizierenen:";
$page_generee= str_replace('###formulaire###',$question_poser,$modele);
$page_generee= str_replace('###aide###',$aide,$page_generee);

echo $page_generee;
?>
