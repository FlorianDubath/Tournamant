<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsAdmin']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';
$mysqli= ConnectionFactory::GetConnection(); 

if($_POST && !empty($_POST['id'])) {

   if (!($stmt = $mysqli->prepare("UPDATE TournamentVenue SET Name=?,Place=?,Transport=?,Organization=?, Admition=?, System=?,Prize=?, Judge=?,Dressing=?,Contact=?,RegistrationEnd=?,TournamentStart=?,TournamentEnd=? WHERE Id=?"))){
      	     echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
      	  } 
      	  
      	  $stmt->bind_param("sssssssssssssi", $_POST['nme'], $_POST['plc'], $_POST['tsp'], $_POST['org'], $_POST['adm'], $_POST['sys'], $_POST['prz'], $_POST['jdg'], $_POST['drs'], $_POST['ctc'], $_POST['reg'], $_POST['ts'], $_POST['te'] , $_POST['id'] );
      	  
      	  
          if (!($stmt->execute())){
             echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
          }


}


include '_commonBlock.php';

writeHead();

echo'
<body>
    <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 

	     if (!($stmt = $mysqli->prepare("SELECT Id,Name,Place,Transport,Organization, Admition, System,Prize, Judge,Dressing,Contact,RegistrationEnd,TournamentStart,TournamentEnd FROM TournamentVenue order by Id desc limit 1"))){
      	     echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
      	  }
          if (!($stmt->execute())){
             echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
          }

          $stmt->bind_result( $Id,$Name,$Place,$Transport,$Organization, $Admition, $System,$Prize, $Judge,$Dressing,$Contact,$RegistrationEnd,$TournamentStart,$TournamentEnd);
          $stmt->fetch();
	      $stmt->close();
echo '	      
	      <form action="./global_config.php" method="post">
	         <span class="ftitle">
	             Informations Globales
	         </span>
	        <input type="hidden" name="id" value="'.$Id.'"/>
	        <span class="fitem">
               <span class="label">Nom du tournois:</span>
               <input class="inputText"  type="text" name="nme" value="'.$Name.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Lieu:</span>
               <input class="inputText"  type="text" name="plc" value="'.$Place.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Transports:</span>
               <input class="inputText"  type="text" name="tsp" value="'.$Transport.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Organisation:</span>
               <input class="inputText"  type="text" name="org" value="'.$Organization.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Admission:</span>
               <input class="inputText"  type="text" name="adm" value="'.$Admition.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Système:</span>
               <input class="inputText"  type="text" name="sys" value="'.$System.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Récompenses:</span>
               <input class="inputText"  type="text" name="prz" value="'.$Prize.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Arbitrage:</span>
               <input class="inputText"  type="text" name="jdg" value="'.$Judge.'" /><br/>
	        </span>
	       
	        <span class="fitem">
               <span class="label">Tenue:</span>
               <input class="inputText"  type="text" name="drs" value="'.$Dressing.'" /><br/>
	        </span>
	       
	        <span class="fitem">
               <span class="label">Contact:</span>
               <input class="inputText"  type="text" name="ctc" value="'.$Contact.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Inscription jusqu\'au:</span>
               <input class="inputDate"  type="date" name="reg" value="'.$RegistrationEnd.'" /><br/>
	        </span>
	        	       
	        <span class="fitem">
               <span class="label">Début du Tournois:</span>
               <input class="inputDate"  type="date" name="ts" value="'.$TournamentStart.'" /><br/>
	        </span>
	        	       
	        <span class="fitem">
               <span class="label">Fin du Tournois:</span>
               <input class="inputDate"  type="date" name="te" value="'.$TournamentEnd.'" /><br/>
	        </span>
	       
	       <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Enregistrer les modifications">
	               <a class="pgeBtn" href="index.php">Annuler/Fermer</a>
	       </span>
	       </form>';
	
echo '	
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

