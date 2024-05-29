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


$is_table =  $_SESSION['_IsMainTable'] ==1 ||  $_SESSION['_IsMatTable'] ==1;


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
                                 TournamentCategory.Id, 
                                 TournamentAgeCategory.Name,
                                 TournamentAgeCategory.ShortName,
                                 TournamentAgeCategory.Duration,
                                 TournamentGender.Name,
                                 IFNULL(-TournamentCategory.MaxWeight, IFNULL(TournamentCategory.MinWeight,'OPEN')),
                                 TournamentWeighting.WeightingEnd, 
                                 count(DISTINCT V2.CompetitorId), 
                                 count(DISTINCT V3.CompetitorId) 
                             from TournamentCategory
                             INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id=TournamentCategory.AgeCategoryId
                             INNER JOIN TournamentGender on TournamentGender.Id=TournamentAgeCategory.GenderId
                             INNER JOIN TournamentWeighting on TournamentAgeCategory.Id = TournamentWeighting.AgeCategoryId
                             LEFT OUTER JOIN V_Category V2 on TournamentCategory.id = V2.CategoryId
                             LEFT OUTER JOIN V_Category V3 on TournamentCategory.Id = V3.CategoryId  AND V3.WeightChecked=1

                             WHERE TournamentCategory.Id=?
                             
                             GROUP BY TournamentCategory.Id, 
                                 TournamentAgeCategory.Name,
                                 TournamentAgeCategory.ShortName,
                                 TournamentGender.Name,
                                 IFNULL(-TournamentCategory.MaxWeight, IFNULL(TournamentCategory.MinWeight,'OPEN')),
                                 TournamentWeighting.WeightingEnd;
                           ");
                             
     $stmt->bind_param("i", $_GET['cid'] );
     $stmt->bind_result( $catId, $cat_n,$cat_sn,$cat_dur,$cat_gen,$weight, $weighting_end, $total, $weighted);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close();         
         
     $stmt = $mysqli->prepare("select
                                 Id,
                                 Name,
                                 CategoryId,
                                 Category2Id,
                                 IsCompleted
                             from ActualCategory
                             WHERE CategoryId=? OR Category2Id=?
                           ");
                             
     $stmt->bind_param("ii", $_GET['cid'],$_GET['cid'] );
     $stmt->bind_result( $actual_cat_Id, $ac_name, $cccid_1, $cccid_2, $cat_completed);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close();  
     
     if (empty($actual_cat_Id)) {
         

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
      <th>Nom Prénom</th>
      <th>Date Nais.</th>
      <th>Club</th>
      <th>Ceinture</th>
      <th>Licence</th>
      <th>Présent</th>
      <th>Pesé</th>
      <th>Acion</th>
      </tr>';
      $mysqli= ConnectionFactory::GetConnection(); 
      $query="select
                                 TournamentCompetitor.StrId,
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
     $stmt->bind_result( $strId, $Surname, $Name, $Birth,  $Club, $Grade, $licence, $chw, $chin);
     $stmt->execute();
     
     while ($stmt->fetch()){
     $date='';
      if (isset($Birth)){
       $d1=new DateTime($Birth);
       $date=$d1->format('d/m/Y');
     }
     
          echo ' <tr class="tblHeader">
      <td>'.$Surname.' '.$Name.'</td>
      <td>'. $date.'</td>
      <td>'. $Club.'</td>
      <td>'.$Grade.'</td>
      <td>'. $licence.'</td>
      <td>'.$chin.'</td>
      <td>'.$chw.'</td>
      <td><a href="card.php?sid='.$strId.'" target="_blancK">Carte</a></td>
      </tr>';

     }
     
     $stmt->close();
     echo '</table>
              </span>
              
              <span class="h_txt"> ';
   } else  {  //check if a Actual category exist
        echo'<span class="h_title">';
        if (! empty($cccid_2)){ //  check if merged with another cat
            echo'La catégorie est mélangée dans : '; 
        }
        echo $ac_name.'</span>
             <span class="h_txt"> <span class="btnBar"> Participants</span></span>
             
              <table class="wt t4">
      <tr class="tblHeader">
      <th>Nom Prénom</th>
      <th>Date Nais.</th>
      <th>Club</th>
      <th>Ceinture</th>
      <th>Licence</th>
      <th></th>
      </tr>';

      $stmt = $mysqli->prepare("select
                                 TournamentCompetitor.StrId,
                                 TournamentCompetitor.Surname,
                                 TournamentCompetitor.Name,  
                                 TournamentCompetitor.Birth, 
                                 TournamentClub.Name, 
                                 TournamentGrade.Name,
                                 TournamentCompetitor.LicenceNumber
                             FROM TournamentCompetitor
                             INNER JOIN TournamentRegistration ON TournamentRegistration.CompetitorId = TournamentCompetitor.Id and WeightChecked=1
                             INNER JOIN TournamentGrade ON GradeId=TournamentGrade.Id
                             INNER JOIN TournamentClub ON ClubId=TournamentClub.Id
                             INNER JOIN ActualCategory ON ActualCategory.CategoryId = TournamentRegistration.CategoryId OR ActualCategory.Category2Id = TournamentRegistration.CategoryId
                             WHERE  ActualCategory.Id=?");
     $stmt->bind_param("i", $actual_cat_Id );
     $stmt->bind_result( $strId, $Surname, $Name, $Birth,  $Club, $Grade, $licence);
     $stmt->execute();
     
     while ($stmt->fetch()){
     $date='';
      if (isset($Birth)){
       $d1=new DateTime($Birth);
       $date=$d1->format('d/m/Y');
     }
          echo ' <tr >
      <td>'.$Surname.' '.$Name.'</td>
      <td>'.$date.'</td>
      <td>'. $Club.'</td>
      <td>'.$Grade.'</td>
      <td>'. $licence.'</td>
      <td><a href="card.php?sid='.$strId.'" target="_blancK">Carte</a></td>
      </tr>';

     }
     
     $stmt->close();
     echo '</table>';
             
     
     
     echo ' <span class="h_txt"> <span class="btnBar"> Combats (Durée :'.$cat_dur.'min)</span></span>
     
          <table class="wt t4">
      <tr class="tblHeader">
      <th>PV</th>
      <th>Rouge</th>
      <th></th>
      <th>Blanc</th>
      <th>PV</th>
      </tr>';

     
      $stmt = $mysqli->prepare("select
                                 Fight.Id,
                                 Fight.pv1,
                                 Fight.pv2,
                                 TC1.Surname,
                                 TC1.Name, 
                                 TC2.Surname,
                                 TC2.Name
                                 
                             FROM Fight
                             LEFT OUTER JOIN TournamentCompetitor as TC1 on TC1.Id = TournamentCompetitor1Id
                             LEFT OUTER JOIN TournamentCompetitor as TC2 on TC2.Id = TournamentCompetitor2Id
                             WHERE  Fight.ActualCategoryId=? order by Fight.Id ASC");
     $stmt->bind_param("i", $actual_cat_Id );
     $stmt->bind_result( $f_id, $pv1, $pv2, $Surname1, $Name1, $Surname2, $Name2);
     $stmt->execute();
     
     $pop_counter=1;
     while ($stmt->fetch()){
         if (empty($Surname1) || empty($Surname2)) {
           echo ' <tr >
                  <td></td>
                  <td> colspan="3"A venir...</td>
                  <td></td>
                  </tr>';
         } else if (empty($pv1)){
          echo ' <tr >
                  <td>';
                  if($is_table){
                  echo'
                  
                  <span class="pop_back pop_hide" Id="pop_1_'.$pop_counter.'"><span class="popcont">
                     Victoire de '.$Surname1.' '.$Name1.' (Rouge) par:
                   <form action="figtRes.php" method="post">
                             <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                             <input type="hidden" name="fid" value="'.$f_id.'" />
                             <input type="hidden" name="pv1" value="10" />
                             <input type="hidden" name="pv2" value="0" />
                             <input type="hidden" name="cid" value="'.$catId.'" />
                             <input type="submit" value="Ippon">
                   </form>
                   
                   <form action="figtRes.php" method="post">
                             <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                             <input type="hidden" name="fid" value="'.$f_id.'" />
                             <input type="hidden" name="pv1" value="7" />
                             <input type="hidden" name="pv2" value="0" />
                             <input type="hidden" name="cid" value="'.$catId.'" />
                             <input type="submit" value="Waza-ari ">
                   </form>
                   
                   <form action="figtRes.php" method="post">
                             <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                             <input type="hidden" name="fid" value="'.$f_id.'" />
                             <input type="hidden" name="pv1" value="1" />
                             <input type="hidden" name="pv2" value="0" />
                             <input type="hidden" name="cid" value="'.$catId.'" />
                             <input type="submit" value="Décision">
                   </form>
                   
                   <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_1_'.$pop_counter.'\'),\'pop_hide\');">Annuler</a>
                  
                  </span></span>
                  <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_1_'.$pop_counter.'\'),\'pop_hide\');">Victoire</a>
                  ';
                  }
                  echo'
                 </td>
                  <td>'.$Surname1.' '.$Name1.'</td>
                  <td>V.S.</td>
                  <td>'.$Surname2.' '.$Name2.'</td>
                  <td> ';
                  if($is_table){
                  echo'
                  <span class="pop_back pop_hide" Id="pop_2_'.$pop_counter.'"><span class="popcont">
                     Victoire de '.$Surname2.' '.$Name2.' (Blanc) par:
                   <form action="figtRes.php" method="post">
                             <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                             <input type="hidden" name="fid" value="'.$f_id.'" />
                             <input type="hidden" name="pv1" value="0" />
                             <input type="hidden" name="pv2" value="10" />
                             <input type="hidden" name="cid" value="'.$catId.'" />
                             <input type="submit" value="Ippon">
                   </form>
                   
                   <form action="figtRes.php" method="post">
                             <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                             <input type="hidden" name="fid" value="'.$f_id.'" />
                             <input type="hidden" name="pv1" value="0" />
                             <input type="hidden" name="pv2" value="7" />
                             <input type="hidden" name="cid" value="'.$catId.'" />
                             <input type="submit" value="Waza-ari ">
                   </form>
                   
                   <form action="figtRes.php" method="post">
                             <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                             <input type="hidden" name="fid" value="'.$f_id.'" />
                             <input type="hidden" name="pv1" value="0" />
                             <input type="hidden" name="pv2" value="1" />
                             <input type="hidden" name="cid" value="'.$catId.'" />
                             <input type="submit" value="Décision">
                   </form>
                   <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_2_'.$pop_counter.'\'),\'pop_hide\');">Annuler</a>
                  
                  </span></span>
                  <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_2_'.$pop_counter.'\'),\'pop_hide\');">Victoire</a></td>';
                  }
                  echo'</td>
                  </tr>';
                  $pop_counter+=1;
         } else { 
           $cl_1="VIC";
           $cl_2="LOS";
           if ($pv2>0){
              $cl_1="LOS";
              $cl_2="VIC";
           }
           echo ' <tr > 
                  <td class="'.$cl_1.'">'.$pv1.'</td>
                  <td class="'.$cl_1.'">'.$Surname1.' '.$Name1.'</td>
                  <td>V.S.</td>
                  <td class="'.$cl_2.'">'.$Surname2.' '.$Name2.'</td>
                  <td class="'.$cl_2.'">'.$pv2.'</td>
                  </tr>';
         }
            
    
         

     }
     
     $stmt->close();
     echo '</table>';
     
       
             
      if ($cat_completed==1){
          echo'<span class="h_title">
               Résultats
               </span>
               
                 <table class="wt t4">
      <tr class="tblHeader">
      <th>Classement</th>
      <th>Nom Prénom</th>
      <th>Club</th>
      </tr>';
      $mysqli= ConnectionFactory::GetConnection(); 
      
      
      $stmt = $mysqli->prepare("select
                                 RankId,
                                 TournamentCompetitor.Surname,
                                 TournamentCompetitor.Name,  
                                 TournamentClub.Name
                             FROM ActualCategoryResult
                             INNER JOIN TournamentCompetitor on TournamentCompetitor.Id =  Competitor1Id
                             INNER JOIN TournamentClub ON ClubId=TournamentClub.Id
                             WHERE  ActualCategoryId=?");
     $stmt->bind_param("i", $actual_cat_Id );
     $stmt->bind_result( $rk,  $Surname, $Name, $Club);
     $stmt->execute();
     
     while ($stmt->fetch()){
          echo ' <tr class="result_'.$rk.'">
          <td>'.$rk.'</td>
      <td>'.$Surname.' '.$Name.'</td>
      <td>'. $Club.'</td>
      </tr>';

     }
     
     $stmt->close();
     echo '</table>';
               
               
            
      }       
     
     }
              
    
   
   
          
              
     echo'
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
