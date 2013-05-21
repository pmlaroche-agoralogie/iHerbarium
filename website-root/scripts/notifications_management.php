<?php
// read a pipeline of notification to send, and send the first one

require_once("../bibliotheque/common_functions.php");

function dieanddebug()
{
 die(mysql_error());
 //die();
}

function taxon_link_list($taxonref,$taxon,$mylanguage)
{ 
    $obsQuery =
      " SELECT * 
      FROM  `iherba_taxon_links` 
      WHERE  `taxon_api` LIKE  '$taxonref'
      AND  `taxon` LIKE  '$taxon'
      AND lng = '$mylanguage' ";
    
    $linkResult = mysql_query($obsQuery) or dieanddebug ();
    $link_message = "";
    while($current_link = mysql_fetch_assoc($linkResult)){
	//$link_message .= "<a href=".$current_link['link'].">".$current_link['legend_link']."</a>\n";
	$link_message .= $current_link['legend_link'] ." : ".$current_link['link']."\n";
    }
    return $link_message;
}

function expertise_very_close_to($topibsid,$mylanguage)
{
    $obsQuery =
      " SELECT *" .
      " FROM iherba_observations" .
      " WHERE idobs = '$topibsid'";
    
    $obsResult = mysql_query($obsQuery) or dieanddebug ();
    assert(mysql_num_rows($obsResult) == 1);
    $current_observation = mysql_fetch_assoc($obsResult);
    
    $url_top_observation = "http://".language_url_observation_from_lang_iso($mylanguage).$current_observation['idobs'];
     
    $expertise_message = get_string_language_sql('mail_notif_expert_system_expertise_sure_one',$mylanguage) ;
    
    
    $nameQuery =
      " SELECT *" .
      " FROM iherba_determination" .
      " WHERE tropicosid = '".$current_observation['computed_best_tropicos_id']."'";
    $nameResult = mysql_query($nameQuery) or dieanddebug ();
    $current_determination = mysql_fetch_assoc($nameResult);
    
    $expertise = $current_determination['nom_scientifique']. " (".$current_determination['genre']." , ".$current_determination['famille'].")";
    
    $expertise_message = str_replace("%s$1",$url_top_observation,$expertise_message);
    $expertise_message = str_replace("%s$2",$expertise,$expertise_message);
    
    $links = taxon_link_list('tropicos',$current_observation['computed_best_tropicos_id'],$mylanguage);
    if($links!="")
    {
       $links_intro = get_string_language_sql('more_info_list_taxon_link',$mylanguage) ;
       $expertise_message .= "\n".str_replace("%s$1",$links,$links_intro);
    }
    return $expertise_message;
}






function expertise_list_close_to($result,$mylanguage)
{
 $nbmax_taxon = 3;
 $expertise_message_global =  get_string_language_sql('mail_notif_expert_system_expertise_list_may',$mylanguage) ;
 
 $list_probable = "";
 
 $nbtaxon_displayed = 0;
 foreach($result as $obs)
 {
  if($nbtaxon_displayed<$nbmax_taxon)
   {
   $obsQuery =
     " SELECT *" .
     " FROM iherba_observations" .
     " WHERE idobs = '".$obs->obsId."'";
   
   $obsResult = mysql_query($obsQuery) or dieanddebug ();
   assert(mysql_num_rows($obsResult) == 1);
   $current_observation = mysql_fetch_assoc($obsResult);
   
   $url_the_observation = "http://".language_url_observation_from_lang_iso($mylanguage).$current_observation['idobs'];
   
   $expertise_message = get_string_language_sql('mail_notif_expert_system_one_proposal',$mylanguage) ."\n";
   
   $nameQuery =
     " SELECT *" .
     " FROM iherba_determination" .
     " WHERE tropicosid = '".$current_observation['computed_best_tropicos_id']."'";
    $nameResult = mysql_query($nameQuery) or dieanddebug ();
   $current_determination = mysql_fetch_assoc($nameResult);
   
   $expertise = $current_determination['nom_scientifique']. " (".$current_determination['genre']." , ".$current_determination['famille'].")";
   
   $expertise_message = str_replace("%s$1",$url_the_observation,$expertise_message);
   $expertise_message = str_replace("%s$2",$expertise,$expertise_message);
   
   $links = taxon_link_list('tropicos',$current_observation['computed_best_tropicos_id'],$mylanguage);
  if($links!="")
   {
    $links_intro = get_string_language_sql('more_info_list_taxon_link',$mylanguage) ;
    $expertise_message .= "\n".str_replace("%s$1",$links,$links_intro);
   }
   
   $list_probable .= $expertise_message;
   $nbtaxon_displayed++;
  }
 }
 $expertise_message_global = "\n".str_replace("%s$1",$list_probable,$expertise_message_global);
 return $expertise_message_global;
}

