<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsMainTable']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';
include '_commonBlock.php';

$mysqli= ConnectionFactory::GetConnection();

if (isset($_POST['cid'])){
    $stmt = $mysqli->prepare("UPDATE ActualCategoryResult SET  RankId=?, Medal=? WHERE ActualCategoryId=? AND Competitor1Id=?");
    $stmt->bind_param("iiii", $_POST['rank'], $_POST['med'], $_POST['acatid'], $_POST['cid'] );
    $stmt->execute();
    $stmt->close();   
    header('Location: ./cat.php?cid='.$_REQUEST['catid']); 
     
}




writeHead();

echo'
<body>
    <div class="f_cont">';

echo'        
       <div class="cont_l">
         <div class="h">'; 
  
writeBand(); 

  $stmt = $mysqli->prepare("select
                                 ActualCategory.Id,
                                 ActualCategory.Name,
                                 IsCompleted
                             from ActualCategory
                             WHERE ActualCategory.Id=?
                           ");
                             
     $stmt->bind_param("i", $_REQUEST['acatid'], );
     $stmt->bind_result( $actual_cat_Id, $ac_name,$cat_completed);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close();       
         

echo ' 
            <span class="h_title">
               MODIFIER UN RESULTAT DE LA CATEGORIE '.$ac_name.'
            </span>
             <span class="btnBar"> 
                   <a class="pgeBtn" href="cat.php?cid='.$_REQUEST['catid'].'" title="Annuler" >Annuler</a>
               </span>
            <span class="h_txt">';
            
            
             $stmt = $mysqli->prepare("select distinct
                                 RankId,
                                 Medal,
                                 TournamentCompetitor.Id,
                                 TournamentCompetitor.Surname,
                                 TournamentCompetitor.Name,  
                                 TournamentClub.Name
                             FROM ActualCategoryResult
                             INNER JOIN TournamentCompetitor on TournamentCompetitor.Id =  Competitor1Id
                             INNER JOIN TournamentClub ON ClubId=TournamentClub.Id
                             WHERE  ActualCategoryId=?  AND TournamentCompetitor.Id=?");
     $stmt->bind_param("ii", $_REQUEST['acatid'], $_REQUEST['cid'] );
     $stmt->bind_result( $rk, $Medal, $cid, $Surname, $Name, $Club);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close();   
     
     echo ' 
    <form action="changeRes.php" method="post"> 
     <input type="hidden" name="acatid" value="'.$_REQUEST['acatid'].'" />
     <input type="hidden" name="cid" value="'.$_REQUEST['cid'].'" />
     <input type="hidden" name="catid" value="'.$_REQUEST['catid'].'" />
     <table class="wt t4">
      <tr class="tblHeader">
      <th>Médaille</th>
      <th>Classement</th>
      <th>Nom Prénom</th>
      <th>Club</th>
      </tr>
      <tr>
        <td>
             <select name="med" >
              <option value="1"'; if ($Medal==1){echo "selected";}  echo'>Or</option>
              <option value="2"'; if ($Medal==2){echo "selected";}  echo'>Argent</option>
              <option value="3"'; if ($Medal==3){echo "selected";}  echo'>Bronze</option>
              <option value="0"'; if ($Medal==0){echo "selected";}  echo'>-</option>
            </select>
        </td>  
        <td><input type="number" min="1" max="100" name="rank" value="'.$rk.'"></td>
        <td>'.$Name.' '.$Surname.' </td>
        <td>'. $Club.'</td>
      </tr>
      </table> 
      <span class="btnBar"> 
                   <input class="pgeBtn" type="submit" title="Enregistrer" value="Enregistrer">
                   <a class="pgeBtn" href="cat.php?cid='.$_REQUEST['catid'].'" title="Annuler" >Annuler</a>
               </span>
      </form>
    </span>
               
   </div></div></div></body></html>';
 
	      
	      
?>
