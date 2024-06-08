<?php

ob_start();


function errorMessage($message){
    writeHead();
   
echo'
<body>
    <div class="f_cont">       
       <div class="cont_l">
         <div class="h">
            <span class="h_title">Echeque du changement du mot de passe</span>
           
            <span class="h_txt">
            '.$message.'
            <br/>
           <a class="pgeBtn" href="index.php">Page d\'acceuil</a> 
           </span>
           </div>     
        </div>   
     </div>
</body>
</html>';
}
function successMessage(){
   writeHead();
   
echo'
<body>
    <div class="f_cont">       
       <div class="cont_l">
         <div class="h">
            <span class="h_title">Mot de passe changé</span>
           
            <span class="h_txt">
            Naviguer sur la page d\'acceuil pour vous y identifier <a class="pgeBtn" href="index.php">Naviguer</a> 
           </span>
           </div>     
        </div>   
     </div>
</body>
</html>';

}

function getForm(){
     
writeHead();
   
echo'
<body>
    <div class="f_cont">       
       <div class="cont_l">
         <div class="h">
            <span class="h_title">Changement de Mot de passe</span>
           
            <span class="h_txt">
            <form action="./mdp.php" method="post" id="frm"> 
            <input type="hidden" name="ota" value="'.$otaid.'"/>
            <input type="hidden" name="sid" value="'.$new_sid.'"/>
                <span class="fitem">
                    <span class="label">Bonjour '.$username.' :</span>
                </span>
                <span class="fitem">
                    <span class="label">Nouveau mot de passe* :</span>
                     <input type="password" name="psw" id="psw"/><br/>
                </span><br/>
                <span class="label">Vérifier le mot de passe* :</span>
                     <input type="password" name="psw2" id="psw2 /><br/>
                </span><br/>
                <span id="msg"></span>
                 <span class="btnBar "> 
                    <a class="pgeBtn" onclick="
                    
                    if (document.getElementById(\'psw2\').value==document.getElementById(\'psw\').value) {
                       document.getElementById(\'frm\').submit();
                    } else {
                       document.getElementById(\'psw2\').value=\'\';
                       document.getElementById(\'msg\').innerHTML=\'Les deux mots de passes ne correspondent pas<br/>\';
                       
                    } " >Enregistrer le mot de passe</a>
               </span>
               
            </form>     
           </div>     
        </div>   
     </div>
</body>
</html>';
                    

}



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



?>  
