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
    if (!empty($_POST['del'])) {
        if ($_POST['del']==1) {
             $stmt = $mysqli->prepare("DELETE FROM TournamentWeighting WHERE AgeCategoryId=?");
             $stmt->bind_param("i", $_POST['id'] );
             $stmt->execute();
             $stmt->close();
	         header('Location: ./cat_config.php');
        }
    } else {
          $stmt = $mysqli->prepare("INSERT INTO TournamentWeighting 
                                         (AgeCategoryId, WeightCategoryBasedOnAttendence, WeightingBegin, WeightingEnd) 
                                         VALUES (?,?,?,?) 
                                         ON DUPLICATE KEY UPDATE
                                            WeightCategoryBasedOnAttendence = ?,
                                            WeightingBegin = ?,
                                            WeightingEnd = ?");
      	  
      	  $stmt->bind_param("iississ", $_POST['id'], $_POST['pp'], $_POST['wb'], $_POST['we'], $_POST['pp'], $_POST['wb'], $_POST['we'] );
      	  
          $stmt->execute();
          $stmt->close();
	      $message='Modifications enregistrées';
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
         
writeBand();

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
	                                     WHERE AgeCategoryId=?"))){
      	     echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
      	  }
      	  
      	  $stmt->bind_param("i", $_REQUEST['id'] );
      	  
      	  
          if (!($stmt->execute())){
             echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
          }

          $stmt->bind_result($Id,$catName,$catShortName,$gender,$mina, $maxa, $duration,$adaptWeight, $RegistrationBegin,$RegistrationEnd);
          $stmt->fetch();
	      $stmt->close();
echo '	      
           <span class="h_title">
               HEURE DE PESEE POUR LA CATEGORIE D\'AGE
            </span>
            <span class="h_txt">
                 <span class="btnBar"> 
                       <a class="pgeBtn" href="cat_config.php" title="Annuler/Fermer" >Annuler/Fermer</a>
                 </span>

	      <form action="./configcat.php" method="post">';
	       if ($message!='') {echo'<span class="fmessage">'.$message.'</span>';}
	       if ($Id && $Id==$_REQUEST['id']) {
	       echo'
	       <input type="hidden" name="id" value="'.$Id.'"/>
	       <span class="fitem">
	           <span class="label">Catégories :</span>
                   <span class="label" style="font-weight:bold;"">'.$catName.'('.$catShortName.'/'.$gender.')</span><br/>
	        </span>';
	       } else {
	           
	  
	                 
	             $stmt2 = $mysqli->prepare("SELECT TournamentAgeCategory.Id,
	                                            TournamentAgeCategory.Name,
	                                            TournamentAgeCategory.ShortName,
	                                            TournamentGender.Name
	                                     FROM TournamentAgeCategory  
	                                     INNER JOIN TournamentGender on TournamentGender.Id = GenderId
	                                     LEFT OUTER JOIN TournamentWeighting on TournamentAgeCategory.Id = AgeCategoryId
	                                     WHERE AgeCategoryId IS NULL 
	                                     ORDER BY MaxAge, GenderId ");
	             $stmt2->execute();
	             $stmt2->bind_result($ccId,$cccatName,$cccatShortName,$ccgender);
	            echo '<span class="fitem">
	                 <span class="label">Catégories</span>
	                 <select name="id">';
                 while($stmt2->fetch()) {
                     echo  '<option value="'.$ccId.'">'.$cccatName.'('.$cccatShortName.'/'.$ccgender.')</option>';
                 }
	             $stmt2->close();
	        
	        
	        echo'</select><br/>
	        </span>';
	        
	        
	         $stmt3 = $mysqli->prepare("SELECT TournamentStart FROM TournamentVenue order by Id desc limit 1");
	         $stmt3->execute();
	         $stmt3->bind_result($td);
	         $stmt3->fetch();
	         $stmt3->close();
	         $RegistrationBegin = $td;
	         $RegistrationEnd = $td;
	       
	       }
	       echo' <span class="fitem">
               <span class="label">Catégorie en fonction de la participation :</span>
               <select name="pp">
                  <option value="0">Non</option>
                  <option value="1"';
                  if ($adaptWeight) echo " selected ";
                  echo'>Oui</option>
                </select><br/>
	       </span>
	         <input type="hidden" id="timezone" name="timezone" value="-01:00" />  
	        <span class="fitem">
               <span class="label">Pesée depuis :</span>
               <input class="inputDate"  type="datetime-local" name="wb" value="'.$RegistrationBegin.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Pesée jusqu\'à :</span>
               <input class="inputDate"  type="datetime-local" name="we" value="'.$RegistrationEnd.'" /><br/>
	        </span>
	       <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Enregistrer les modifications">
	               <a class="pgeBtn" href="cat_config.php">Annuler/Fermer</a>
	       </span>
	       </form>';
	
echo '	
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

