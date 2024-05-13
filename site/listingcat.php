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
               
      <table class="wt t4">
      <tr class="tblHeader">
      <th>Nom</th>
      <th>Fin de la pesée</th>
      <th>Participants(déjà pesé)</th>
      <th>Combats commencés</th>
      <th >Action</th>
      </tr>';
      $mysqli= ConnectionFactory::GetConnection(); 
     $stmt = $mysqli->prepare("select
                                 V1.CategoryId, 
                                 TournamentAgeCategory.Name,
                                 TournamentAgeCategory.ShortName,
                                 TournamentGender.Name,
                                 IFNULL(-TournamentCategory.MaxWeight, IFNULL(TournamentCategory.MinWeight,'OPEN')),
                                 V1.Started,
                                 V1.WeightingEnd, 
                                 count(V2.CompetitorId), 
                                 count(V3.CompetitorId) 
                             from V_Category V1 
                             INNER JOIN TournamentCategory ON TournamentCategory.Id=V1.CategoryId
                             INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id=TournamentCategory.AgeCategoryId
                             INNER JOIN TournamentGender on TournamentGender.Id=TournamentAgeCategory.GenderId
                             INNER JOIN V_Category V2 on V1.CategoryId = V2.CategoryId
                             INNER JOIN V_Category V3 on V1.CategoryId = V3.CategoryId  AND V3.WeightChecked=1 
                             ORDER BY V1.WeightingEnd, TournamentAgeCategory.GenderId, IFNULL(MaxWeight,1000)");
     $stmt->bind_result( $catId, $cat_n,$cat_sn,$cat_gen,$weight, $Started, $weighting_end, $total, $weighted);
     $stmt->execute();
     
     while ($stmt->fetch()){
     
        $w_end = new DateTime($weighting_end);
        $now = new DateTime();
        $interval_end = $now->diff($w_end);
     
     
         echo '<tr><td>'.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.'</td>
                   <td>';
            
         if ($now > $w_end) {
            echo 'Pesée terminée';
         } else {
            echo date('H\hi', strtotime($weighting_end)). ' ('.$interval_end->h.'h '.$interval_end->m.' min.)';
         }
                   
                   
         echo '</td>
                   <td>'.$total.' ('.$weighted.')</td>
                   <td>'.$Started.'</td>
                   <td><a href="cat.php?cid='.$catId.'&m=1">A peser</a><a href="cat.php?cid='.$catId.'">Détails</a></td></tr>';
     }
     
     $stmt->close();
     echo '</table>
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
