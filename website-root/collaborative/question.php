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

if($_SERVER['REMOTE_ADDR']=='94.23.195.65')
        $xmlgeneration = 1; else $xmlgeneration = 0;

if($xmlgeneration == 0)
	{echo '<head>';
echo '<link rel="stylesheet" type="text/css" href="questionstyle.css" media="all" />';
echo '</head>';
}

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

if($xmlgeneration == 0)
	echo '<body>';
	else
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
  if($xmlgeneration == 0)
	$content = '<div>' . $qv->viewQuestion($taskCcq, $roi, $s) . '</div>';
	else
        $content =  $qv->viewQuestion($taskCcq, $roi, $s);
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

if($xmlgeneration == 0)
	echo "</body>";
	else
	echo '</question>';

?>
