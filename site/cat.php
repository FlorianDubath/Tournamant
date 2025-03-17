<?php

ob_start();
session_name("Tournament");	
session_start();

if (empty($_SESSION['_UserId'])) {
	header('Location: ./index.php');
}

$is_table =  $_SESSION['_IsMainTable'] ==1 ||  $_SESSION['_IsMatTable'] ==1;

include 'connectionFactory.php';
include '_categoryHelper.php';
$curr_c_id=0;
if ($_SESSION['_IsMainTable']==1 && !empty($_POST['acat']) && !empty($_POST['cid']) && !empty($_POST['cc']) && $_POST['cc']==1){
   
    cancel_Category($_POST['acat'], False);
    $curr_c_id=(int)$_POST['cid'];
}

if ($is_table && !empty($_POST['fid']) && !empty($_POST['acat']) && !empty($_POST['cid']) && !empty($_POST['cc']) && $_POST['cc']==1){
   
    cancel_fight($_POST['acat'], $_POST['fid']);
    $curr_c_id=(int)$_POST['cid'];
}



if(empty($_GET['cid']) && $curr_c_id==0) {
      	header('Location: ./index.php');
}

if (!empty($_GET['cid'])){
    $curr_c_id=(int)$_GET['cid'];
}

$missing=0;
if (!empty($_GET['m'])) {
      	$missing=(int)$_GET['m'];
}





include '_commonBlock.php';

writeHead();

echo'
 <style>
        .pop_sb{
        width:100%;
        height:100%;
        position:fixed;
        background-color:black;
        font-family:sans;
        top:0px;
        left:0px;
        }
        .title{
           height:7%;
           color:#DDFFDD; 
           text-align:center;
           font-size:40px;
           margin-top:1%;
           margin-left:auto; 
           margin-right:auto;
           width:calc(100% - 320px);
           display:inline-block;
        }
          .blue, .nblue {
            height:440px;
            display:inline-block;
            width:calc(50% - 3px);
            background-color: blue;
            color:white;
            text-align:center;
            position:relative;
  
       
        }
        .white, .nwhite {
            height:440px;
            display:inline-block;
            width:calc(50% - 3px);
            background-color: white;
            text-align:center;
            position:relative;
        }
        .nf {
           width:100%;
           background-color:#ffffcc;
           position:absolute;
           bottom:0px;
           left:0px;
    
       }
       .ntitle{
          
           color:black; 
           text-align:center;
           font-size:40px;
           margin-top:20px;
           margin-left:auto; 
           margin-right:auto;
           margin-bottom:10px;
           width:100%;
           display:inline-block;
        }
        .nblue {
            color:#999999;
            background-color: #000055;
            height:unset;
         }
         .nwhite {
            background-color: #777777;
            height:unset;
        }
        .fighter {
        margin-top:20px;
        font-size:80px;
        }
        .score {
       // margin-top:40px;
        font-size:200px;
        letter-spacing: 20px;
        }
        
        .timer {
           height:170px;
           color:#DDFFDD; 
           text-align:center; 
           font-size:170px;
        }
        
        .win {
            position: absolute;
            width:100%;
            
            font-size:100px;
            color:#bf9b30;
            right:0px;bottom:0px;
        }
        
        .bbtn {
           display:inline-block;
           padding:5px; 
           text-decoration:none;
           color:black;
           background-color:grey;
           border:solid 2px lightgrey;
           border-radius:5px;
           margin-right:5px;
           font-size:16px;
        }
        .sbbb{
           display:inline-block;
           padding-top:5px;
           text-align:center;
           vertical-align:center;
           width:100%;
        }
        .pos_1{
            position: absolute;
            left:calc(50% - 200px);
            top:120px;
            font-size:18;
            
        }
        .pos_s_1{
            position: absolute;
            left:calc(50% - 200px);
            top:170px;
        }
         .pos_2{
            position: absolute;
            left:calc(50% - 50px);
            top:120px;
            font-size:18;
        }
         .pos_s_2{
            position: absolute; 
            left:calc(50% - 50px);
            top:170px;
        }
         .pos_3{
            position: absolute;
            right:calc(50% - 180px);
            top:120px;
            font-size:18;
        }
          .pos_s_3{
            position: absolute; 
            right:calc(50% - 180px);
            top:170px;
        }  
        .pos_4{
            position: absolute;
            right:calc(50% - 300px);
            top:120px;
            font-size:18;
        }
          .pos_s_4{
            position: absolute; 
            right:calc(50% - 300px);
            top:170px;
        }
        
        .pos_s_d1{
            position: absolute; 
            right:20px;
            bottom:20px;
        }
        .pos_s_d2{
            position: absolute; 
            left:20px;
            bottom:20px;
        }
        
        .sh_1{
          position: absolute; 
          right:calc(50% - 250px);
          top:200px;
          width:60px;
          height:80px;
          background-color:yellow;
          border:solid grey 2px;
        }  
        .sh_2{
         position: absolute; 
          right:calc(50% - 275px);
          top:225px;
          width:60px;
          height:80px;
          background-color:yellow;
          border:solid grey 2px;
        }
        .sh_3{
         position: absolute; 
          right:calc(50% - 300px);
          top:250px;
          width:60px;
          height:80px;
          background-color:red;
          border:solid grey 2px;
        }
      
       
        
        .key{
           width:50px;
        }
    </style>
<body>
    <div class="f_cont">';

echo'        
       <div class="cont_l">
         <div class="h">'; 
         
   
