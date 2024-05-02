<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsAdmin']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';

include '_commonBlock.php';

writeHead();

  $mysqli= ConnectionFactory::GetConnection();	
  $mail=$_POST['mail'];
  $disp=$_POST['disp'];
  $psw    = $_POST["psw"]; 
  $salt   = date('Y-m-d:h:m:s');
  if (isset($_POST["da"])){  $da = 1 ; } else { $da = 0 ;}
  if (isset($_POST["dr"])){  $dr = 1 ; } else { $dr = 0 ;}
  if (isset($_POST["dwc"])){  $dwc = 1 ; } else { $dwc = 0 ;}
  if (isset($_POST["dwg"])){  $dwg = 1 ; } else { $dwg = 0 ;}
  if (isset($_POST["dmt"])){  $dmt = 1 ; } else { $dmt = 0 ;}
  if (isset($_POST["dtt"])){  $dtt = 1 ; } else { $dtt = 0 ;}
  $uid    = $_POST["uid"];
  $password_md5 = md5($psw.$salt);
  
  if (isset($_GET['d']) and isset($_GET['uid'])){
     $stmt =$mysqli->prepare("DELETE FROM TournamentSiteUser WHERE Id=?");
     $stmt->bind_param("i",$_GET['uid'] );
     $stmt->execute();
     $stmt->close();
  } else {
  
      if ($uid>0){
          if (!($stmt = $mysqli->prepare("UPDATE TournamentSiteUser SET DisplayName=?, IsAdmin=?, IsRegistration=?, IsWelcome=?, IsWeighting=?, IsMainTable=?, IsMatTable=? WHERE Id=?"))){
         //echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
       }
          $stmt->bind_param("siiiiiii",$disp,$da,$dr,$dwc,$dwg,$dmt,$dtt,$uid );
          $stmt->execute();
          $stmt->close();
      } else {
          $stmt = $mysqli->prepare("INSERT  TournamentSiteUser (EMail,DisplayName, Salt,Password,IsAdmin,IsRegistration,IsWelcome,IsWeighting,IsMainTable,IsMatTable) VALUES (?,?,?,?,?,?,?,?,?,?)");
          $stmt->bind_param("sssiiiiiii",$mail,$disp,$salt,$password_md5,$da,$dr,$dwc,$dwg,$dmt,$dtt);
          $stmt->execute();
          $stmt->close();
      }
  }

//echo $mail.' '.$salt.' '.$password_md5.' '.$da.' ';

  header('Location: ./admin.php');
  exit();
?>
