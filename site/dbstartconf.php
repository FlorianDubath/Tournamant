<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsAdmin']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';
$mysqli= ConnectionFactory::GetConnection(); 
if($_POST && !empty($_POST['mac']) && !empty($_POST['aac'])) {
          $stmt = $mysqli->prepare("INSERT INTO TournamentDoubleSatrt 
                                         (MainAgeCategoryId, AcceptedAgeCategoryId) 
                                         VALUES (?,?) ");
                                         
      	  $stmt->bind_param("ii", $_POST['mac'], $_POST['aac']);
      	  
          if (!($stmt->execute())){
             echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
          }
          $stmt->close();
          
          header('Location: ./double_start_conf.php');
          
          
}
if ($_POST && !empty($_POST['id']) && $_POST['del']==1) {
      if (!($stmt = $mysqli->prepare("DELETE FROM TournamentDoubleSatrt WHERE Id=?"))){
      	     echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
      	  } 
      	  
      	  $stmt->bind_param("i", $_POST['id'] );

          if (!($stmt->execute())){
             echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
          }
          
	header('Location: ./double_start_conf.php');
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


echo '	     <span class="h_title">
               AJOUTER UN DOUBLE DEPART
            </span>
            <span class="h_txt">
                 <span class="btnBar"> 
                       <a class="pgeBtn" href="double_start_conf.php" title="Annuler" >Annuler</a>
                 </span>
	      <form action="./dbstartconf.php" method="post">
	      ';
	           $stmt2 = $mysqli->prepare("SELECT TournamentAgeCategory.Id,
	                                            TournamentAgeCategory.Name,
	                                            TournamentAgeCategory.ShortName,
	                                            TournamentGender.Name
	                                     FROM TournamentAgeCategory  
	                                     INNER JOIN TournamentGender on TournamentGender.Id = GenderId
	                                     LEFT OUTER JOIN TournamentWeighting on TournamentAgeCategory.Id = AgeCategoryId
	                                     WHERE AgeCategoryId IS NOT NULL 
	                                     ORDER BY MaxAge, GenderId ");
	             $stmt2->execute();
	             $stmt2->bind_result($ccId,$cccatName,$cccatShortName,$ccgender);
	            echo '<span class="fitem">
	                 <span class="label">Catégorie d\'âge du participant :</span>
	                 <select name="mac">';
	             $list='';
                 while($stmt2->fetch()) {
                     $list=$list.  '<option value="'.$ccId.'">'.$cccatName.'('.$cccatShortName.'/'.$ccgender.')</option>';
                 }
	             $stmt2->close();
	             echo $list;
	        
	        echo'</select>
	        </span>';
	        
	        echo '<span class="fitem">
	                 <span class="label">Double départ possible dans la catégorie :</span>
	                 <select name="aac">'. $list.'</select>
	        </span>
	        
	      
	       <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Enregistrer">
	               <a class="pgeBtn" href="./double_start_conf.php">Annuler</a>
	       </span>
	       </form>';
	
echo '	
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

