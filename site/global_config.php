<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsAdmin']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';
$mysqli= ConnectionFactory::GetConnection(); 
$message='';
if($_POST && !empty($_POST['id'])) {

          $stmt = $mysqli->prepare("UPDATE TournamentVenue SET Name=?,Place=?,Transport=?,Organization=?, Admition=?, `System`=?,Prize=?, Judge=?,Dressing=?,Contact=?,RegistrationEnd=?,TournamentStart=?,TournamentEnd=? WHERE Id=?");
      	  $stmt->bind_param("sssssssssssssi", $_POST['nme'], $_POST['plc'], $_POST['tsp'], $_POST['org'], $_POST['adm'], $_POST['sys'], $_POST['prz'], $_POST['jdg'], $_POST['drs'], $_POST['ctc'], $_POST['reg'], $_POST['ts'], $_POST['te'] , $_POST['id'] );
          $stmt->execute();
	      $stmt->close();
	      
	      $stmt = $mysqli->prepare("
	      UPDATE TournamentWeighting TW1 
	      INNER JOIN TournamentWeighting TW2 ON TW1.AgeCategoryId = TW2.AgeCategoryId
          SET TW1.WeightingBegin = concat(DATE(?),' ',TIME(TW2.WeightingBegin)), TW1.WeightingEnd = concat(DATE(?),' ',TIME(TW2.WeightingEnd))
	      ");
          $stmt->bind_param("ss", $_POST['ts'], $_POST['ts']);
          $stmt->execute();
	      $stmt->close();
	      $message='Modifications enregistrées';


}
if($_POST && !empty($_POST['del'])) {
    $mysqli->begin_transaction();
    try {
        
        $mysqli->query("DELETE FROM ActualCategoryResult;");
        $mysqli->query("DELETE FROM Fight;");
        $mysqli->query("DELETE FROM StepLinking;");
        $mysqli->query("DELETE FROM CategoryStep;");
        $mysqli->query("DELETE FROM ActualCategory;");
        $mysqli->query("DELETE FROM TournamentRegistration;");
        $mysqli->query("DELETE FROM TournamentCompetitor;");
        $mysqli->commit();
    } catch (mysqli_sql_exception $exception) {
        $mysqli->rollback();
        throw $exception;
    }
    $message='Données précédentes éffacées';


}


include '_commonBlock.php';

writeHead();

echo'
<body>
    <div class="f_cont">';
    
writeBand();

echo'        
       <div class="cont_l">
         <div class="h"> 
         <span class="h_title">
               INFORMATIONS GLOBALES
	</span>
	<span class="h_txt">
	   <span class="btnBar"> 
	       <a class="pgeBtn" href="index.php" title="Annuler/Fermer" >Annuler/Fermer</a>
	   </span>
	   <span class="btnBar"> 
	            <span class="pop_back pop_hide" Id="pop_d">
	               <span class="popcont">
	                   <span class="pop_tt">
		            Vous êtes en train d\'effacer les enregistrements (compétiteurs et résultats) de la dernière édition!<br/>
		            Etes-vous sur ? <br/> 
		            Cette opération ne peut être annulée !
		           </span> 
		            Peut-être voulez-vous sauvegarder encore une fois <br/><a class="pgeBtn" href="results.php?acat=-1" target="_blanck">les résultats</a>? <br/><a class="pgeBtn" href="resultsclub.php?cid=-1" target="_blanck">les résultats par clubs</a>?<br/><br/>
		            
		            
		 	    <span class="btnBar"> 
				    <form action="./global_config.php" method="post">         
				             <input type="hidden" value="1" name="del"/>
				             <input class="pgeBtn" type="submit" value="Effacer les données précédentes">
				             <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_d\'),\'pop_hide\');">Annuler</a>
				   </form>
		          </span>
                     </span>
                  </span>
                  <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_d\'),\'pop_hide\');">Effacer les données précédentes</a></td> 
	  </span>'; 
         
         
         
         

	     if (!($stmt = $mysqli->prepare("SELECT Id,Name,Place,Transport,Organization, Admition, `System`,Prize, Judge,Dressing,Contact,RegistrationEnd,TournamentStart,TournamentEnd FROM TournamentVenue order by Id desc limit 1"))){
      	     echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
      	  }
          if (!($stmt->execute())){
             echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
          }

          $stmt->bind_result( $Id,$Name,$Place,$Transport,$Organization, $Admition, $System,$Prize, $Judge,$Dressing,$Contact,$RegistrationEnd,$TournamentStart,$TournamentEnd);
          $stmt->fetch();
	      $stmt->close();
echo '	      
	      <form action="./global_config.php" method="post">';       
	if ($message!='') {echo'<span class="fmessage">'.$message.'</span>';}
	      echo'  <input type="hidden" name="id" value="'.$Id.'"/>
	        <span class="fitem">
               <span class="label">Nom du tournoi:</span><br/>
                <textarea  name="nme" rows="1"  style="display:block;width:100%;">'.$Name.'</textarea>
	        </span>
	        <span class="fitem">
               <span class="label">Lieu:</span><br/>
                <textarea  name="plc" rows="4"  style="display:block;width:100%;">'.$Place.'</textarea>
	        </span>
	        <span class="fitem">
               <span class="label">Transports:</span><br/>
                <textarea  name="tsp" rows="4"  style="display:block;width:100%;">'.$Transport.'</textarea>
	        </span>
	        <span class="fitem">
               <span class="label">Organisation:</span><br/>
                <textarea  name="org" rows="4"  style="display:block;width:100%;">'.$Organization.'</textarea>
	        </span>
	        <span class="fitem">
               <span class="label">Admission:</span><br/>
                <textarea  name="adm" rows="4"  style="display:block;width:100%;">'.$Admition.'</textarea>
	        </span>
	        <span class="fitem">
               <span class="label">Système:</span><br/>
                <textarea  name="sys" rows="4"  style="display:block;width:100%;">'.$System.'</textarea>
	        </span>
	        <span class="fitem">
               <span class="label">Récompenses:</span><br/>
                <textarea  name="prz" rows="4"  style="display:block;width:100%;">'.$Prize.'</textarea>
	        </span>
	        <span class="fitem">
               <span class="label">Arbitrage:</span><br/>
                <textarea  name="jdg" rows="4"  style="display:block;width:100%;">'.$Judge.'</textarea>
	        </span>
	        <span class="fitem">
               <span class="label">Tenue:</span><br/>
                <textarea  name="drs" rows="4"  style="display:block;width:100%;">'.$Dressing.'</textarea>
	        </span>
	        <span class="fitem">
               <span class="label">Contact:</span><br/>
                <textarea  name="ctc" rows="4"  style="display:block;width:100%;">'.$Contact.'</textarea>
	        </span>
	        <span class="fitem">
               <span class="label">Inscription jusqu\'au:</span>
               <input class="inputDate"  type="date" name="reg" value="'.$RegistrationEnd.'" /><br/>
	        </span>
	        	       
	        <span class="fitem">
               <span class="label">Début du Tournoi:</span>
               <input class="inputDate"  type="date" name="ts" value="'.$TournamentStart.'" /><br/>
	        </span>
	        	       
	        <span class="fitem">
               <span class="label">Fin du Tournoi:</span>
               <input class="inputDate"  type="date" name="te" value="'.$TournamentEnd.'" /><br/>
	        </span><br/>
	       
	       <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Enregistrer les modifications">
	               <a class="pgeBtn" href="index.php">Annuler/Fermer</a>
	       </span>
	       </form>
	     </span>
	       ';
	
echo '	
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

