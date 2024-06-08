<?php

ob_start();


include 'connectionFactory.php';

include '_commonBlock.php';
$mysqli= ConnectionFactory::GetConnection(); 


if (empty($_REQUEST['ota'])) {
    header('Location: ./index.php');
}

$new_sid ='';
$done=false;

$stmt = $mysqli->prepare("SELECT OTA.StrId, OTA.UserId, DisplayName, Salt , CURRENT_TIMESTAMP < OTA.CreatedOn + INTERVAL 5 MINUTE, OTA.Used , OTA.SId FROM OTA INNER JOIN TournamentSiteUser ON TournamentSiteUser.Id=OTA.UserId WHERE OTA.StrId=?");
$stmt->bind_param("s", $_REQUEST['ota']);         
$stmt->bind_result($otaid,$userid,$username,$salt, $timevalid,$used,$sid);     
$stmt->execute();
$stmt->fetch();
$stmt->close();
    
if (empty($otaid)) {
    header('Location: ./index.php');
}

$stmt = $mysqli->prepare("UPDATE OTA SET OTA.Used=OTA.Used+1 WHERE OTA.StrId=?");
$stmt->bind_param("s", $otaid);         
$stmt->execute();
$stmt->close();

if ($used==1 && $timevalid==1 && !empty($_POST['sid']) && !empty($_POST['psw']) && !empty($_POST['psw2'])) {
    if ($_POST['sid']!=$sid){
        header('Location: ./index.php');
    }
    
    if ($_POST['psw']!=$_POST['psw2']) {
         $done=true;
         errorMessage("Les deux mots de passes ne sont pas identiques. Redemandé un code d'accès.");
    } else {
	$password_md5 = md5($_POST['mdp'].$Salt);
	$stmt = $mysqli->prepare("UPDATE TournamentSiteUser SET Password=? WHERE Id=?");
	$stmt->bind_param("si", $password_md5, $userid);         
	$stmt->execute();
	$stmt->close();
	
        $done=true;
	successMessage();
    } 
} else if ($used==0 && $timevalid==1){
      $new_sid = substr(md5($Salt. $otaid.date('Y-m-d:h:m:s')),0,12);
      $stmt = $mysqli->prepare("UPDATE OTA SET OTA.sid=? WHERE OTA.StrId=?");
      $stmt->bind_param("ss", $new_sid, $otaid);         
      $stmt->execute();
      $stmt->close();
}

if (! $done){
	if ($timevalid==0){
	       errorMessage("Ce code d'accès est échus! Redemandé un code d'accès.");
	} else if ($used>1){
	       errorMessage("Ce code d'accès a déjà été utilisé! Redemandé un code d'accès.");
	} else if (!empty($new_sid)){
	   getForm();
	}
}

function errorMessage($message){
 //TODO 
}
function successMessage(){
 //TODO 
}
function getForm(){
     //TODO form
writeHead();
}







	
    


echo'
<body>
    <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 
echo ' 
            <span class="h_title">Changement de Mot de passe</span>
           
            <span class="h_txt">
            <form action="./addUser.php" method="post"> 
            <input type="hidden" name="uid" value="'.$Id.'"/>
                <span class="fitem">
                    <span class="label">Identifiant* :</span>
                    <input type="text" name="mail" value="'.$EMail.'" '.$readonly.'/><br/>
                </span>
                <span class="fitem">
                    <span class="label">Nom publique* :</span>
                    <input type="text" name="disp" value="'.$disp.'" /><br/>
                </span>';
if ($isReadonly==0){
   echo' 
                <span class="fitem">
                     <span class="label">Mot de passe* :</span>
                     <input type="text" name="psw" /><br/>
                </span>';
}
echo'  
               <span class="fitem">
                    <span class="label">Droits Admin :</span>
                    <input type="checkbox" name="da" value="1" '; if ($IsAdmin==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
               <span class="fitem">
                    <span class="label">Droits Inscription :</span>
                    <input type="checkbox" name="dr" value="1" '; if ($IsRegistration==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
               <span class="fitem">
                    <span class="label">Droits Acceuil :</span>
                    <input type="checkbox" name="dwc" value="1" '; if ($IsWelcome==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
                <span class="fitem">
                    <span class="label">Droits Pesée :</span>
                    <input type="checkbox" name="dwg" value="1" '; if ($IsWeighting==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
               <span class="fitem">
                    <span class="label">Droits Table Centrale :</span>
                    <input type="checkbox" name="dmt" value="1" '; if ($IsMainTable==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
               <span class="fitem">
                    <span class="label">Droits Table Tatami :</span>
                    <input type="checkbox" name="dtt" value="1" '; if ($IsMatTable==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
             
               <span class="btnBar "> 
                    <input class="pgeBtn" type="submit" value="'.$button.'" title="'.$button.'" /> 
               </span>
               
           </form>
            </span>
           <span class="h_txt"> 
                <span class="btnBar"> 
                   <a class="pgeBtn" href="admin.php" title="Fermer" >Fermer</a>
               </span>
           </span>    
           </div>     
        </div>   
     </div>
</body>
</html>';
?>  