function notifyUserAbout_Account_Open($parameters,$mylanguage) {
 $send_notify=1; //not all notification are interesting people, we can decide to unable some

 // User
 $userQuery =
   " SELECT *" .
   " FROM fe_users" .
   " WHERE uid = '" . $parameters->user . "'";
 
 $userResult = mysql_query($userQuery) or dieanddebug();
 assert(mysql_num_rows($userResult) == 1);
 $user = mysql_fetch_assoc($userResult);
 $language_user = strtolower($user['language']);
 if($mylanguage=='')
  {
   //if no prefered language for the notification, use the owner language
   $mylanguage=strtolower($language_user);
  }

 // Prepare Mail
  $agoralogieAddress = "notification@".language_domainename_mail_from_lang_iso($mylanguage);
  $bccagoralogieAddress = 'agoralogie@gmail.com';

  $to = $user['email'];
  
  $subject = get_string_language_sql('mail_notif_open_account_title',$mylanguage) ;
  $message = get_string_language_sql('mail_notif_open_account_message',$mylanguage);
  //$url_observation = $obs['idobs']." : \n".'http://www.iherbarium.fr/index.php?id=detail&numero_observation='.$obs['idobs'];
  $message = str_replace("%s$1",language_domainename_mail_from_lang_iso($mylanguage),$message);
  $message = str_replace("%s$2",$user['password'],$message);
  $message .= get_string_language_sql('mail_notif_footer',$mylanguage) ;

  $headers = 
    "From: " . $agoralogieAddress . "\r\n" .
    "Bcc: " . $bccagoralogieAddress . "\r\n";
  
  // Send mail
 mail($to, $subject, $message, $headers);
 return  "to = \n$to \n Subject = \n".$subject." \n Message = \n".$message." \n Header =".$headers;
}



