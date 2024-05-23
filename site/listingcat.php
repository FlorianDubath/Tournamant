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

echo'
<body>
    <div class="f_cont">';

echo'        
       <div class="cont_l">
         <div class="h">'; 
         
         

echo ' 
            <span class="h_title">
               CATEGORIES
            </span>
            <span class="h_txt">
               
   ';
      $mysqli= ConnectionFactory::GetConnection(); 
     $stmt = $mysqli->prepare("select
                                 TournamentCategory.Id, 
                                 TournamentAgeCategory.Name,
                                 TournamentAgeCategory.ShortName,
                                 TournamentGender.Name,
                                 IFNULL(-TournamentCategory.MaxWeight, IFNULL(TournamentCategory.MinWeight,'OPEN')),
                                 TournamentWeighting.Started,
                                 TournamentWeighting.WeightingEnd, 
                                 count(DISTINCT V2.CompetitorId), 
                                 count(DISTINCT V3.CompetitorId) 
                             from TournamentCategory
                             INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id=TournamentCategory.AgeCategoryId
                             INNER JOIN TournamentGender on TournamentGender.Id=TournamentAgeCategory.GenderId
                             INNER JOIN TournamentWeighting on TournamentAgeCategory.Id = TournamentWeighting.AgeCategoryId
                             INNER JOIN V_Category V2 on TournamentCategory.id = V2.CategoryId
                             INNER JOIN V_Category V3 on TournamentCategory.Id = V3.CategoryId  AND V3.WeightChecked=1
                             
                             GROUP BY TournamentCategory.Id, 
                                 TournamentAgeCategory.Name,
                                 TournamentAgeCategory.ShortName,
                                 TournamentGender.Name,
                                 IFNULL(-TournamentCategory.MaxWeight, IFNULL(TournamentCategory.MinWeight,'OPEN')),
                                 TournamentWeighting.Started,
                                 TournamentWeighting.WeightingEnd
                                 ORDER bY TournamentWeighting.WeightingEnd, TournamentAgeCategory.MinAge ASC, GenderId ASC, IFNULL(MaxWeight, 100+MinWeight) ASC;
                           ");
     $stmt->bind_result( $catId, $cat_n,$cat_sn,$cat_gen,$weight, $Started, $weighting_end, $total, $weighted);
     $stmt->execute();
     
     
     $current_end_time ='';
     while ($stmt->fetch()){
     
        if ( $current_end_time!=$weighting_end){
           if ( $current_end_time!=''){
               echo '</table></div>';
           }
               $w_end = new DateTime($current_end_time);
               $now = new DateTime();
               $interval_end = $now->diff($w_end);
              
               echo ' <div class="wgt_tm_grp"> <span class="wgt_tm_grp_ttl">';
               if ($now > $w_end) {
                    echo 'Pesée terminée ('.date('j/m H\hi', strtotime($weighting_end)).')';
               } else {
                    echo date('j/m H\hi', strtotime($weighting_end)). ' ('.$interval_end->h.'h '.$interval_end->m.' min.)';
               }    
           $current_end_time=$weighting_end;
          
          echo'
               <table class="wt t4">
               <tr class="tblHeader">
               <th>Nom</th>
               <th>Participants(déjà pesé)</th>
               <th>Combats commencés</th>
               <th >Action</th>
               </tr>';
        }
     
     
      
     
     
         echo '<tr><td>'.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.'</td>
                   <td>'.$total.' ('.$weighted.')</td>
                   <td>'.$Started.'</td>
                   <td><a href="cat.php?cid='.$catId.'&m=1">A peser</a><a href="cat.php?cid='.$catId.'">Détails</a></td></tr>';
     }
     
     $stmt->close();
     echo '</table></div>
              </span>
              
              <span class="h_txt"> 
                <span class="btnBar"> 
                   <a class="pgeBtn" href="index.php" title="Fermer" >Fermer</a>
               </span>
           </span>    
           </div>     
        </div>   
     </div>
</body>
</html>';
?>
