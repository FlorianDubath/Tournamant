<?php

ob_start();
session_name("Tournament");	
session_start();

if (empty($_SESSION['_UserId'])) {
	header('Location: ./index.php');
}

if(empty($_GET['cid']) ) {
      	header('Location: ./index.php');
}

$missing=0;
if (!empty($_GET['m'])) {
      	$missing=(int)$_GET['m'];
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
                             WHERE V1.CategoryId=?");
                             
     $stmt->bind_param("i", $_GET['cid'] );
     $stmt->bind_result( $catId, $cat_n,$cat_sn,$cat_gen,$weight, $Started, $weighting_end, $total, $weighted);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close();         
         
         
         

echo ' 
            <span class="h_title">
               '.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.'
            </span>
            <span class="h_txt">
                  <span class="btnBar"> ';
       if ($missing==-1) {
          echo ' Participants pesés ('.$weighted.')
          <a class="pgeBtn"  href="cat.php?cid='.$_GET['cid'].'" title="Tous les participants">Tous les participants ('.$total.')</a>
                 <a class="pgeBtn"  href="cat.php?cid='.$_GET['cid'].'&m=1" title="Participants à peser/manquants">Participants à peser/manquants ('.$total - $weighted.')</a>';
       } else if ($missing==1) {
                 echo ' Participants à peser/manquants ('.$total - $weighted.')
                 <a class="pgeBtn"  href="cat.php?cid='.$_GET['cid'].'" title="Tous les participants">Tous les participants ('.$total.')</a>
                 <a class="pgeBtn"  href="cat.php?cid='.$_GET['cid'].'&m=-1" title="Participants pesés">Participants pesés ('.$weighted.')</a>';
       }  else {
                 echo ' 
                 Tous les participants ('.$total.')
                 <a class="pgeBtn"  href="cat.php?cid='.$_GET['cid'].'&m=-1" title="Participants pesés">Participants pesés ('.$weighted.')</a>
                 <a class="pgeBtn"  href="cat.php?cid='.$_GET['cid'].'&m=1" title="Participants à peser/manquants">Participants à peser/manquants ('.$total - $weighted.')</a>';
       }                    
                  
	              
echo'
	             </span>
      <table class="wt t4">
      <tr class="tblHeader">
      <th>Nom, Prénom</th>
      <th>Date Nais.</th>
      <th>Club</th>
      <th>Ceinture</th>
      <th>Licence</th>
      <th>Présent</th>
      <th>Pesé</th>
      </tr>';
      $mysqli= ConnectionFactory::GetConnection(); 
      $query="select
                                 TournamentCompetitor.Surname,
                                 TournamentCompetitor.Name,  
                                 TournamentCompetitor.Birth , 
                                 TournamentClub.Name, 
                                 TournamentGrade.Name,
                                 TournamentCompetitor.LicenceNumber,
                                 TournamentRegistration.WeightChecked,
                                 TournamentCompetitor.CheckedIn
                             FROM TournamentCompetitor
                             INNER JOIN TournamentRegistration ON TournamentRegistration.CompetitorId = TournamentCompetitor.Id
                               INNER JOIN TournamentGrade ON GradeId=TournamentGrade.Id
                               INNER JOIN TournamentClub ON ClubId=TournamentClub.Id
                             WHERE  TournamentRegistration.CategoryId=?";
      if ($missing!=0) {
          $query=$query." AND TournamentRegistration.WeightChecked=".((-$missing+1)/2)." ";
      }                     
      $query=$query." ORDER BY TournamentCompetitor.CheckedIn desc, TournamentRegistration.WeightChecked DESC,TournamentCompetitor.Surname, TournamentCompetitor.Name";
      
      
      $stmt = $mysqli->prepare($query);
     $stmt->bind_param("i", $_GET['cid'] );
     $stmt->bind_result( $Surname, $Name, $Birth,  $Club, $Grade, $licence, $chw, $chin);
     $stmt->execute();
     
     while ($stmt->fetch()){
          echo ' <tr class="tblHeader">
      <td>'.$Surname.' '.$Name.'</td>
      <td><input class="inputDate"  type="date"  value="'.$Birth.'" readonly="readonly"/></td>
      <td>'. $Club.'</td>
      <td>'.$Grade.'</td>
      <td>'. $licence.'</td>
      <td>'.$chin.'</td>
      <td>'.$chw.'</td>
      </tr>';

     }
     
     $stmt->close();
     echo '</table>
              </span>
              
              <span class="h_txt"> 
                <span class="btnBar"> 
                   <a class="pgeBtn" href="listingcat.php" title="Fermer" >Fermer</a>
               </span>
           </span>    
           </div>     
        </div>   
     </div>
</body>
</html>';
?>
