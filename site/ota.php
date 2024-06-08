<?php

ob_start();
session_name("Tournament");	
session_start();
include 'connectionFactory.php';

if ($_SESSION['_IsAdmin']!=1) {
	header('Location: ./index.php');
}


if(empty((int)$_REQUEST['uid'])) {
      	header('Location: ./index.php');
}
  
$user_id = (int)$_REQUEST['uid'];
  

// check user
$mysqli= ConnectionFactory::GetConnection(); 
$stmt = $mysqli->prepare("SELECT Id,EMail,DisplayName,Salt, (now() + INTERVAL 5 MINUTE) FROM TournamentSiteUser WHERE Id=?");
$stmt->bind_param("i", $user_id);     
$stmt->bind_result($uid, $EMail,$disp,$salt,$date);
$stmt->execute();
$stmt->fetch();
$stmt->close();

if($user_id!=$uid) {
      	header('Location: ./index.php');
}
  

// invalid existing OTA
$stmt = $mysqli->prepare("UPDATE OTA SET OTA.Used=10 WHERE OTA.UserId=?");
$stmt->bind_param("i", $user_id);         
$stmt->execute();
$stmt->close();

// create new OTA
$new_otid = substr(md5($salt.$user_id.date('Y-m-d:h:m:s')),0,12);

$stmt = $mysqli->prepare("INSERT OTA (StrId,UserId) VALUES (?,?)");
$stmt->bind_param("si",$new_otid, $user_id);         
$stmt->execute();
$stmt->close();



include '_commonBlock.php';
writeHead();

 
echo'
<body>
  <div class="f_cont">       
      <div class="cont_l">
        <div class="h">
            <span class="h_title">Accès unique pour changer de mot de passe</span>
            Desiné à :'.$disp.'<br/>
            Valide jusqu\'à :'. $date.'<br/>
            <div id="qrcode"></div>
           <div class="url">https://'.$_SERVER['HTTP_HOST'].'/mdp.php?ota='.$new_otid.'</div>

         </div>     
        </div>  
     </div>   
    
</body>

<script type="text/javascript" src="js/qrcode.js"></script>

<script type="text/javascript">
var qrcode = new QRCode(document.getElementById("qrcode"), {
	width : 250,
	height : 250
});


qrcode.makeCode("http://'.$_SERVER['HTTP_HOST'].'/mdp.php?ota='.$new_otid.'");


</script>
</html>';

?>

