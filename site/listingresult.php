<?php

ob_start();
session_name("Tournament");	
session_start();

if (empty($_SESSION['_UserId'])) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';

include '_commonBlock.php';


writeHead();
$mysqli= ConnectionFactory::GetConnection(); 


echo'
<body>
    <div class="f_cont">';

echo'        
       <div class="cont_l">
         <div class="h">'; 
     
writeBand();    
         
echo ' 
            <span class="h_title">
               RESULTATS
            </span>
             <span class="btnBar"> 
                   <a class="pgeBtn" href="index.php" title="Fermer" >Fermer</a>
                   <a class="pgeBtn" href="results.php" title="PDF Catégories" >PDF Catégories</a>
                   <a class="pgeBtn" href="resultsclub.php" title="PDF Clubs" >PDF Clubs</a>
               </span>
           
     ';
     $stmt = $mysqli->prepare("select
                                 ActualCategory.Id,
                                 ActualCategory.Name,
                                 ActualCategoryResult.RankId,
                                 ActualCategoryResult.Medal,
                                 TournamentCompetitor.Name,
                                 TournamentCompetitor.Surname,
                                 TournamentClub.Name
                             from ActualCategoryResult
                             INNER JOIN ActualCategory ON ActualCategory.Id=ActualCategoryResult.ActualCategoryId
                             INNER JOIN TournamentCompetitor ON TournamentCompetitor.Id = ActualCategoryResult.Competitor1Id
                             INNER JOIN TournamentClub on TournamentClub.Id=TournamentCompetitor.ClubId
                             INNER JOIN TournamentCategory ON TournamentCategory.Id = ActualCategory.CategoryId
                             INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId
                             INNER JOIN TournamentWeighting ON TournamentWeighting.AgeCategoryId = TournamentAgeCategory.Id
                             ORDER bY TournamentWeighting.WeightingEnd, TournamentAgeCategory.MinAge ASC, TournamentAgeCategory.GenderId ASC, IFNULL(MaxWeight, 100+MinWeight) ASC, ActualCategory.Id, ActualCategoryResult.RankId ASC;
                           ");
     $stmt->bind_result( $acat_id, $agcat_name,$rank,$medal,$name,$surname,$club);
     $stmt->execute();
     
    
     $current_cat ='';
     while ($stmt->fetch()){
         
         if ( $current_cat!=$agcat_name){
               if ( $current_cat!=''){
                   echo '</table></div>';
               } 
              
              
               echo ' <div class="wgt_tm_grp"> <span class="wgt_tm_grp_ttl">'.$agcat_name.'</span><a class="pgeBtn" href="results.php?acid='.$acat_id.'" title="PDF" >PDF</a>';
               
                 echo '
             <table class="wt t4">
               <tr class="tblHeader">
               <th>Classement</th>
               <th>Participant</th>
               <th>Club</th>
               </tr>';
            
        }
        
        echo ' <tr class="result_'.$medal.'">
          <td>'.$medal_char[$medal].$rank.'</td>
      <td>'.$surname.' '.$name.'</td>
      <td>'. $club.'</td>
      </tr>';
        
         $current_cat = $agcat_name; 
     }
     
     echo '</table></div>';
     
     $stmt->close();
     echo '
              </span>
              
              <span class="btnBar"> 
                   <a class="pgeBtn" href="index.php" title="Fermer" >Fermer</a>
              </span>
           </div>     
        </div>   
     </div>
</body>
</html>';
?>