writeBand();      
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
                             
     $stmt->bind_param("i", $curr_c_id );
     $stmt->bind_result( $catId, $cat_n,$cat_sn,$cat_dur,$cat_gen,$weight, $weighting_end, $total, $weighted);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close();         
         
     $stmt = $mysqli->prepare("select
                                 ActualCategory.Id,
                                 ActualCategory.Name,
                                 CategoryId,
                                 Category2Id,
                                 IsCompleted,
                                 max(pv1) IS NOT NULL 
                             from ActualCategory
                             LEFT OUTER JOIN Fight ON Fight.ActualCategoryId = ActualCategory.Id
                             WHERE CategoryId=? OR Category2Id=?
                             GROUP BY ActualCategory.Id, ActualCategory.Name, CategoryId, Category2Id, IsCompleted
                           ");
                             
     $stmt->bind_param("ii", $curr_c_id,$curr_c_id );
     $stmt->bind_result( $actual_cat_Id, $ac_name, $cccid_1, $cccid_2, $cat_completed, $started);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close();  
     
     if (empty($actual_cat_Id)) {
         

echo '
            <span class="h_title">
               '.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.'';
               

               
               
            echo'   
            </span>
            <span class="h_txt">
                <span class="btnBar"> 
                   <a class="pgeBtn" href="listingcat.php" title="Fermer" >Fermer</a>
                 </span>
                  <span class="btnBar"> ';
       if ($missing==-1) {
          echo ' <a class="pgeBtn"  href="cat.php?cid='.$curr_c_id.'" title="Tous les participants">Tous les participants ('.$total.')</a>
                 Participants pesés ('.$weighted.')
                 <a class="pgeBtn"  href="cat.php?cid='.$curr_c_id.'&m=1" title="Participants à peser/manquants">Participants à peser/manquants ('.$total - $weighted.')</a>';
       } else if ($missing==1) {
                 echo ' 
                 <a class="pgeBtn"  href="cat.php?cid='.$curr_c_id.'" title="Tous les participants">Tous les participants ('.$total.')</a>
                 <a class="pgeBtn"  href="cat.php?cid='.$curr_c_id.'&m=-1" title="Participants pesés">Participants pesés ('.$weighted.')</a>
                 Participants à peser/manquants ('.$total - $weighted.')';
       }  else {
                 echo ' 
                 Tous les participants ('.$total.')
                 <a class="pgeBtn"  href="cat.php?cid='.$curr_c_id.'&m=-1" title="Participants pesés">Participants pesés ('.$weighted.')</a>
                 <a class="pgeBtn"  href="cat.php?cid='.$curr_c_id.'&m=1" title="Participants à peser/manquants">Participants à peser/manquants ('.$total - $weighted.')</a>';
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
      $query=$query." ORDER BY TournamentCompetitor.CheckedIn desc, TournamentRegistration.WeightChecked DESC,ClubId, TournamentCompetitor.Surname, TournamentCompetitor.Name";
      
      
      $stmt = $mysqli->prepare($query);
     $stmt->bind_param("i", $curr_c_id );
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
        
       
        echo $ac_name;
        
if ($_SESSION['_IsMainTable']==1 && !empty($actual_cat_Id)) {
 echo'    
      <a class="btn_sos" onclick="toggleClass(document.getElementById(\'pop_acat\'),\'pop_hide\');"></a>
			    <span class="pop_back pop_hide" Id="pop_acat">
			       <span class="popcont">
				   <span class="pop_tt">ANNULER LA CATEGORIE </span> 
				   '.$ac_name.'<br/><br/>
				     
				     <span class="fmessage">Cette opération sera annulée si des combats ont déjà eu lieu. Dans ce cas commencez par les annuler</span><br/>';
				    
				        echo '<br/><span class="btnBar"> 
				        
				        <form action="./cat.php" method="post">
                                        <input type="hidden" name="acat" value="'.$actual_cat_Id.'"/>
                                        <input type="hidden" name="cid" value="'.$curr_c_id.'"/>
		                       <input type="hidden" name="cc" value="1"/>
		                        <input class="pgeBtn"  type="submit" value="Annuler la catégorie">
		                        <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_acat\'),\'pop_hide\');">Fermer</a>  
		                        </form>
		                         </span>';
		                      
				  
				    
				   echo'
			 	    
					
				  </span>
			     </span>      
     
   ';
}  
        
        
        echo'</span>';
      
        
        echo' <span class="btnBar"> 
                   <a class="pgeBtn" href="listingcat.php" title="Fermer" >Fermer</a>
               </span>';
               
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
                                 Medal,
                                 TournamentCompetitor.Surname,
                                 TournamentCompetitor.Name,  
                                 TournamentClub.Name
                             FROM ActualCategoryResult
                             INNER JOIN TournamentCompetitor on TournamentCompetitor.Id =  Competitor1Id
                             INNER JOIN TournamentClub ON ClubId=TournamentClub.Id
                             WHERE  ActualCategoryId=?");
     $stmt->bind_param("i", $actual_cat_Id );
     $stmt->bind_result( $rk, $Medal, $Surname, $Name, $Club);
     $stmt->execute();
     
     while ($stmt->fetch()){
          echo ' <tr class="result_'.$Medal.'">
          <td>'.$medal_char[$Medal].$rk.'</td>
      <td>'.$Surname.' '.$Name.'</td>
      <td>'. $Club.'</td>
      </tr>';

     }
     
     $stmt->close();
     echo '</table>';
               
               
            
      }               
               
               
               
 echo'            <span class="h_title"> Participants  <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'part_list\'), \'hidden_pannel\')">montrer/cacher</a></span>
       <span id="part_list" class="'; 
       if ($started==1) {echo'hidden_pannel';}
       echo'">    
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
                             WHERE  ActualCategory.Id=?
                             ORDER BY TournamentCompetitor.Surname, TournamentCompetitor.Name");
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
     echo '</table></span>';
     

     

     
     echo ' <span class="h_title">  Combats (Durée :'.$cat_dur.'min)</span>
     
          <table class="wt t4">
      <tr class="tblHeader">
      <th>Type</th>
      <th>PV</th>
      <th>Rouge</th>
      <th></th>
      <th>Blanc</th>
      <th>PV</th>
      </tr>';

     
      $stmt = $mysqli->prepare("select
                                 Fight.Id,
                                 CategoryStep.Name,
                                 Fight.pv1,
                                 Fight.pv2,
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
                             WHERE  Fight.ActualCategoryId=? order by CategoryStep.Id, Fight.Id");
                             
                             
     $stmt->bind_param("i", $actual_cat_Id );
     $stmt->bind_result( $f_id, $step_name, $pv1, $pv2, $Surname1, $Name1, $Surname2, $Name2, $tbf, $order);
     $stmt->execute();
     
     $pop_counter=1;
     $rows = array();
     while ($stmt->fetch()){
         $tb_s='';
         if ($tbf>0){
             $tb_s='(Tie Break)';
         }
     
         $row_value='';
         if (empty($Surname1) || empty($Surname2)) {
           $row_value =' <tr >
                  <td>'. $step_name.' '.$tb_s.'</td>
                  <td></td>
                  <td colspan="3">A venir...</td>
                  <td></td>
                  </tr>';
         } else if (empty($pv1) && empty($pv2)){
           $row_value = ' <tr >
                  <td>'. $step_name.' '.$tb_s.'</td>
                  <td>';
                  if($is_table){
                       $row_value = $row_value.'
                      
                      <span class="pop_back pop_hide" Id="pop_1_'.$pop_counter.'"><span class="popcont">
                         Victoire de '.$Surname1.' '.$Name1.' (Rouge) par:
                       <form action="figtRes.php" method="post">
                                 <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                                 <input type="hidden" name="fid" value="'.$f_id.'" />
                                 <input type="hidden" name="pv1" value="10" />
                                 <input type="hidden" name="pv2" value="0" />
                                 <input type="hidden" name="cid" value="'.$catId.'" />
                                 <input class="resbtn" type="submit" value="Ippon">
                       </form>
                       
                       <form action="figtRes.php" method="post">
                                 <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                                 <input type="hidden" name="fid" value="'.$f_id.'" />
                                 <input type="hidden" name="pv1" value="7" />
                                 <input type="hidden" name="pv2" value="0" />
                                 <input type="hidden" name="cid" value="'.$catId.'" />
                                 <input class="resbtn" type="submit" value="Waza-ari ">
                       </form>
                       
                       <form action="figtRes.php" method="post">
                                 <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                                 <input type="hidden" name="fid" value="'.$f_id.'" />
                                 <input type="hidden" name="pv1" value="5" />
                                 <input type="hidden" name="pv2" value="0" />
                                 <input type="hidden" name="cid" value="'.$catId.'" />
                                 <input  class="resbtn" type="submit" value="Yuko">
                       </form>
                       
                       <form action="figtRes.php" method="post">
                                 <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                                 <input type="hidden" name="fid" value="'.$f_id.'" />
                                 <input type="hidden" name="pv1" value="1" />
                                 <input type="hidden" name="pv2" value="0" />
                                 <input type="hidden" name="cid" value="'.$catId.'" />
                                 <input  class="resbtn" type="submit" value="Décision">
                       </form>
                      
                       <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_1_'.$pop_counter.'\'),\'pop_hide\');">Annuler</a>
                      
                      </span></span>
                      <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_1_'.$pop_counter.'\'),\'pop_hide\');">Victoire</a>
                      <span style="display:none;" Id="nc_1_'.$pop_counter.'">'.$Surname1.' '.$Name1.'</span>
                      ';
                  }
                  $row_value = $row_value.'
                 </td>
                  <td>'.$Surname1.' '.$Name1.'</td>
                  <td><a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_VS\'),\'pop_hide\');set_name(\''.str_replace("'", "\\'", $Surname1).' '.str_replace("'", "\\'", $Name1).'\',\''.str_replace("'", "\\'", $Surname2).' '.str_replace("'", "\\'", $Name2).'\');set_next_name('.$pop_counter.');reset();reset_pin_down();displayScore();setf_id('.$f_id.')">V.S.</a></td>
                  <td>'.$Surname2.' '.$Name2.'</td>
                  <td> ';
                  if($is_table){
                      $row_value = $row_value.'
                      <span class="pop_back pop_hide" Id="pop_2_'.$pop_counter.'"><span class="popcont">
                         Victoire de '.$Surname2.' '.$Name2.' (Blanc) par:
                       <form action="figtRes.php" method="post">
                                 <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                                 <input type="hidden" name="fid" value="'.$f_id.'" />
                                 <input type="hidden" name="pv1" value="0" />
                                 <input type="hidden" name="pv2" value="10" />
                                 <input type="hidden" name="cid" value="'.$catId.'" />
                                 <input class="resbtn" type="submit" value="Ippon">
                       </form>
                       
                       <form action="figtRes.php" method="post">
                                 <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                                 <input type="hidden" name="fid" value="'.$f_id.'" />
                                 <input type="hidden" name="pv1" value="0" />
                                 <input type="hidden" name="pv2" value="7" />
                                 <input type="hidden" name="cid" value="'.$catId.'" />
                                 <input class="resbtn" type="submit" value="Waza-ari ">
                       </form>
                       
                       <form action="figtRes.php" method="post">
                                 <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                                 <input type="hidden" name="fid" value="'.$f_id.'" />
                                 <input type="hidden" name="pv1" value="0" />
                                 <input type="hidden" name="pv2" value="5" />
                                 <input type="hidden" name="cid" value="'.$catId.'" />
                                 <input class="resbtn" type="submit" value="Yuko ">
                       </form>
                 
                       <form action="figtRes.php" method="post">
                                 <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                                 <input type="hidden" name="fid" value="'.$f_id.'" />
                                 <input type="hidden" name="pv1" value="0" />
                                 <input type="hidden" name="pv2" value="1" />
                                 <input type="hidden" name="cid" value="'.$catId.'" />
                                 <input class="resbtn" type="submit" value="Décision">
                       </form>
                  
                       <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_2_'.$pop_counter.'\'),\'pop_hide\');">Annuler</a>
                      
                      </span></span>
                      <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_2_'.$pop_counter.'\'),\'pop_hide\');">Victoire</a></td>
                      <span style="display:none;" Id="nc_2_'.$pop_counter.'">'.$Surname2.' '.$Name2.'</span>';
                  }
                  $row_value = $row_value.'</td>
                  </tr>';
                  $pop_counter+=1;
         } else { 
           $cl_1="VIC";
           $cl_2="LOS";
           if ($pv2>0){
              $cl_1="LOS";
              $cl_2="VIC";
           }
           $row_value = ' <tr > 
                  <td>'. $step_name.' '.$tb_s;   
           if($is_table){   
   $row_value=$row_value.'    
      <a class="btn_sos" onclick="toggleClass(document.getElementById(\'pop_canc_fgt_'.$f_id.'\'),\'pop_hide\');"></a>
			    <span class="pop_back pop_hide" Id="pop_canc_fgt_'.$f_id.'">
			       <span class="popcont">
				   <span class="pop_tt">ANNULER LE RESULTAT DU COMBAT </span> 
				   <br/>
				     
				     <span class="fmessage">Cette opération sera annulée si des combats dépendant de celui-ci ont déjà eu lieu. Dans ce cas commencez par les annuler</span><br/>
				     <br/><span class="btnBar"> 
				        
				        <form action="./cat.php" method="post">
                                        <input type="hidden" name="fid" value="'.$f_id.'"/>
                                        <input type="hidden" name="acat" value="'.$actual_cat_Id.'"/>
                                        <input type="hidden" name="cid" value="'.$curr_c_id.'"/>
		                       <input type="hidden" name="cc" value="1"/>
		                        <input class="pgeBtn"  type="submit" value="Annuler le Résultat">
		                        <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_canc_fgt_'.$f_id.'\'),\'pop_hide\');">Fermer</a>  
		                        </form>
		                         </span>
			 	    
					
				  </span>
			     </span>       ';                 
                  
           }       
                  
   $row_value=$row_value.'</td>
                  <td class="'.$cl_1.'">'.$pv1.'</td>
                  <td class="'.$cl_1.'">'.$Surname1.' '.$Name1.'</td>
                  <td>V.S.</td>
                  <td class="'.$cl_2.'">'.$Surname2.' '.$Name2.'</td>
                  <td class="'.$cl_2.'">'.$pv2.'</td>
                  </tr>';
         }
         
         
         $rows[$order.'-'.$f_id] = $row_value;
            
    
         

     }
     
     $stmt->close();
     
     $k_order = order_fight(array_keys($rows));
     
     
     foreach($k_order as $key){
         echo  $rows[$key];
     }
     echo '</table>';
     
 
      
    include '_visualizationHelper.php';   
      $mysqli= ConnectionFactory::GetConnection(); 
      
      $stmt = $mysqli->prepare("select
                                Id,
                                Name,
                                CategoryStepsTypeId
                             from CategoryStep
                             WHERE ActualCategoryId=?
                             order by CategoryStepsTypeId>1 ASC, Id DESC
                           ");
                             
     $stmt->bind_param("i", $actual_cat_Id );
     $stmt->bind_result( $step_id, $stp_name, $step_id_type);
     $stmt->execute();
     $step_1_id = -1;
     $number = 0;
     $step_pool_ids = array();
     while ($stmt->fetch()) {
         if ($step_1_id<0 && $step_id_type==1) {
             $step_1_id = $step_id;
         } else if ($step_id_type<10 &&$step_id_type>1) {
             $step_pool_ids[$step_id]=$stp_name;
         }
         
     }
     $stmt->close();  
     
     foreach($step_pool_ids as $pool_id=>$pool_name) {
         plot_pool($pool_id, $ac_name, $pool_name);
     }
     
     if ($step_1_id>0) {
         plot_table($step_1_id, $ac_name);
     }
  }
              
    
    
   
   
          
              
     echo'
                <span class="btnBar"> 
                   <a class="pgeBtn" href="listingcat.php" title="Fermer" >Fermer</a>
               </span>
           </span>    
           </div>     
        </div>   
     </div>';
 echo'    
 <div id="pop_VS" class="pop_hide pop_sb" >    
<div style="width:100%">
<a class="bbtn" href="MirorScoreBoard.html" target="_new">Tableau des scores</a><span id="title" class="title"></span> 
<a class="bbtn" onclick="toggleClass(document.getElementById(\'pop_sbconf\'),\'pop_hide\');" >Configuration</a>
<a class="bbtn" onclick="toggleClass(document.getElementById(\'pop_VS\'),\'pop_hide\');" >Fermer</a></div>
 



<div class="white" >
    <div id="name_2" class="fighter"></div>
    <div id="s_2" class="score"></div>
    <div class="win" id="win_2" style="display:none;">vainqueur</div>
    <div class="sh_1" id="sh_1_2" style="display:none;"></div>
    <div class="sh_2" id="sh_2_2" style="display:none;"></div>
    <div class="sh_3" id="sh_3_2" style="display:none;"></div>
  
    <a class="bbtn pos_1" onclick=" AddScore(2,100)" >Ippon (<span id="t_100_2">u</span>)</a>
    <a class="bbtn pos_2" onclick=" AddScore(2,10)" >Waza-ari (<span id="t_10_2">i</span>)</a>
    <a class="bbtn pos_3" onclick=" AddScore(2,1)" >Yuko (<span id="t_1_2">o</span>)</a>
    <a class="bbtn pos_4" onclick=" AddShido(2,1)" >Shido (<span id="t_sh_2">p</span>)</a> 
    <a class="bbtn  pos_s_1" onclick=" AddScore(2,-100)" > </span>&nbsp;(<span id="t_m100_2">U</span>)&nbsp;<span class="btn_sos"></span></a>
    <a class="bbtn  pos_s_2" onclick=" AddScore(2,-10)" > (<span id="t_m10_2">I</span>)&nbsp;<span class="btn_sos"></span></a>
    <a class="bbtn  pos_s_3" onclick=" AddScore(2,-1)" > (<span id="t_m1_2">O</span>)&nbsp;<span class="btn_sos"></span></a>
    <a class="bbtn pos_s_4" onclick=" AddShido(2,-1)" > (<span id="t_msh_2">P</span>)&nbsp;<span class="btn_sos"></span></a>
    <div class="sbbb">
<a class="bbtn " onclick="StartPausePin(2)" ><img src="css/pin_down_white.png" width="80px"/>(<span id="t_pd_2">l</span>)</a>
</div>
    <a class="bbtn pos_s_d2" onclick="decision(2)" >Décision (<span id="t_dc_2">v</span>)</a></div>
<div class="blue" >
    <div id="name_1" class="fighter"></div>
    <div id="s_1" class="score"></div>
    <div class="win" id="win_1" style="display:none;">vainqueur</div>
    <div class="sh_1" id="sh_1_1" style="display:none;"></div>
    <div class="sh_2" id="sh_2_1" style="display:none;"></div>
    <div class="sh_3" id="sh_3_1" style="display:none;"></div>
    
    <a class="bbtn pos_1" onclick=" AddScore(1,100)" >Ippon (<span id="t_100_1">q</span>)</a>
    <a class="bbtn pos_2" onclick=" AddScore(1,10)" >Waza-ari (<span id="t_10_1">w</span>)</a>
    <a class="bbtn pos_3" onclick=" AddScore(1,1)" >Yuko (<span id="t_1_1">e</span>)</a> 
    <a class="bbtn pos_4" onclick=" AddShido(1,1)" >Shido (<span id="t_sh_1">r</span>)</a> 
    <a class="bbtn  pos_s_1" onclick=" AddScore(1,-100)" > (<span id="t_m100_1">Q</span>)&nbsp;<span class="btn_sos"></span></a>
    <a class="bbtn  pos_s_2" onclick=" AddScore(1,-10)" > (<span id="t_m10_1">W</span>)&nbsp;<span class="btn_sos"></span></a>
    <a class="bbtn  pos_s_3" onclick=" AddScore(1,-1)" > (<span id="t_m1_1">E</span>)&nbsp;<span class="btn_sos"></span></a>
    <a class="bbtn pos_s_4" onclick=" AddShido(1,-1)" > (<span id="t_msh_1">R</span>)&nbsp;<span class="btn_sos"></span></a>
    <div class="sbbb">
        <a class="bbtn" onclick="StartPausePin(1)" ><img src="css/pin_down_blue.png" width="80px"/>(<span id="t_pd_1">a</span>)</a>
    </div>
    <a class="bbtn  pos_s_d1" onclick="decision(1)" >Décision (<span id="t_dc_1">n</span>)</a>
</div>
<div class="sbbb">
<a class="bbtn" onclick="StartPauseTimer()" >&nbsp;&nbsp;&nbsp;&nbsp;&#x23EF (bare d\'espace)&nbsp;&nbsp;&nbsp;&nbsp;</a>
<a class="bbtn" onclick="addTime(1000)"  > Ajouter 1" &nbsp; <span class="btn_sos"> &nbsp; <span></span></a>
<a class="bbtn" onclick="addTime(-1000)"  > Enlever 1" &nbsp; <span class="btn_sos"> &nbsp; <span></span></a>

<a class="bbtn" onclick="gong()" >Gong (<span id="t_gong">1</span>)</a>
</div>
<div class="timer">
<span id="gs" style="display:none;" >GS&nbsp;</span>
<span id="time"></span>
<span id="running" style="display:none;">&#x23F2;</span>

</div><br/>
<div class="timer">
<img src="css/pin_down.png"  id="img_pd_time" style="display:none;"/>
<img src="css/pin_down_blue.png"  id="img_pd_blue" style="display:none;"/>
<img src="css/pin_down_white.png"  id="img_pd_white" style="display:none;"/>
<span id="pd_time" style="display:none;"></span>
<span id="pd_running" style="display:none;">&#x23F2;<a class="bbtn" onclick="stop_pin_down()" >Toketa (<span id="t_toketa">ArrowDown</span>)</a></span>
</div>
<span id="next_fight" class="nf" style="display:none">
   <span  class="ntitle" >Prochain Combat</span>
    <div class="nwhite" >
   <span id="next_name_2"  class="fighter" style="font-size:60px;"></span>
   </div>
   <div class="nblue" >
   <span id="next_name_1"  class="fighter" style="font-size:60px;"></span>
   </div>
</span>

<span class="pop_back pop_hide" Id="pop_sbconf">
		<span class="popcont">
		 <span class="pop_tt">CONFIGURATION </span> <br/><br/>
		 <!-- Catégorie: <input type="text" id="cat_name" value="'.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.'"/><br/><br/>
		  Durée du combat: <input type="number" id="cat_dur" min="1" value="'.$cat_dur.'"/>min<br/><br/>
		  Combattant Bleu: <input type="text" id="fight_blue" value="Combattant 1"/><br/><br/>
		  Combattant Blanc: <input type="text" id="fight_white" value="Combattant 2"/><br/><br/> -->
		  
		  Osaekomi combattant bleu <input type="text" id="k_pd_1" maxlength="10"  value="ArrowRight"/> <br/><br/>
		  Osaekomi combattant blanc <input type="text" id="k_pd_2" maxlength="10"  value="ArrowLeft"/><br/><br/>
		  Toketa <input type="text" id="k_toketa" maxlength="10"  value="ArrowDown"/><br/><br/><br/>
		  
		  Ipon combattant bleu <input class="key" type="text" id="k_100_1" maxlength="1"  value="z"/> Annulation <input class="key" type="text" id="k_m100_1" maxlength="1"  value="h"/><br/><br/>
		  Waza-ari combattant bleu <input class="key" type="text" id="k_10_1" maxlength="1"  value="u"/> Annulation <input class="key" type="text" id="k_m10_1" maxlength="1"  value="j"/><br/><br/>
		  Yuko combattant bleu <input class="key" type="text" id="k_1_1" maxlength="1"  value="i"/> Annulation <input class="key" type="text" id="k_m1_1" maxlength="1"  value="k"/> <br/><br/>
		  Shido combattant bleu <input class="key" type="text" id="k_sh_1" maxlength="1"  value="o"/> Annulation <input class="key" type="text" id="k_msh_1" maxlength="1"  value="l"/><br/><br/>
		  
		  Décision combattant bleu <input class="key" type="text" id="k_dc_1" maxlength="1"  value="n"/><br/><br/><br/>
		  
		  Ipon combattant blanc <input  class="key" type="text" id="k_100_2" maxlength="1"  value="q"/> Annulation <input class="key" type="text" id="k_m100_2" maxlength="1"  value="a"/><br/><br/>
		  Waza-ari combattant blanc <input  class="key" type="text" id="k_10_2" maxlength="1"  value="w"/> Annulation <input class="key" type="text" id="k_m10_2" maxlength="1"  value="s"/><br/><br/>
		  Yuko combattant blanc <input  class="key" type="text" id="k_1_2" maxlength="1"  value="e"/> Annulation <input class="key" type="text" id="k_m1_2" maxlength="1"  value="d"/><br/><br/>
		  Shido combattant blanc <input class="key"  type="text" id="k_sh_2" maxlength="1"  value="r"/> Annulation <input class="key" type="text" id="k_msh_2" maxlength="1"  value="f"/><br/><br/>
		  
		  Décision combattant blanc <input class="key" type="text" id="k_dc_2" maxlength="1"  value="v"/><br/><br/><br/>
		  
		  Gong <input class="key" type="text" id="k_gong" maxlength="1"  value="1"/><br/><br/>
		  

		  
		 
      <a class="bbtn" onclick="toggleClass(document.getElementById(\'pop_sbconf\'),\'pop_hide\');
                              /* conf();*/conf_key();" >Appliquer</a>
      <a class="bbtn" onclick="toggleClass(document.getElementById(\'pop_sbconf\'),\'pop_hide\');" >Annuler</a></div>
		</span>
</span>

<span class="pop_back pop_hide" Id="pop_victory">
		<span class="popcont">
		      <span class="pop_tt">VICTOIRE DE </span> <br/><br/>
		      <span class="pop_tt"><span id="vic_name"></span> (<span id="vic_color"></span>) </span> <br/><br/>
		      <span class="pop_tt">PAR  </span> <br/><br/>
		      <span class="pop_tt"><span id="vic_type"></span> - <span id="vic_score"></span> </span> <br/><br/>
		      
		      <form action="figtRes.php" method="post">
		        
                                 <input type="hidden" name="acid" value="'.$actual_cat_Id.'" />
                                 <input type="hidden" name="fid" id="fid" value="-1" />
                                 <input type="hidden" name="pv1" id="pv1" value="0" />
                                 <input type="hidden" name="pv2" id="pv2" value="0" />
                                 <input type="hidden" name="cid" value="'.$catId.'" />
                                 <input class="bbtn" type="submit" value="Enregistrer "/>
      <a class="bbtn" onclick="toggleClass(document.getElementById(\'pop_victory\'),\'pop_hide\');" >Annuler</a></div>
               </form>
		</span>
</span>


 <audio controls src="gong-92707.mp3" id="gong" style="display:none;"></audio>     
     
    </div>
     
</body>

<script>

      
var running=false;
var pin_down=0;
var direction=-1;
var start = Date.now();
var current = 4*60000;  // 4min
var display = current;
var shido_1=0;
var shido_2=0;
var score_1=0;
var score_2=0;
var winner=0;
var pin_down_start = Date.now();
var pd_time=0;
var pd_score=0;
var gs=false;
var char_pd_1="ArrowRight";
var char_pd_2="ArrowLeft";
var char_toketa="ArrowDown";
var char_sh_1="r";
var char_sh_2="p";
var char_100_1="q";
var char_10_1="w";
var char_1_1="e";
var char_100_2="u";
var char_10_2="i";
var char_1_2="o";
var char_msh_1="r";
var char_msh_2="p";
var char_m100_1="q";
var char_m10_1="w";
var char_m1_1="e";
var char_m100_2="u";
var char_m10_2="i";
var char_m1_2="o";
var char_dc_1="n";
var char_dc_2="v";
var f_id =-1;
var char_gong="1";

function setf_id(new_f_id){
 f_id=new_f_id;
}

function StartPauseTimer(){
  running = !running;
      if (running){ 
        reset_pin_down();
        start = Date.now();
      } else {
         current = display;
         stop_pin_down();
      }
      document.getElementById("running").style.display= running?"inline-block":"none";
      localStorage.setItem("running", running);
}

function StartPausePin(num){
        if (pin_down>0){
           if (num==pin_down){
           // same key pressed again: nothing to do
           } else {
           // other key switch side pin down and possible scoring
              pin_down=num;
              score_1+=(num==1)?pd_score:-pd_score;
              score_2+=(num==1)?-pd_score:pd_score;
              localStorage.setItem("score_1", score_1);
              localStorage.setItem("score_2", score_2);
              displayScore();
              check_score();  
              localStorage.setItem("pin_down",pin_down);
          } 
        } else {
           reset_pin_down();
           pin_down=num;
           pin_down_start = Date.now();
        }
}

function AddScore(fighter_num, amount){
    if (fighter_num==1 &&  score_1+amount>=0){
       score_1+=amount;
    } else if (fighter_num==2 &&  score_2+amount>=0){
       score_2+=amount;
    }
    if (amount<0){
        winner=0;
        document.getElementById("win_1").style.display= "none";
        document.getElementById("win_2").style.display= "none";
    }
    check_score();
}

function AddShido(fighter_num, amount){
    if (fighter_num==1 &&  shido_1+amount>=0){
       shido_1+=amount;
    } else if (fighter_num==2 &&  shido_2+amount>=0){
       shido_2+=amount;
    }
    if (amount<0){
        winner=0;
        document.getElementById("win_1").style.display= "none";
        document.getElementById("win_2").style.display= "none";
    }
    check_score();
}

function decision(fighter_num){
        if (fighter_num==1) {
            winner=1;
            document.getElementById("win_1").style.display= "inline-block"; 
            document.getElementById("win_2").style.display= "none"; 
            check_score();
        } else if  (fighter_num==2) {
            winner=2;
            document.getElementById("win_2").style.display= "inline-block"; 
            document.getElementById("win_1").style.display= "none"; 
            check_score();
        }
}


document.addEventListener(
  "keydown",
  (event) => {
    const keyName = event.key;
    if (keyName === " ") {
       StartPauseTimer();
    } else if (keyName === char_pd_1){
       StartPausePin(1);
    }  else if (keyName === char_pd_2){
       StartPausePin(2)
    } else if (keyName === char_toketa){
       stop_pin_down();
    }else if (keyName === char_sh_1){
        AddShido(1,1);
    } else if (keyName === char_msh_1){
        AddShido(1,-1);
    } else if (keyName === char_sh_2){
        AddShido(2,1);
    } else if (keyName === char_msh_2){
        AddShido(2,-1);
    } else if (keyName === char_100_1){
        AddScore(1,100);
    } else if (keyName === char_100_2){
        AddScore(2,100);
    } else if (keyName === char_10_1){
        AddScore(1,10);
    } else if (keyName === char_10_2){
        AddScore(2,10);
    } else if (keyName === char_1_1){ 
        AddScore(1,1);
    } else if (keyName === char_1_2){
        AddScore(2,1);
    } else if (keyName === char_m100_1){
        AddScore(1,-100);
    } else if (keyName === char_m100_2){
        AddScore(2,-100);
    } else if (keyName === char_m10_1){
        AddScore(1,-10);
    } else if (keyName === char_m10_2){
        AddScore(2,-10);
    } else if (keyName === char_m1_1){ 
        AddScore(1,-1);
    } else if (keyName === char_m1_2){
        AddScore(2,-1);
    } else if (keyName === char_dc_1){
        decision(1);
    } else if (keyName === char_dc_2){
        decision(2);
    } else if (keyName === char_gong){
        gong();
    }
  },
  false,
);

function gong(){
  document.getElementById("gong").play();
}

function display_pd(){
    document.getElementById("pd_time").style.display= (pin_down>0 || pd_time>0)?"inline-block":"none";
    document.getElementById("img_pd_time").style.display= (pd_time>0 && pin_down==0)?"inline-block":"none";
    document.getElementById("img_pd_blue").style.display= (pd_time>0 && pin_down==1)?"inline-block":"none";
    document.getElementById("img_pd_white").style.display= (pd_time>0 && pin_down==2)?"inline-block":"none";
    
    document.getElementById("pd_time").innerHTML=pd_time+"\"";
    document.getElementById("pd_running").style.display= pin_down>0?"inline-block":"none";
    
}

function reset(){
    f_id=-1;
    score_1=0;
    score_2=0;
    shido_1=0;
    shido_2=0;
    winner=0;
    running=false;
    gs=false;
    
    document.getElementById("win_1").style.display= "none";
    document.getElementById("win_2").style.display= "none";
    
   
    localStorage.setItem("running", running);
    localStorage.setItem("score_1", score_1);
    localStorage.setItem("shido_1", shido_1);
    localStorage.setItem("score_2", score_2);
    localStorage.setItem("shido_2", shido_2);
    localStorage.setItem("winner", winner);
    localStorage.setItem("gs", gs);
};

function set_duration(minutes){
    current = minutes*60000; 
    direction=-1;
    display = current; 
    localStorage.setItem("time", displayTime(current/1000));
    document.getElementById("time").innerHTML =  displayTime(current/1000);
}

function set_name(name_1,name_2){
    document.getElementById("name_1").innerHTML=name_1;
    document.getElementById("name_2").innerHTML=name_2;
    localStorage.setItem("name_1", name_1);
    localStorage.setItem("name_2", name_2);
}

function set_next_name(counter){
    var span_name_1 = document.getElementById("nc_1_"+(counter+1));
    name_1 = span_name_1?span_name_1.innerHTML:"";
    var span_name_2 = document.getElementById("nc_2_"+(counter+1));
    name_2 = span_name_2?span_name_2.innerHTML:"";

    document.getElementById("next_name_1").innerHTML=name_1;
    document.getElementById("next_name_2").innerHTML=name_2;
    localStorage.setItem("next_name_1", name_1);
    localStorage.setItem("next_name_2", name_2);
    if (name_1!="" && name_2!=""){
       document.getElementById("next_fight").style.display = "inline-block";
    } else {
       document.getElementById("next_fight").style.display = "none";
    }
}

function set_title(title){
    document.getElementById("title").innerHTML=title;
    localStorage.setItem("title", title);
}

function pauseTimer(){
    running=false;
    pin_down=0;
    current = display;
    document.getElementById("running").style.display= running?"inline-block":"none";
    localStorage.setItem("running", running);
    document.getElementById("pd_running").style.display= (pin_down>0)?"inline-block":"none";
    localStorage.setItem("pin_down", pin_down);
}

function check_time(){
   if (display<=0 && pin_down==0){
      gong();
      pauseTimer();
      if (score_1>score_2){
          winner=1;
          document.getElementById("win_1").style.display= "inline-block"; 
          check_score();
      } else if (score_2>score_1){
          winner=2;
          document.getElementById("win_2").style.display= "inline-block"; 
          check_score();
      } else {
        gs=true;
        localStorage.setItem("gs", gs);
        direction=1;
        current = 0;  
        display = 0;
        localStorage.setItem("time", displayTime(0));
        document.getElementById("time").innerHTML=  displayTime(0);
        document.getElementById("gs").style.display = "inline-block";
        
      }
      
   }
}

function  check_pd_time() {
  if (pin_down>0){
      if (pd_time>=5 && pd_score<1){
          pd_score=1;
          if (pin_down==1){
              score_1+=1;
              localStorage.setItem("score_1", score_1);
          } else  if (pin_down==2){
              score_2+=1;
              localStorage.setItem("score_2", score_2);
          }
          displayScore();
          check_score();
      } 
      if (pd_time>=10 && pd_score<10){
          pd_score=10;
           if (pin_down==1){
              score_1+=9;
              localStorage.setItem("score_1", score_1);
          } else  if (pin_down==2){
              score_2+=9;
              localStorage.setItem("score_2", score_2);
          }
          displayScore();
          check_score();
          
      }
      if (pd_time>=20 && pd_score<100){
          pd_score=100;
           if (pin_down==1){
              score_1+=90;
              localStorage.setItem("score_1", score_1);
          } else  if (pin_down==2){
              score_2+=90;
              localStorage.setItem("score_2", score_2);
          }
          displayScore();
          check_score();   
      }
   }
}

function check_score(){

    if (shido_1>0){
       document.getElementById("sh_1_1").style.display= "inline-block";  
    }  else {
        document.getElementById("sh_1_1").style.display= "none";  
    }
    if (shido_1>1){
       document.getElementById("sh_2_1").style.display= "inline-block";  
    } else {
        document.getElementById("sh_2_1").style.display= "none";  
    }
     if (shido_1>2){
       document.getElementById("sh_3_1").style.display= "inline-block"; 
       winner=2;  
       pauseTimer();
    } else {
        document.getElementById("sh_3_1").style.display= "none"; 
        
    }
   
    
   if (shido_2>0){
       document.getElementById("sh_1_2").style.display= "inline-block";  
    }  else {
        document.getElementById("sh_1_2").style.display= "none";  
    }
    if (shido_2>1){
       document.getElementById("sh_2_2").style.display= "inline-block";  
    } else {
        document.getElementById("sh_2_2").style.display= "none";  
    }
     if (shido_2>2){
       document.getElementById("sh_3_2").style.display= "inline-block"; 
       winner=1; 
       pauseTimer();
    } else {
        document.getElementById("sh_3_2").style.display= "none";  
    }
    
    if (score_1>=20){
        winner=1;
        if (pin_down>0){
            gong();
        }
        pauseTimer();
        
        document.getElementById("win_1").style.display= "inline-block";  
    }
    if (score_2>=20){
        winner=2;
        if (pin_down>0){
            gong();
        }
        pauseTimer();
        
        document.getElementById("win_2").style.display= "inline-block";  
    }
    
    if (gs && score_1>score_2){
        winner=1;
        if (pin_down>0){
            gong();
        }
        pauseTimer();
        document.getElementById("win_1").style.display= "inline-block";  
    }
    
    if (gs && score_1<score_2){
        winner=2;
        if (pin_down>0){
            gong();
        }
        pauseTimer();
        document.getElementById("win_2").style.display= "inline-block";  
    }
    localStorage.setItem("score_1", score_1);
    localStorage.setItem("score_2", score_2);
    localStorage.setItem("shido_1", shido_1);
    localStorage.setItem("shido_2", shido_2);
    localStorage.setItem("winner", winner);
    displayScore();
    
    if (winner>0){
        var score_1_win = score_1;
        if (score_1_win>=20 && score_1_win<100) {
            score_1_win+=80;
        }
        
        var score_2_win = score_2;
        if (score_2_win>=20 && score_2_win<100) {
            score_2_win+=80;
        }
        
        if (shido_1>=3) {
            score_1_win+=100;
        }
        
        if (shido_2>=3) {
            score_2_win+=100;
        }
        
        var res_name = "Décision";
        var pv=1;
        if ( Math.abs(Math.floor(score_2_win/100)-Math.floor(score_1_win/100))==1){
            res_name = "Ippon";
            var pv=10;
        } else if (Math.abs(Math.floor(score_2_win/10)-Math.floor(score_1_win/10))==1){
            res_name = "Waza-ari";
            var pv=7;
        } else if (Math.abs(Math.floor(score_2_win)-Math.floor(score_1_win))>=1){
            res_name = "Yuko";
            var pv=5;
        } 
        
    
        document.getElementById("vic_name").innerHTML = document.getElementById("name_"+winner).innerHTML;
        document.getElementById("vic_color").innerHTML = winner==1?"Bleu":"Blanc";
        document.getElementById("vic_type").innerHTML = res_name;
        document.getElementById("vic_score").innerHTML = "2("+pv+")";
           
        document.getElementById("fid").value = f_id;
        document.getElementById("pv1").value = winner==1 ? pv : 0;
        document.getElementById("pv2").value = winner==2 ? pv : 0;
        
        
        toggleClass(document.getElementById("pop_victory"),"pop_hide");
    }
}

function displayScore(){
    document.getElementById("s_1").innerHTML = ("00" + score_1).slice(-3);
    document.getElementById("s_2").innerHTML = ("00" + score_2).slice(-3);
}

function displayTime(time_sec){
    var sign="";
    if (time_sec<0){
        sign="-";
        time_sec= -time_sec;
    }
    return sign+Math.floor(time_sec/60)+"\'"+ ("0" + time_sec%60).slice(-2)+"\"";
}

function reset_pin_down(){
    pin_down=0;
    pd_time=0;
    pd_score=0;
    localStorage.setItem("pin_down", pin_down);
    localStorage.setItem("pd_time", 0);
    display_pd();
}

function stop_pin_down(){
    pin_down=0;
    pd_score=0;
    localStorage.setItem("pin_down", pin_down);
    display_pd();
    check_time();
}

function addTime(durration){
 if (! running){
   current -= direction *durration;
   display=current;
   winner=0;
   document.getElementById("win_1").style.display= "none";
   document.getElementById("win_2").style.display= "none";
   const d_time = displayTime(direction>0?Math.floor(display/1000):Math.ceil(display/1000));
   localStorage.setItem("time", d_time);
   document.getElementById("time").innerHTML= d_time;
   check_time();
   check_score();  
 }  
}


     

setInterval(function() {
    if (running){
        var delta = Date.now() - start; // milliseconds elapsed since start
        display = current + direction* delta;
        const d_time = displayTime(direction>0?Math.floor(display/1000):Math.ceil(display/1000));
        localStorage.setItem("time", d_time);
        document.getElementById("time").innerHTML= d_time;
        check_time();
        
        if (pin_down>0){
            delta = Date.now() - pin_down_start;
            pd_time = Math.floor(delta/1000);
            localStorage.setItem("pd_time", pd_time);
            localStorage.setItem("pin_down", pin_down);
            display_pd();
            check_pd_time();
        } 
    }
}, 100); 


function conf(){
  set_title(document.getElementById("cat_name").value);
  set_name(document.getElementById("fight_blue").value,document.getElementById("fight_white").value);
  set_name(document.getElementById("next_fight_blue").value,document.getElementById("next_fight_white").value);
  set_duration(document.getElementById("cat_dur").value);
  reset();
  reset_pin_down();
  displayScore();
}

function conf_key(){
    char_pd_1 = document.getElementById("k_pd_1").value;
    document.getElementById("t_pd_1").innerHTML = char_pd_1;
    char_pd_2 = document.getElementById("k_pd_2").value;
    document.getElementById("t_pd_2").innerHTML = char_pd_2;
    char_toketa = document.getElementById("k_toketa").value;
    document.getElementById("t_toketa").innerHTML = char_toketa;
    
    
    char_100_1 = document.getElementById("k_100_1").value;
    document.getElementById("t_100_1").innerHTML = char_100_1;
    char_m100_1 = document.getElementById("k_m100_1").value;
    document.getElementById("t_m100_1").innerHTML = char_m100_1;
    char_10_1 = document.getElementById("k_10_1").value;
    document.getElementById("t_10_1").innerHTML = char_10_1;
    char_m10_1 = document.getElementById("k_m10_1").value;
    document.getElementById("t_m10_1").innerHTML = char_m10_1;
    char_1_1 = document.getElementById("k_1_1").value;
    document.getElementById("t_1_1").innerHTML = char_1_1;
    char_m1_1 = document.getElementById("k_m1_1").value;
    document.getElementById("t_m1_1").innerHTML = char_m1_1;
    
    
    char_sh_1 = document.getElementById("k_sh_1").value;
    document.getElementById("t_sh_1").innerHTML = char_sh_1;
    char_msh_1 = document.getElementById("k_msh_1").value;
    document.getElementById("t_msh_1").innerHTML = char_msh_1;
    
    
    char_dc_1 = document.getElementById("k_dc_1").value;
    document.getElementById("t_dc_1").innerHTML = char_dc_1;
    
    char_dc_2 = document.getElementById("k_dc_2").value;
    document.getElementById("t_dc_2").innerHTML = char_dc_2;
    
    
    char_sh_2 = document.getElementById("k_sh_2").value;
    document.getElementById("t_sh_2").innerHTML = char_sh_2;
    char_msh_2 = document.getElementById("k_msh_2").value;
    document.getElementById("t_msh_2").innerHTML = char_msh_2;
    
    char_100_2 = document.getElementById("k_100_2").value;
    document.getElementById("t_100_2").innerHTML = char_100_2;
    char_m100_2 = document.getElementById("k_m100_2").value;
    document.getElementById("t_m100_2").innerHTML = char_m100_2;
    char_10_2 = document.getElementById("k_10_2").value;
    document.getElementById("t_10_2").innerHTML = char_10_2;
    char_m10_2 = document.getElementById("k_m10_2").value;
    document.getElementById("t_m10_2").innerHTML = char_m10_2;
    char_1_2 = document.getElementById("k_1_2").value;
    document.getElementById("t_1_2").innerHTML = char_1_2;
    char_m1_2 = document.getElementById("k_m1_2").value;
    document.getElementById("t_m1_2").innerHTML = char_m1_2;
    
    char_gong = document.getElementById("k_gong").value;
    document.getElementById("t_gong").innerHTML = char_gong;
}

set_title("'.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.'");
set_name("Combattant 1","Combattant 2");
set_duration('.$cat_dur.');
reset();
reset_pin_down();
displayScore();
conf_key();

</script>


</html>';
?>
