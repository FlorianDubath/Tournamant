<?php

ob_start();
session_name("Tournament");	
session_start();

function wrap_res($rk){
  if ($rk==1){
  return $rk.'<sup>re</sup>';
  } else {
  return $rk.'<sup>e</sup>';
  }
}


  if(empty($_REQUEST['sid'] || strlen($_REQUEST['sid'])!=12) ) {
      	header('Location: ./index.php');
  }
  
  
include '_commonBlock.php';
writeHead();

 include 'connectionFactory.php';
  $mysqli= ConnectionFactory::GetConnection(); 
    $stmt = $mysqli->prepare("SELECT 
                                      TournamentCompetitor.Id, 
                                      StrId, 
                                      Surname, 
                                      TournamentCompetitor.Name, 
                                      Birth, 
                                      TournamentGender.Name, 
                                      TournamentClub.Name, 
                                      CollectVP,
                                      TournamentGrade.Name,
                                      LicenceNumber,
                                      TournamentCompetitor.CheckedIn
                               FROM TournamentCompetitor 
                               INNER JOIN TournamentGender ON TournamentCompetitor.GenderId=TournamentGender.Id
                               INNER JOIN TournamentGrade ON GradeId=TournamentGrade.Id
                               INNER JOIN TournamentClub ON ClubId=TournamentClub.Id
                              WHERE StrId=?
 ");
 
$stmt->bind_param("s", $_REQUEST['sid'] );
$stmt->bind_result( $Id, $strId,$Surname,$Name,$Birth, $Gender, $Club, $GradeCollectVP, $Grade, $licence,$chin);
$stmt->execute();
$stmt->fetch();
$stmt->close();


if (empty($Id)){
      	header('Location: ./reg.php?sid='.$_REQUEST['sid']);
}

$weight_cat = array();

  $stmt2 = $mysqli->prepare("SELECT 
	                                            TournamentCategory.Id, 
	                                            IFNULL(-MaxWeight, IFNULL(MinWeight,'OPEN')),
	                                            AgeCategoryId
	                                     FROM TournamentCategory 
	                                    
	                                     ORDER BY IFNULL(MaxWeight,1000)");    
                                $stmt2->execute();
                                $stmt2->bind_result($ccc_id, $ccc_weight,$age_cat_ref);
                                while ($stmt2->fetch()){ 
                                     if (!key_exists($age_cat_ref, $weight_cat) ){
                                          $weight_cat[$age_cat_ref]= array();
                                     }
                                      $weight_cat[$age_cat_ref][$ccc_id] = $ccc_weight;
                                      
                                }
      	                        $stmt2->close();
function getWeights($dict, $age_cat,$selection) {
    $res="";
    foreach ($dict[$age_cat] as $kid => $dwg) {
        $res=$res.'<option value="'.$kid.'" ';
        if ($selection==$kid) {
            $res=$res.'selected';
        }
        $res=$res.'>'.$dwg.'</option>';
        
    }
    
    return $res;
    
}


echo'
<body>
    <div class="f_cont">';
    
    
    
writeBand();
echo'        
       <div class="cont_l">
         <div class="h">'; 
if ( ! empty($_SESSION['_UserId'])) {
    if ($_SESSION['_IsWelcome']==1 || $_SESSION['_IsRegistration']==1 || $_SESSION['_IsMainTable']==1) {
       echo '<span class="item_action"><span class="ftitle">ACCUEIL</span>';
       if (!empty($Id) && $Id>0) {
            
            if (!empty($_POST['pr'])&&$_POST['pr']==1) { 
              $stmt = $mysqli->prepare("UPDATE TournamentCompetitor SET CheckedIn=1, CheckedInBy=? WHERE Id=?");
              $stmt->bind_param("ii",$_SESSION['_UserId'] , $Id );
              $stmt->execute();
	          $stmt->close();
              $chin=1;
            } 
            
             if (!empty($_POST['py'])&&$_POST['py']==1&&!empty($_POST['trid'])) { 
              $stmt = $mysqli->prepare("UPDATE TournamentRegistration SET Payed=1 WHERE Id=?");
              $stmt->bind_param("i", $_POST['trid'] );
              $stmt->execute();
	          $stmt->close();
              $chin=1;
            } 
            
            
            echo '<span class="fsubtitle">Informations sur le participant:</span><a class="pgeBtn" href="reg.php?id='.$Id.'">Corriger des données</a> <br/> ';
            
            echo '<span class="fsubtitle">Payement:</span>';
            $stmt = $mysqli->prepare("SELECT   TournamentRegistration.Id, 
                                               TournamentAgeCategory.Name,
	                                           TournamentAgeCategory.ShortName,
	                                           TournamentGender.Name,
	                                           TournamentRegistration.Payed
	                                     FROM TournamentRegistration
	                                     INNER JOIN TournamentCategory ON TournamentRegistration.CategoryId=TournamentCategory.Id
	                                     INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId
	                                     INNER JOIN TournamentWeighting ON TournamentWeighting.AgeCategoryId = TournamentAgeCategory.Id 
	                                     INNER JOIN TournamentGender ON TournamentGender.Id=TournamentAgeCategory.GenderId 
	                                     WHERE CompetitorId =?
	                                     ORDER BY TournamentWeighting.WeightingEnd");
               $stmt->bind_param("i", $Id);         
      	       $stmt->execute();
               $stmt->bind_result($trid,$cat_n,$cat_sn,$cat_gen,$payed);
               while ($stmt->fetch()){
                    echo ' &nbsp; &nbsp; Catégorie '.$cat_sn.' '.$cat_n.' '.$cat_gen.' :';
                    if ($payed==1) {
                        echo ' Payement reçu<br/>';
                    } else {
                        echo '<form action="./card.php" method="post">
                             <input type="hidden" name="sid" value="'.$strId.'"/>
                             <input type="hidden" name="py" value="1"/>
                             <input type="hidden" name="trid" value="'.$trid.'"/> 
                             A payer! 
                             <input class="pgeBtn"  type="submit" value="Encaisser"/> 
                            </form> <br/>';
                    }
               }
            
            echo'
            
            <span class="fsubtitle">Présence et carte de combatant:</span>';
            
            
            if ( $chin==0) {
                echo '<form action="./card.php" method="post">
                             <input type="hidden" name="sid" value="'.$strId.'"/>
                             <input type="hidden" name="pr" value="1"/>
                             <input class="pgeBtn" type="submit" value="Confirmer la présence et remise de la carte"/> 
                             </form><br/> ';
            } else {
                echo 'Présence confirmée / carte remise au participant.  ';
            }
            

            
            
       } 
        echo '</span>'; 
    }
    
    if (($_SESSION['_IsWeighting']==1 || $_SESSION['_IsMainTable']==1) && $Id>0 && $chin>0) {
         if (!empty($_POST['wgok'])&&$_POST['wgok']==1 && !empty($_POST['wgc']) && !empty($_POST['trid'])) { 
              $stmt = $mysqli->prepare("UPDATE TournamentRegistration SET WeightChecked=1, CategoryId=?, WeightCheckedBy=? WHERE Id=?");
              $stmt->bind_param("iii", $_POST['wgc'], $_SESSION['_UserId'], $_POST['trid']);
              $stmt->execute();
	          $stmt->close();
         } 
    
    
       echo '<span class="item_action"><span class="ftitle">PESEE</span>Catégorie:';
              $stmt = $mysqli->prepare("SELECT 
	                                            TournamentRegistration.Id, 
	                                            TournamentAgeCategory.Name,
	                                            TournamentAgeCategory.ShortName,
	                                            TournamentGender.Name,
	                                            IFNULL(-MaxWeight, IFNULL(MinWeight,'OPEN')),
	                                            TournamentCategory.Id,
	                                            TournamentAgeCategory.Id,
	                                            TournamentWeighting.WeightingBegin,
	                                            TournamentWeighting.WeightingEnd,
	                                            WeightChecked
	                                     FROM TournamentRegistration 
	                                     INNER JOIN TournamentCategory ON TournamentRegistration.CategoryId=TournamentCategory.Id
	                                     INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId
	                                     INNER JOIN TournamentWeighting ON TournamentWeighting.AgeCategoryId = TournamentAgeCategory.Id 
	                                     INNER JOIN TournamentGender ON TournamentGender.Id=TournamentAgeCategory.GenderId
	                                     WHERE CompetitorId =?
	                                     ORDER BY TournamentWeighting.WeightingEnd");
	                            
               $stmt->bind_param("i", $Id);         
      	       $stmt->execute();
               $stmt->bind_result($tr_to_w_id, $cat_n,$cat_sn,$cat_gen, $dpw, $w_to_confirm, $age_cat_id, $weighting_begin, $weighting_end,$wck);
               while ($stmt->fetch()){
                    if ($wck==1) {
                           echo '<div>&nbsp; &nbsp; Pesée effectuée pour la catégorie '.$cat_sn.' '.$cat_n.' '.$cat_gen.' poid:'.$dpw.'</div>';
                    } else {
                    
                    echo '<form action="./card.php" method="post">
                             <input type="hidden" name="sid" value="'.$strId.'"/>
                             <input type="hidden" name="trid" value="'.$tr_to_w_id.'"/>
                             <input type="hidden" name="wgok" value="1"/>
                            <span> &nbsp; &nbsp; Pesée pour la catégorie '.$cat_sn.' '.$cat_n.' '.$cat_gen.' poid:<select name="wgc">';
                               
                             echo getWeights($weight_cat, $age_cat_id,$w_to_confirm) ;   
                             echo '</select>';
                             if (date($weighting_end) > date("Y-m-d H:i:s")  || $_SESSION['_IsMainTable']==1) {
                                echo' <input class="pgeBtn"  type="submit" value="Poid vérifié">';
                            }
                            echo '</span>
                          </form>';
                    }
               
               
               }      
      	       $stmt->close();
      	       
        echo '</span>'; 
    }
    
    
}  

echo '
<div class="card">
 <span class="ftitle">
	        COMPETITEUR ';
	        
if ( ! empty($_SESSION['_UserId'])) {	        
	        echo '<a class="pgeBtn" href="" onclick="makePDF(\'carte_'.$strId.'.pdf\');">PDF</a>';
}	    
    
	        echo'
	         </span><br/>
	           <input type="hidden" name="id" value="'.$Id.'"/>
	        <span class="fitem">
               <span class="label">Identifiant :</span>
               <input class="inputDate"  type="text" name="sid" value="'.$strId.'" readonly="readonly" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Nom :</span>
               <input class="inputDate"  type="text" name="sm" value="'.$Surname.'" readonly="readonly" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Prénom :</span>
               <input class="inputDate"  type="text" name="nm" value="'.$Name.'" readonly="readonly"/><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Date de naissence :</span>
               <input class="inputDate"  type="date" name="bt" value="'.$Birth.'" readonly="readonly"/><br/>
	        </span>
	        
	        <span class="fitem">
               <span class="label">Genre :</span>
               <input class="inputDate"  type="text" name="bt" value="'.$Gender.'" readonly="readonly"/><br/>
	        </span>
	        
	        <span class="fitem">
               <span class="label">Club :</span>
               <input class="inputDate"  type="text" name="bt" value="'.$Club.'" readonly="readonly"/><br/>
	        </span>
	        
	        <span class="fitem">
               <span class="label">Grade :</span>
               <input class="inputDate"  type="text" name="bt" value="'.$Grade.'" readonly="readonly"/><br/>
	        </span>
	        
	        <span class="fitem">
               <span class="label">N° licence :</span>
               <input class="inputDate"  type="text" name="lc" value="'.$licence.'" readonly="readonly" /><br/>
	        </span>';
 

echo'
   <span class="ftitle">
	         CATEGORIE(S)
	           </span>';
	              
	           $stmt = $mysqli->prepare("SELECT 
	                                            TournamentRegistration.Id, 
	                                            TournamentAgeCategory.Name,
	                                            TournamentAgeCategory.ShortName,
	                                            IFNULL(-MaxWeight, IFNULL(MinWeight,'OPEN')),
	                                            TournamentGender.Name,
	                                            TournamentRegistration.Payed,
	                                            TournamentCompetitor.CheckedIn,
	                                            TournamentRegistration.WeightChecked,
	                                            TournamentWeighting.WeightingBegin,
	                                            TournamentWeighting.WeightingEnd,
	                                            ActualCategory.Name,
	                                            ActualCategoryResult.RankId
	                                     FROM TournamentRegistration 
	                                     INNER JOIN TournamentCategory ON TournamentRegistration.CategoryId=TournamentCategory.Id
	                                     INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId
	                                     INNER JOIN TournamentWeighting ON TournamentWeighting.AgeCategoryId = TournamentAgeCategory.Id 
	                                     INNER JOIN TournamentGender ON TournamentGender.Id=TournamentAgeCategory.GenderId
	                                     INNER JOIN TournamentCompetitor ON TournamentCompetitor.Id=TournamentRegistration.CompetitorId
	                                     LEFT OUTER JOIN ActualCategory on ActualCategory.CategoryId=TournamentRegistration.CategoryId or ActualCategory.Category2Id=TournamentRegistration.CategoryId
	                                     LEFT OUTER JOIN ActualCategoryResult ON ActualCategoryResult.ActualCategoryId=ActualCategory.Id AND ActualCategoryResult.Competitor1Id=TournamentRegistration.CompetitorId
	                                     WHERE CompetitorId =?
	                                     ORDER BY TournamentWeighting.WeightingEnd");
	                            
               $stmt->bind_param("i", $Id);         
      	       $stmt->execute();
               $stmt->bind_result($trid,$trname,$trshort,$wgt,$gender,$payed,$checkedin,$weight_checked, $weighting_begin, $weighting_end, $acname, $acrk);
               $cat_regist = array();
               while ($stmt->fetch()){
                   $pay_cls = "c_p_todo";
                   if ($payed==1) {
                      $pay_cls  = "c_p_done";
                   } 
                   $acc_cls = "c_p_todo";
                   if ($checkedin==1) {
                      $acc_cls  = "c_p_done";
                   }
                    echo'<span class="cprogress">
                         <span class="c_p_title">'.$trshort.' '.$trname.' '.$gender.' '.$wgt;
                         
                    if (!empty($acname) && $acname!=$trshort.' '.$trname.' '.$gender.' '.$wgt){
                        echo'('.$acname.')';
                    }
                         
                    echo'</span>
                         <span class="c_p_element c_p_done"> Inscription </span>
                         <span class="c_p_element '.$pay_cls.'"> Payement </span>
                         <span class="c_p_element '.$acc_cls.'"> Annonce à l\'acceuil </span>';
                   
                  $w_end = new DateTime($weighting_end);
                  $w_start = new DateTime($weighting_begin);
                  $now = new DateTime();
                  $interval_start = $now->diff($w_start);
                  $interval_end = $now->diff($w_end);
                 
                  
                  if ($now < $w_start){   // weighting not yet opened
                       echo' <span class="c_p_element c_p_notavailable"> Pesée </span>';
                       if ( $interval_start->days>0){
                           echo' <span class="c_p_element c_p_info"> La pesée ouvre dans '.$interval_start->days.' jour(s)</span>'; 
                       } else {
                           echo' <span class="c_p_element c_p_info"> La pesée ouvre dans '.$interval_start->h.'h et '.$interval_start->m.' min</span>'; 
                       }
                       
                  } else {   // weighting already opened
                      if (!$weight_checked) {
                          if ($now < $w_end){
                              echo '<span class="c_p_element c_p_todo"> Pesée </span>';
                              echo '<span class="c_p_element c_p_info"> Il vous reste '.$interval_end->h.'h et '.$interval_end->m.' min pour vous peser</span>'; 
                          } else {
                              echo '<span class="c_p_element c_p_missed"> Pesée </span>';
                              echo '<span class="c_p_element c_p_info"> Désolé vous avez raté la pesée...</span>';
                          }
                      } else {
                          echo '<span class="c_p_element c_p_done"> Pesée </span>';
                          
                          if (!empty($acrk)) {
                               echo '<span class="c_p_element result_'.$acrk.'"> Classement : '.wrap_res($acrk).' </span>';
                          }
                          
                      }
                      
                  
                  }
                         
                  
                  echo '</span>';
                  array_push($cat_regist, ["name"=>$trshort.' '.$trname.' '.$gender.' '.$wgt,"end_wgt"=>$w_end]);
               }
	           $stmt->close();    
	
	// Listing combats
	      
	       $stmt = $mysqli->prepare(" SELECT 
	                                      ActualCategory.Name,
	                                      ActualCategory.IsCompleted,
	                                      G1.CollectVP + G2.CollectVP,
	                                      TC1.Id,
	                                      TC1.Name,
                                          TC1.Surname,
                                          G1.Id,
                                          G1.Name,
                                          C1.Name,
                                          pv1,
	                                      TC2.Id,
	                                      TC2.Name,
                                          TC2.Surname,
                                          G2.Id,
                                          G2.Name,
                                          C2.Name,
                                          pv2
	                                  FROM Fight
	                                  INNER JOIN TournamentCompetitor TC1 ON TournamentCompetitor1Id = TC1.Id
	                                  INNER JOIN TournamentCompetitor TC2 ON TournamentCompetitor2Id = TC2.Id
	                                  INNER JOIN TournamentGrade G1 ON TC1.GradeId = G1.Id
	                                  INNER JOIN TournamentGrade G2 ON TC2.GradeId = G2.Id
	                                  INNER JOIN TournamentClub C1 ON C1.Id=TC1.ClubId
	                                  INNER JOIN TournamentClub C2 ON C2.Id=TC2.ClubId
	                                  INNER JOIN ActualCategory ON Fight.ActualCategoryId=ActualCategory.Id
	                                  
	                                  WHERE (TournamentCompetitor1Id = ? OR TournamentCompetitor2Id = ?) AND pv1 IS NOT NULL
	                                  ORDER BY ActualCategory.Name, Fight.Id");
	                            
           $stmt->bind_param("ii", $Id, $Id);         
  	       $stmt->execute();
           $stmt->bind_result($cat_nm,$completed,$ppv,$c1_id,$c1_name,$c1_surname,$c1_grade_id,$c1_grade,$c1_club,$pv1, $c2_id,$c2_name,$c2_surname,$c2_grade_id,$c2_grade,$c2_club,$pv2);
           
           $fight_result = '<span class="ftitle">COMBATS</span>';
           $fight_number=0;
           $fight_nb_pv=0;
           $fight_win=0;
           $fight_win_pv=0;
           $fight_pv=0;
           $cur_cat='';
           while ($stmt->fetch()){
               if ($cur_cat!=$cat_nm) {
                  if ($cur_cat!='') {
                     $fight_result =$fight_result.'</table>';
                  }
                  $fight_result =$fight_result. '<span>'.$cat_nm;
                  if ($completed==0) {
                     $fight_result =$fight_result. ' En cours...';
                  }
                  $fight_result =$fight_result. '</span><table class="wt t4">
                                                   <tr class="tblHeader">  
                                                   <th>Résultat</th>
                                                   <th>Oposant</th>
                                                   <th>Ceinture</th>
                                                   <th>PV</th>
                                                   </tr>';
                 $cur_cat=$cat_nm;
                   
               }
               $fight_number+=1;
               if ($ppv==2){
                   $fight_nb_pv+=1;
               }
               $op='';
               $blt='';
               $pv_c='-';
               $vic = '';
               if ($c1_id==$Id) {
                  $op=$c2_surname.' '.$c2_name;
                  $blt=$c2_grade;
                  if ($pv1>0){
                     $vic = "Victoire contre";  
                     $fight_win += 1;
                     if ($ppv==2) {
                         $fight_pv += $pv1;
                         $fight_win_pv+=1;
                         $pv_c = $pv1;
                     } else {
                         $pv_c = '('.$pv1.')';
                     }
                  } else {
                     $vic = "Défaite contre"; 
                  }
                  
               } else {
                  $op=$c1_surname.' '.$c1_name;
                  $blt=$c1_grade;
                  if ($pv2>0){
                     $vic = "Victoire contre"; 
                     $fight_win+=1; 
                     if ($ppv==2) {
                         $fight_pv += $pv2;
                         $fight_win_pv+=1;
                         $pv_c = $pv2;
                     } else {
                         $pv_c = '('.$pv2.')';
                     }
                  } else {
                     $vic = "Défaite contre"; 
                  }
               }
               $fight_result =  $fight_result.'<tr><td>'.$vic.'</td><td>'.$op.'</td><td>'.$blt.'</td><td>'. $pv_c.'</td></tr>';
           }
           $stmt->close();
            $fight_result =  $fight_result.'</table>';
           
           if ($fight_number>0) {
               echo $fight_result;
               if ($GradeCollectVP) {
                   echo 'Nombre de combat(s) :'.$fight_number.'<br/>';
                   echo 'Nombre de combat(s) contre des 1Kyu et Dan :'.$fight_nb_pv.'<br/>';
                   echo 'Nombre de victoire(s) :'.$fight_win.'<br/>';
                   echo 'Nombre de victoire(s) contre des 1Kyu et Dan :'.$fight_win_pv.'<br/>';
                   echo 'Nombre de points valeurs collectés :'.$fight_pv.'<br/>';
               } 
           }
           
echo '
           </div>     
           </div>  
        </div>   
     </div>
     <div id="print" style="display:none;">
         <div id="qrcode"></div>
         <div class="url">https://'.$_SERVER['HTTP_HOST'].'/card.php&sid='.$strId.'<div>
         <img id="logo_l" src="css/Logo_ACG_JJJ_light.png"></img>
    </div>
</body>

<script type="text/javascript" src="js/qrcode.js"></script>
<script type="text/javascript" src="js/jspdf.min.js"></script>

<script type="text/javascript">
var qrcode = new QRCode(document.getElementById("qrcode"), {
	width : 250,
	height : 250
});

function wrapImgData(img){
    if (img.substring(0, 4) == "url("){
        img=img.substring(4,img.length-5);
        if (img.substring(0, 1) == "\""){
            img=img.substring(1,img.length-2)
        }

    }
    return img;
}

function getImgData(id) {
    var c = document.createElement("canvas");
    var img = document.getElementById(id);
    c.height = img.naturalHeight;
    c.width = img.naturalWidth;
    var ctx = c.getContext("2d");

    ctx.drawImage(img, 0, 0, c.width, c.height);
    return c.toDataURL();
}


qrcode.makeCode("http://'.$_SERVER['HTTP_HOST'].'/card.php&sid='.$strId.'");

function makePDF(pdf_name) {
  var doc = new jsPDF({format: \'a6\',orientation:\'l\'});
  
  var imgAddData = wrapImgData(getImgData("logo_l"));
  doc.addImage(imgAddData, "PNG", 40, 15, 70, 70);
   
  var imgAddData = wrapImgData(document.getElementById("qrcode").getElementsByTagName("img")[0].src);
  doc.addImage(imgAddData, "PNG", 100, 25, 40, 40);
  //doc.setFontSize(6).setFont("helvetica", "normal");
  //doc.textWithLink("https://'.$_SERVER['HTTP_HOST'].'/card.php&sid='.$strId.'", 100, 70, {url: "https://'.$_SERVER['HTTP_HOST'].'/card.php&sid='.$strId.'"});

  doc.setFontSize(16).setFont("helvetica", "bold");
  ';
  
  $stmt = $mysqli->prepare("SELECT Name, TournamentStart,TournamentEnd FROM TournamentVenue order by Id desc limit 1");
  $stmt->execute();
  $stmt->bind_result( $trName,$TournamentStart,$TournamentEnd);
  $stmt->fetch();
  $stmt->close();
  
  $date_txt = formatDate($TournamentStart);
  if ($TournamentStart!=$TournamentEnd) {
	          $date_txt =  $date_txt.' - '. formatDate($TournamentEnd);
   }
  
  
  echo'
  
  doc.text("'.$trName.'", doc.internal.pageSize.width/2, 12, {align: \'center\'});
  doc.setFontSize(16).setFont("helvetica", "normal");
  doc.text("'.$date_txt.'", doc.internal.pageSize.width/2, 20, {align: \'center\'});
  
  doc.setFontSize(14).setFont("helvetica", "normal");
  
  doc.text("Compétiteur :",10 ,30) ; 
  doc.setFont("helvetica", "bold");
  doc.text("'.$Surname.' '.$Name.' ",15 ,40) ; 
  doc.setFont("helvetica", "normal");
  doc.text("Club :",10 ,50) ;
  doc.setFontSize(11).setFont("helvetica", "bold");
  doc.text("'.$Club.' ",15 ,60) ;
  doc.setFontSize(14).setFont("helvetica", "normal");
  doc.text("Catégorie(s):",10 ,70);
  doc.setFont("helvetica", "bold"); ';
  
  $position=80;
  $step=10;
  foreach ($cat_regist as $ctr){
             echo 'doc.text("'.$ctr["name"].'",15 ,'.$position.');
                   doc.text("Pesée => '.$ctr["end_wgt"]->format('j/m H\hi').'",94 ,'.$position.');';
             $position=$position+$step;
         }
  
  echo'
  
  doc.save(pdf_name);
}

</script>
</html>';

?>

