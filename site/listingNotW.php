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
         
         
echo ' 
            <span class="h_title">
               COMPETITEUR MANQUANTS PAR HEURE DE PESEE
            </span>
             <span class="btnBar"> 
                   <a class="pgeBtn" href="index.php" title="Fermer" >Fermer</a>
               </span>
           
     ';
     $stmt = $mysqli->prepare("select
                                 TournamentCategory.Id, 
                                 TournamentAgeCategory.Name,
                                 TournamentAgeCategory.ShortName,
                                 TournamentGender.Name,
                                 IFNULL(-TournamentCategory.MaxWeight, IFNULL(TournamentCategory.MinWeight,'OPEN')),
                                 TournamentWeighting.WeightingEnd, 
                                 TournamentCompetitor.Id, 
                                 TournamentCompetitor.StrId, 
                                 Surname, 
                                 TournamentCompetitor.Name, 
                                 Birth, 
                                 TournamentClub.Name, 
                                 LicenceNumber, 
                                 IFNULL(Payed,0),
                                 IFNULL(CheckedIn,0),
                                 IFNULL(WeightChecked,0)
                             from TournamentCategory
                             INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id=TournamentCategory.AgeCategoryId
                             INNER JOIN TournamentWeighting on TournamentAgeCategory.Id = TournamentWeighting.AgeCategoryId
                             INNER JOIN TournamentRegistration on TournamentCategory.Id = TournamentRegistration.CategoryId
                             INNER JOIN TournamentCompetitor ON TournamentCompetitor.Id = TournamentRegistration.CompetitorId
                             INNER JOIN TournamentGender on TournamentGender.Id = TournamentAgeCategory.GenderId
                             INNER JOIN TournamentClub ON ClubId=TournamentClub.Id
                             WHERE WeightChecked<>1
                             ORDER bY TournamentWeighting.WeightingEnd, TournamentAgeCategory.MinAge ASC, TournamentAgeCategory.GenderId ASC, IFNULL(MaxWeight, 100+MinWeight) ASC, TournamentClub.Name, Surname;
                           ");
     $stmt->bind_result( $catid, $agcat_name,$agcat_short, $gender, $weight, $weighting_end,  $Id, $strId,$Surname,$Name,$Birth, $Club, $licence, $payed,$present,$weighted);
     $stmt->execute();
     
    
     $current_end_time ='';
     $current_age_cat_n ='';
     while ($stmt->fetch()){
         $bdate='';
         if (isset($Birth)){
           $d1=new DateTime($Birth);
           $bdate=$d1->format('d/m/Y');
         } 
         if ( $current_end_time!=$weighting_end){
               if ( $current_end_time!=''){
                   echo '</table></div>';
               } 
               $w_end = new DateTime($weighting_end);
               $now = new DateTime();
               $interval_end = $now->diff($w_end);
              
               echo ' <div class="wgt_tm_grp"> <span class="wgt_tm_grp_ttl">';
               if ($now > $w_end) {
                    echo 'Pesée terminée ('.date('j/m H\hi', strtotime($weighting_end)).')';
               } else {
                    echo date('j/m H\hi', strtotime($weighting_end)). ' ('.$interval_end->h.'h '.$interval_end->m.' min.)';
               }    
               echo'</span>';
               
                 echo '
             <table class="wt t4">
               <tr class="tblHeader">
               <th>Catégorie</th>
               <th>Participant</th>
               <th>Club</th>
               <th >Payé</th>
               <th >Présent</th>
               <th >Pesé</th>
               </tr>';
            
        }
        
        echo ' <tr >
               <td>'.$agcat_short.' '. $weight.'</td>
               <td>'.$Surname.' '.$Name.'</td>
               <td>'.$Club.'</td>
               <td >'.$payed.'</td>
               <td >'.$present.'</td>
               <td >'.$weighted.'</td>
               </tr>';
        
        
         
         
         $current_end_time = $weighting_end; 
         $current_age_cat_n=$cat_sn.$cat_gen; 
         
         
         
         
         
         
         
         
         
         
         
     }
     
     echo '</table></div>';
     
     $stmt->close();
     echo '
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
