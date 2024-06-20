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

echo'
<body>
    <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 
    
writeBand();

         $mysqli= ConnectionFactory::GetConnection(); 
	     if (!($stmt = $mysqli->prepare("SELECT TournamentWeighting.AgeCategoryId,
	                                            TournamentAgeCategory.Name,
	                                            TournamentAgeCategory.ShortName,
	                                            TournamentGender.Name,
	                                            TournamentAgeCategory.MinAge,
	                                            TournamentAgeCategory.MaxAge,
	                                            TournamentAgeCategory.Duration,
	                                            WeightCategoryBasedOnAttendence,
	                                            WeightingBegin,
	                                            WeightingEnd
	                                            
	                                     FROM TournamentWeighting 
	                                     INNER JOIN TournamentAgeCategory on TournamentAgeCategory.Id = AgeCategoryId
	                                     INNER JOIN TournamentGender on TournamentGender.Id = GenderId
	                                     
	                                     order by  WeightingEnd, MaxAge, GenderId "))){
      	     echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
      	  }
          if (!($stmt->execute())){
             echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
          }

          $stmt->bind_result( $Id,$catName,$catShortName,$gender,$mina, $maxa, $duration,$adaptWeight, $RegistrationBegin,$RegistrationEnd);
          
          echo '            <span class="h_title">
               GESTION DES CATEGORIES / HEURES DE PESEE
            </span>
            <span class="h_txt">
                 <span class="btnBar"> 
                 
                       <a class="pgeBtn" href="index.php" title="Fermer" >Fermer</a> 
	               <a class="pgeBtn" href="configcat.php?id=-1">Ajouter</a>
	          </span>
	               <table class="wt t4">
          <tr class="tblHeader"><th>Nom</th><th>Genre</th><th>Ages</th><th>Durée</th><th>Catégories adaptées</th><th>Début Pesée</th><th>Fin Pesée</th><th>Action</th></tr>';
          
          
         while( $stmt->fetch()){
         echo'  <tr><td>'.$catName.' ('.$catShortName.')</td><td>'.$gender.'</td><td>'. (date("Y", strtotime($RegistrationBegin))-$maxa).'-'.(date("Y", strtotime($RegistrationBegin))-$mina).' ('.$mina.'-'.$maxa.' ans)</td><td>'.$duration.' min</td><td>'.$adaptWeight.'</td><td>'.$RegistrationBegin.'</td><td>'.$RegistrationEnd.'</td><td><a class="grdBtn" href="configcat.php?id='.$Id.'">Modifier</a>
          <form action="./configcat.php" method="post">
             <input type="hidden" name="id" value="'.$Id.'"/>
             <input type="hidden" name="del" value="1"/>
             <input class="pgeBtn" type="submit" value="Supprimer"/> 
         </form>
         </td></tr>';
         }
         
         echo '</table>
           
	       <span class="btnBar"> 
	               <a class="pgeBtn" href="index.php">Fermer</a>
	       </span>';
	      $stmt->close();
	
echo '	
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

