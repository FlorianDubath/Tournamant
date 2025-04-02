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
  
writeBand();       
         

echo ' 
            <span class="h_title">
               CATEGORIES
            </span>
             <span class="btnBar"> 
                   <a class="pgeBtn" href="index.php" title="Fermer" >Fermer</a>
               </span>
            <span class="h_txt">
               
   ';
      $mysqli= ConnectionFactory::GetConnection(); 
     $stmt = $mysqli->prepare("select distinct 
                                 TournamentCategory.Id, 
                                 TournamentAgeCategory.Name,
                                 TournamentAgeCategory.ShortName,
                                 TournamentGender.Name,
                                 IFNULL(-TournamentCategory.MaxWeight, IFNULL(TournamentCategory.MinWeight,'OPEN')),
                                 ActualCategory.Id,
                                 ActualCategory.IsCompleted,
                                 V_HMD_ActualCategory.HMD,
                                 TournamentWeighting.WeightingEnd, 
                                 count(DISTINCT V2.CompetitorId), 
                                 count(DISTINCT V3.CompetitorId) 
                             from TournamentCategory
                             INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id=TournamentCategory.AgeCategoryId
                             INNER JOIN TournamentGender on TournamentGender.Id=TournamentAgeCategory.GenderId
                             INNER JOIN TournamentWeighting on TournamentAgeCategory.Id = TournamentWeighting.AgeCategoryId
                             LEFT OUTER JOIN V_Category V2 on TournamentCategory.id = V2.CategoryId
                             LEFT OUTER JOIN V_Category V3 on TournamentCategory.Id = V3.CategoryId  AND V3.WeightChecked=1
                             LEFT OUTER JOIN ActualCategory ON TournamentCategory.Id=ActualCategory.CategoryId 
                             LEFT OUTER JOIN V_HMD_ActualCategory ON V_HMD_ActualCategory.ActualCategoryId=ActualCategory.Id 
                             
                             GROUP BY TournamentCategory.Id, 
                                 TournamentAgeCategory.Name,
                                 TournamentAgeCategory.ShortName,
                                 TournamentGender.Name,
                                 TournamentCategory.MinWeight,
                                 TournamentCategory.MaxWeight,                                
                                 ActualCategory.Id,
                                 ActualCategory.IsCompleted,
                                 V_HMD_ActualCategory.HMD,
                                 TournamentWeighting.WeightingEnd
                                 ORDER bY TournamentWeighting.WeightingEnd, TournamentAgeCategory.MinAge ASC, GenderId ASC, IFNULL(MaxWeight, 100+MinWeight) ASC;
                           ");
     $stmt->bind_result( $catId, $cat_n,$cat_sn,$cat_gen,$weight, $a_cat_id,$a_cat_comp, $hmd, $weighting_end, $total, $weighted);
     $stmt->execute();
     
     
     $current_end_time ='';
     $current_age_cat_n ='';
     while ($stmt->fetch()){
        if ( $current_end_time!=$weighting_end){
           if ( $current_end_time!=''){
               echo '</table></div></div>';
           } 
               $w_end = new DateTime($weighting_end, new DateTimeZone('Europe/Zurich'));
               $now = new DateTime("now",new DateTimeZone('Europe/Zurich'));
               $interval_end = $now->diff($w_end);
              
               echo ' <div class="wgt_tm_grp"> <span class="wgt_tm_grp_ttl">';
               if ($now > $w_end) {
                    echo 'Pesée terminée ('.date('j/m H\hi', strtotime($weighting_end)).')';
               } else {
                    echo date('j/m H\hi', strtotime($weighting_end)). ' ('.$interval_end->h.'h '.$interval_end->i.' min.)';
               }    
               echo'</span>';
            
        }
      
        if ($current_age_cat_n!=$cat_sn.$cat_gen ){
               if ($current_age_cat_n!='' && $current_end_time==$weighting_end){
                   echo '</table></div>';
               }
          
               echo '<div class="wgt_tm_cat" ><span>'.$cat_sn.' '.$cat_n.' '.$cat_gen.'</span>
             <table class="wt t4">
               <tr class="tblHeader">
               <th>Poid</th>
               <th>Participants(déjà pesé)</th>
               <th>Status</th>
               <th >Action</th>
               </tr>';
           }
     
         $current_end_time = $weighting_end; 
         $current_age_cat_n=$cat_sn.$cat_gen; 
         echo '<tr><td>'.$weight.'</td>
                   <td>'.$total.' ('.$weighted.')</td>
                   <td>';
          
         if ($a_cat_comp and $a_cat_comp==1){
             echo 'Terminée';
         } else if ($a_cat_id and $a_cat_id>0){
            if ($hmd>0){
               echo '<span title="Au moin 1 compétiteur à reçu un Hansoku-Make Direct">&#x26A0;</span>&nbsp;';
            }
             echo 'En cours';
         }  else if ($now > $w_end && $_SESSION['_IsMainTable']==1 ) {
            if ($weighted>0) {
                 echo' <a href="acat.php?cid='.$catId.'">Préparer</a>';
            } else {
                 echo '--';
            }
         } else {
             echo 'En attente';
         }       
                   
         echo'</td>
                   <td>';
         if ($total>0) {
             if (empty($a_cat_id)){
                   echo'<a href="cat.php?cid='.$catId.'&m=1">A peser</a>';
               }
               echo'
               <a href="cat.php?cid='.$catId.'">Détails</a></td></tr>';
        }
     }
     
     $stmt->close();
     echo '</table></div></div>
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