function notifyUserAbout_somebody_say_Determination($determinationId,$targetuser,$mylanguage,$id_notification ) {
    $monobjet=NULL;
    // Determination
    $determinationQuery =
      " SELECT *" .
      " FROM iherba_determination" .
      " WHERE id = '" . $determinationId . "'";
    $determinationResult = mysql_query($determinationQuery) or dieanddebug ();
   
    assert(mysql_num_rows($determinationResult) == 1);
    $determination = mysql_fetch_assoc($determinationResult);
    
    // Obs
    $obsQuery =
      " SELECT *" .
      " FROM iherba_observations" .
      " WHERE idobs = '" . $determination["id_obs"] . "'";
    
    $obsResult = mysql_query($obsQuery) or dieanddebug ();
    if(mysql_num_rows($obsResult)==0)
	{
	    // notification on non existing observation
	    $sql_delete = " delete from iherba_notification WHERE  `id_notification` = $id_notification ";
	    $obsResult2 = mysql_query($sql_delete) or dieanddebug ();
	    mail("philippe.laroche@agoralogie.fr","delete from notification",$sql_delete);
	}
     
 assert(mysql_num_rows($obsResult) == 1);
 $obs = mysql_fetch_assoc($obsResult);
 
 // User
 $userQuery =
   " SELECT *" .
   " FROM fe_users" .
   " WHERE uid = '" . $obs['id_user'] . "'";
 
 $userResult = mysql_query($userQuery) or dieanddebug ();

 assert(mysql_num_rows($userResult) == 1);
 $user = mysql_fetch_assoc($userResult);
 
 $language_user = strtolower($user['language']);
 if($mylanguage=='')
  {
   //if no prefered language for the notification, use the owner language
   $mylanguage=strtolower($language_user);
  }
  
  $links_intro="";
  if($determination['tropicosid']>0)
    {
	$links = taxon_link_list('tropicos',$determination['tropicosid'],$mylanguage);
	if($links!="")
	{
	   $plus = get_string_language_sql('more_info_list_taxon_link',$mylanguage) ;
	   $links_intro = str_replace('%s$1',$links,$plus);
	}
    }
    
 // Prepare Mail
  $agoralogieAddress = "notification@".language_domainename_mail_from_lang_iso($mylanguage);
  $bccagoralogieAddress = 'agoralogie@gmail.com';

  $to = $user['email'];
  
  $subject = get_string_language_sql('mail_notif_somebody_say_title',$mylanguage) . " ".$obs['idobs'];
  $message = get_string_language_sql('mail_notif_somebody_say_message',$mylanguage);
  //$url_observation = $obs['idobs']." : \n".'http://www.iherbarium.fr/index.php?id=detail&numero_observation='.$obs['idobs'];
  //$url_observation = " \n"."http://".language_url_observation_from_lang_iso($mylanguage)."numero_observation=".$obs['idobs'];
  $url_the_observation = "http://".language_url_observation_from_lang_iso($mylanguage).$obs['idobs'];
   
  
  $expertise = affiche_expertise($obs['idobs'],$monobjet,"courte",$x,1,$mylanguage);
  $message = str_replace("%s$1",$url_the_observation,$message);
  $message = str_replace("%s$2",$expertise,$message);
  $message = str_replace("%s$3",$links_intro,$message);
  
  $message .= get_string_language_sql('mail_notif_footer',$mylanguage) ;

  $headers = 
    "From: " . $agoralogieAddress . "\r\n" .
    "Bcc: " . $bccagoralogieAddress . "\r\n";
  
  // Send mail
 mail($to, $subject, $message, $headers);
 return  "to = \n$to \n Subject = \n".$subject." \n Message = \n".$message." \n Header =".$headers;
}

