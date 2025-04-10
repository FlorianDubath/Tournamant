<?php

ob_start();
session_name("Tournament");	
session_start();

$shift=intval($_GET['shift']);

include 'connectionFactory.php';

include '_commonBlock.php';
include '_categoryHelper.php';

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
               CATEGORIES EN COURS
            </span>
             
            <span class="h_txt"> ';
            
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
                             WHERE ActualCategory.IsCompleted<>1 AND ActualCategory.Id>0
                             
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
     
      echo '<div class="wgt_tm_cat" >
             <table class="wt t4">
               <tr class="tblHeader">
                 <th>Catégorie</th>
                 <th>Participants</th>
               </tr>';
     $cat_ids = array();
     while ($stmt->fetch()){
         $cat_ids[$a_cat_id] = $cat_n.' '.$cat_gen.' - '.$cat_sn.' '.$weight;
         echo '<tr>
                  <td>'.$cat_n.' '.$cat_gen.' - '.$cat_sn.' '.$weight.'</td>
                   <td>'.$weighted.'</td>
               </tr>';
     }
     
     $stmt->close();
     echo '</table></div></div>';
     $cat_table=array();
     foreach ($cat_ids as $acatId=>$acatName) {
          $stmt = $mysqli->prepare("select
                                     Fight.Id,
                                     CategoryStep.Name,
                                     Fight.pv1,
                                     Fight.forfeit1,
                                     Fight.pv2,
                                     Fight.forfeit2,
                                     Fight.noWinner,
                                     TC1.Surname,
                                     TC1.Name, 
                                     TC2.Surname,
                                     TC2.Name,
                                     Fight.TieBreakFight,
                                     if (CategoryStep.CategoryStepsTypeId=1,0,CategoryStep.Id)
                                     
                                 FROM Fight
                                 INNER JOIN CategoryStep ON step_id=CategoryStep.Id
                                 LEFT OUTER JOIN TournamentCompetitor as TC1 on TC1.Id = TournamentCompetitor1Id
                                 LEFT OUTER JOIN TournamentCompetitor as TC2 on TC2.Id = TournamentCompetitor2Id
                                 WHERE  Fight.ActualCategoryId=? and Fight.pv1 IS NULL order by CategoryStep.Id, Fight.Id
                                 LIMIT 4");                
         $stmt->bind_param("i", $acatId );
         $stmt->bind_result( $f_id, $step_name, $pv1, $ff1, $pv2, $ff2, $nowin, $Surname1, $Name1, $Surname2, $Name2, $tbf, $order);
         $stmt->execute();
         $tbl= '
             <div class="wgt_tm_cat" style="background-color:#CCCCCC;" ><span>'.$acatName.'</span>
             <table class="wt t4">
               <tr class="tblHeader">
                 <th>Status</th>
                 <th>Type</th>
                 <th>Rouge</th>
                 <th>Blanc</th>
               </tr>';
         
         $rows = array();
         while ($stmt->fetch()){
             $tb_s='';
             if ($tbf>0){
                 $tb_s='(Tie Break)';
             }
              $row_value='';
             if (empty($Surname1) && empty($Surname2)) {
               $row_value =' 
                      <td>'. $step_name.' '.$tb_s.'</td>
                      <td colspan="2">A venir...</td>
                      </tr>';
             } else {
                $row_value = '<td>';
                $row_value = $row_value.$step_name.' '.$tb_s.'</td>
                  <td>'.$Surname1.' '.$Name1.'</td>
                  <td>'.$Surname2.' '.$Name2.'</td>
                </tr>';
             }
             $rows[$order.'-'.$f_id] = $row_value;
         }
         $stmt->close();
         
         $k_order = order_fight(array_keys($rows));

         $index=0;
         foreach($k_order as $key){
             if ($index==0){
                $tbl=$tbl.  '<tr><td>Prochains / Combatent:</td>';
             } else if ($index==1){
                $tbl=$tbl.  '<tr><td>Se préparent:</td>';
             } else{
                $tbl=$tbl.  '<tr><td></td>';
             }
             $tbl=$tbl.   $rows[$key];
             $index+=1;
             if ($index>=3){
                 break;
             }  
         }
         if (count($k_order)>3){
            $tbl=$tbl.  '<tr ><td>...</td><td colspan="3"></td></tr>';
         }
         $tbl=$tbl.  '</table></div>';
         
         $cat_table[]=$tbl;
     }
     if (count($cat_table)>0) {
         for ($index=0; $index<$shift; $index++){
            $tmp = $cat_table[0];
            array_splice($cat_table, 0, 1);
            $cat_table[]=$tmp;
         }
         
         foreach($cat_table as $tbl){
             echo $tbl;
         }
     }
     
     echo '
              </span> 
           </div>     
        </div>   
     </div>
     
<script>
setTimeout(function(){
   location.assign("./categories.php?shift=';
   
   if (count($cat_table)>0){
    echo ($shift+1)%count($cat_table);
    } else {
       echo 0;
    }
   
   echo'");
}, 10000);
</script>
</body>
</html>';
?>



