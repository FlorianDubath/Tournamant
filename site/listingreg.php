<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsRegistration']!=1 && $_SESSION['_IsWelcome']!=1 && $_SESSION['_IsMainTable']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';

include '_commonBlock.php';


$fcid = (int)$_GET['fcid'];
$fnm = $_GET['fnm'];

$where_clause = '';

if($fcid>0){
   if($fnm && $fnm!=''){
      $where_clause = "WHERE ClubId=".$fcid." AND CONCAT(Surname,' ',TournamentCompetitor.Name) LIKE '%".$fnm."%'";
    } else {
      $where_clause = "WHERE ClubId=".$fcid;
    }
} else {
   if($fnm && $fnm!=''){
      $where_clause = "WHERE CONCAT(Surname,' ',TournamentCompetitor.Name) LIKE '%".$fnm."%'";
    } 
}


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
               GESTION DES INSCRIPTIONS
            </span>
             <span class="btnBar"> 
                   <a class="pgeBtn" href="index.php" title="Fermer" >Fermer</a>';
          if ($_SESSION['_IsRegistration']==1 || $_SESSION['_IsWelcome']==1 || $_SESSION['_IsMainTable']==1) {
              echo'
                   <a class="pgeBtn" target="_blanck" href="Inscriptions.xltx" title="Fichier Club" >Fichier Club</a>
                   <a class="pgeBtn" href="cards.php" title="Cartes" >Générer les cartes</a>';
          }
                   echo'
               </span>
            <span class="h_txt">
           
	             <form class="filter" action="./listingreg.php" method="get">
	                <span  class="h_title">Filtrer les inscrits: </span>
	                
	                Filtre sur "Nom Prénom" : <input type="text" name="fnm" value="'.$fnm.'"/><br/>
	                Filtre sur le club : <select name="fcid"><option value="-1" >Tous</option> <option style="font-size: 1pt; background-color: #000000;" disabled>&nbsp;</option>';
               $stmt = $mysqli->prepare("SELECT Id, Name FROM TournamentClub ORDER BY Name");
      	        $stmt->execute();
               $stmt->bind_result($ccId,$ccname);
               while ($stmt->fetch()){
                  $sel ='';
                  if ($ccId==$fcid) { $sel=' selected ';}
                  echo '<option value="'.$ccId.'" '.$sel.'>'.$ccname.'</option>';
               }
	           $stmt->close();
               
               echo'
                </select>
                 <span class="btnBar"> 
	                <input class="pgeBtn" type="submit" value="Appliquer">
	                </span>
	             </form>
	             
	             <span class="btnBar"> ';
          if ($_SESSION['_IsRegistration']==1 || $_SESSION['_IsWelcome']==1 || $_SESSION['_IsMainTable']==1) {
              echo'
                       <a class="pgeBtn"  href="pays.php" title="Payements Groupés">Payements Groupés</a>
	               <a class="pgeBtn"  href="regs.php" title="Inscriptions Groupées">Inscriptions Groupées</a>';
          }
                   echo'
	              
	               <a class="pgeBtn"  href="reg.php?id=0" title="Nouvel Inscrit">Nouvel Inscrit</a>
	               
	             </span>
      <table class="wt t4">
      <tr class="tblHeader">
      <th>Club</th>
      <th>Nom, Prénom</th>
      <th>Date de naissence</th>
      <th>Genre</th>
      <th>Grade</th>
      <th>Licence</th>
      <th>Catégorie(s)</th>
      <th>Payé</th>
     
      <th >Action</th>
      </tr>';
     $stmt = $mysqli->prepare("SELECT 
                                      TournamentCompetitor.Id, 
                                      TournamentCompetitor.StrId, 
                                      Surname, 
                                      TournamentCompetitor.Name, 
                                      Birth, 
                                      TournamentGender.Name, 
                                      TournamentClub.Name, 
                                      TournamentClub.Contact,
                                      TournamentGrade.Name,
                                      LicenceNumber, 
                                      TournamentAgeCategory.ShortName, 
                                      IFNULL(Payed,0)
                                       
                               FROM TournamentCompetitor 
                               LEFT OUTER JOIN TournamentRegistration ON TournamentRegistration.CompetitorId = TournamentCompetitor.Id 
                               LEFT OUTER JOIN TournamentCategory ON TournamentCategory.Id = TournamentRegistration.CategoryId
                               LEFT OUTER JOIN TournamentAgeCategory ON TournamentCategory.AgeCategoryId = TournamentAgeCategory.Id
                               INNER JOIN TournamentGender ON TournamentCompetitor.GenderId=TournamentGender.Id
                               INNER JOIN TournamentGrade ON GradeId=TournamentGrade.Id
                               INNER JOIN TournamentClub ON ClubId=TournamentClub.Id
                               ".$where_clause."
                               ORDER BY TournamentClub.Name, Surname, TournamentCompetitor.Name, ShortName
 ");
     $stmt->bind_result( $Id, $strId,$Surname,$Name,$Birth, $Gender, $Club, $ContactClub, $Grade, $licence, $cat, $payed);
     $stmt->execute();
     
     $cur_id=-1;
     $cur_strId='';
     $cur_club='';
     $cur_cc='';
     $cur_Surname='';
     $cur_Name='';
     $cur_birth='';
     $cur_gend='';
     $cur_grad='';
     $cur_lic='';
     $tot_pay=0;
     $tot_cat=0;
     $cat_l='';
     
     while ($stmt->fetch()){
     $date='';
     if (isset($Birth)){
       $d1=new DateTime($Birth);
       $date=$d1->format('d/m/Y');
     }
     
      if ($cur_id!=$Id) {
       if ( $cur_id>0) {
       echo' <tr >
     <td >';
     
     if ($cur_cc==''){
         echo $cur_club;
     } else {
         
         echo '
                    <a class="btn_info" onclick="toggleClass(document.getElementById(\'pop_'.$cur_id.'\'),\'pop_hide\');">'. $cur_club.'</a>
                    <span class="pop_back pop_hide" Id="pop_'.$cur_id.'">
	               <span class="popcont_info">
	                   <span class="pop_tt">CONTACT </span> 
		             '. $cur_club.'<br/>
		            '.$cur_cc.'
		           
		 	    <span class="btnBar"> 
				 <a class="pgeBtn" onclick="toggleClass(document.getElementById(\'pop_'.$cur_id.'\'),\'pop_hide\');">Fermer</a>
			     </span
		          </span>
                     </span>';
     
     }
     
     
     
     echo '</td>
      <td class="rt">'.$cur_Surname.' '.$cur_Name.'</td>
      <td class="rt">'.$cur_birth.'</td>
      <td class="rt">'.$cur_gend.'</td>
      <td class="rt">'.$cur_grad.'</td>
      <td class="rt">'.$cur_lic.'</td>
      <td class="rt">'.substr($cat_l,3).'</td>
      <td class="rt">'.$tot_pay.'/'.$tot_cat.'</td>
      <td class="rt">
         <form action="./reg.php" method="post">
             <input type="hidden" name="id" value="'.$cur_id.'"/>
             <input type="hidden" name="del" value="1"/>
             <input class="gridButton" type="submit" value="Supprimer"/> 
         </form>
         <a href="./reg.php?id='.$cur_id.'" class="gridButton" >Modifier</a>
         <a href="./card.php?sid='.$cur_strId.'" class="gridButton" >Carte</a>
       </td>
      </tr>';
      }
      
         $cur_id=$Id;
         $cur_strId = $strId;
         $cur_club=$Club;
         $cur_cc=$ContactClub;
         $cur_Surname=$Surname;
         $cur_Name=$Name;
         $cur_birth=$date;
         $cur_gend=$Gender;
         $cur_grad=$Grade;
         $cur_lic=$licence;
         $tot_pay=0;
         $tot_cat=0;
         $cat_l='';
      }
      
      $tot_pay+=$payed;
      $tot_cat+=1;
      $cat_l=$cat_l.' / '.$cat;
     
     
    
     }
     if ( $cur_id>0) {
      echo' <tr >
     <td >'. $cur_club.'</td>
      <td class="rt">'.$cur_Surname.' '.$cur_Name.'</td>
      <td class="rt">'.$cur_birth.'</td>
      <td class="rt">'.$cur_gend.'</td>
      <td class="rt">'.$cur_grad.'</td>
      <td class="rt">'.$cur_lic.'</td>
      <td class="rt">'.substr($cat_l,3).'</td>
      <td class="rt">'.$tot_pay.'/'.$tot_cat.'</td>
      <td class="rt">
         <form action="./reg.php" method="post">
             <input type="hidden" name="id" value="'.$cur_id.'"/>
             <input type="hidden" name="del" value="1"/>
             <input class="gridButton" type="submit" value="Supprimer"/> 
         </form>
         <a href="./reg.php?id='.$cur_id.'" class="gridButton" >Modifier</a>
         <a href="./card.php?sid='.$cur_strId.'" class="gridButton" >Carte</a>
       </td>
      </tr>';
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