function notifyUserAbout_expert_system_say_Determination($parameters,$mylanguage) {
 $send_notify=1; //not all notification are interesting people, we can decide to unable some
 $expertise=""; // conclusion if any
 // Obs
 $obsQuery =
   " SELECT *" .
   " FROM iherba_observations" .
   " WHERE idobs = '" . $parameters->obsId . "'";
 
 $obsResult = mysql_query($obsQuery) or dieanddebug();

 assert(mysql_num_rows($obsResult) == 1);
 $current_observation = mysql_fetch_assoc($obsResult);

 // User
 $userQuery =
   " SELECT *" .
   " FROM fe_users" .
   " WHERE uid = '" . $current_observation['id_user'] . "'";
 
 $userResult = mysql_query($userQuery) or dieanddebug();

 assert(mysql_num_rows($userResult) == 1);
 $user = mysql_fetch_assoc($userResult);
 
 $language_user = strtolower($user['language']);
 if($mylanguage=='')
  {
   //if no prefered language for the notification, use the owner language
   $mylanguage=strtolower($language_user);
  }

 
 // Prepare Mail
  $agoralogieAddress = "notification@".language_domainename_mail_from_lang_iso($mylanguage);
  $bccagoralogieAddress = 'agoralogie@gmail.com';

  $to = $user['email'];
  
  $subject = get_string_language_sql('mail_notif_expert_system_say_title',$mylanguage) . " ".$current_observation['idobs'];
  
  
 if($parameters->verdict == 'TooSmallReferenceGroup')
  {
   if($current_observation['latitude']== 0)
   {
    $message = get_string_language_sql('mail_notif_expert_system_say_not_localized',$mylanguage);
    $send_notify = 0;
   }
   else
   $message = get_string_language_sql('mail_notif_expert_system_say_geoloc_notenough',$mylanguage);
  }
  else
  if(($parameters->verdict == 'TooMuchComparisonsNedded') ||($parameters->verdict == 'TooMuchComparisonsNeeded'))
  {
   $message = get_string_language_sql('mail_notif_expert_system_say_too_much_close',$mylanguage);
  }
  else
  
  {
   $message = get_string_language_sql('mail_notif_expert_system_say_message',$mylanguage);
  if($parameters->verdict == 'NoComparisonsNeeded')
    $expertise =  expertise_very_close_to($parameters->topObsId,$mylanguage);
 if($parameters->verdict == 'ComparisonsFinished')
    $expertise =  expertise_list_close_to($parameters->results,$mylanguage);
    if($parameters->results[0]->similarity <50)    $send_notify = 0; // if very low similarity, no tagged image was given, or only one very ininteresting

  }
  
  $url_observation = 'http://'.language_url_observation_from_lang_iso($mylanguage).$current_observation['idobs'];
  $message = str_replace("%s$1",$url_observation,$message);
  $message = str_replace("%s$2",$expertise,$message);
  
  $message .= get_string_language_sql('mail_notif_footer',$mylanguage) ;

  $headers = 
    "From: " . $agoralogieAddress . "\r\n" .
    "Bcc: " . $bccagoralogieAddress . "\r\n";
  
  // Send mail
 if($send_notify==1)
  {
   mail($to, $subject, $message, $headers);
  }
 return  "send_notify = $send_notify to = \n$to \n Subject = \n".$subject." \n Message = \n".$message." \n Header =".$headers;
}



//test if notification are still waiting
bd_connect();
$notsentnotification =
    " SELECT *" .
    " FROM  iherba_notification" .
    " WHERE  `ts_send` =  '0000-00-00 00:00:00'  ";
  
$notifications = mysql_query($notsentnotification) or dieanddebug();
if(mysql_num_rows($notifications)==0)die("0 to send");

//if at least one notification, do the first one
$thenotification = mysql_fetch_assoc($notifications);

if($thenotification['message_type'] == 'somebody-say')
 {
 $id_current_notification = $thenotification['id_notification'];
 $parameters = json_decode($thenotification['parameters']);
 $messagetexte = notifyUserAbout_somebody_say_Determination($parameters->determination,$parameters->owner,$thenotification['preferred_language'],$id_current_notification);
  }
if($thenotification['message_type'] == 'expert-system-say')
 {
 $id_current_notification = $thenotification['id_notification'];
 $parameters = json_decode($thenotification['parameters']);
 $messagetexte = notifyUserAbout_expert_system_say_Determination($parameters,$thenotification['preferred_language']);
  }

if($thenotification['message_type'] == 'account-open')
 {
 $id_current_notification = $thenotification['id_notification'];
 $parameters = json_decode($thenotification['parameters']);
 $messagetexte = notifyUserAbout_Account_Open($parameters,$thenotification['preferred_language']);
  }

  
$messagetexte =  str_replace("\n",'\n ', $messagetexte);
$messagetexte =  str_replace("\r",'', $messagetexte);
$messagetexte =  str_replace("'",'``', $messagetexte);
$sentnotification =
    " update iherba_notification" .
    " set  `ts_send` =  CURRENT_TIMESTAMP where id_notification = $id_current_notification ;";
$sentnotificationresult = mysql_query($sentnotification) or  dieanddebug();
$sentnotification =
    " update iherba_notification" .
    " set  `sent_text` =  '$messagetexte' where id_notification = $id_current_notification ";
  
$sentnotificationresult = mysql_query($sentnotification) or dieanddebug();

?>
