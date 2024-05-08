<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsRegistration']!=1) {
	header('Location: ./index.php');
}


include 'connectionFactory.php';
$mysqli= ConnectionFactory::GetConnection(); 

//TODO multiple delete en cascade et insert à l'avenant
//TODO insert/delete catégory
//TODO Pay

if($_POST && !empty($_POST['id'])) {
     if ($_POST['id']==-1) {
         $StrId = substr(md5($_POST['nm'].$_POST['sm'].date('Y-m-d:h:m:s')),0,12);
         $stmt = $mysqli->prepare("INSERT INTO TournamentCompetitor  (StrId, Name, Surname, Birth, GenderId, LicenceNumber, GradeId, ClubId) VALUES (?,?,?,?,?,?,?,?)");
         $stmt->bind_param("ssssisii", $StrId, $_POST['nm'], $_POST['sm'], $_POST['bt'], $_POST['gid'], $_POST['lc'], $_POST['grid'], $_POST['cid']);
         $stmt->execute();
         $stmt->close();
     } else {
         $stmt = $mysqli->prepare("UPDATE TournamentCompetitor  SET Name=?, Surname=?, Birth=?, GenderId=?, LicenceNumber=?, GradeId=?, ClubId=? WHERE Id=?");
         $stmt->bind_param("sssisiii", $_POST['nm'], $_POST['sm'], $_POST['bt'], $_POST['gid'], $_POST['lc'], $_POST['grid'], $_POST['cid'], $_POST['id']);
         $stmt->execute();
         $stmt->close();
     }
}
if (!empty($_GET['id']) && $_GET['del']==1) {
     $stmt = $mysqli->prepare("DELETE FROM TournamentCompetitor WHERE Id=?");
     $stmt->bind_param("i", $_GET['id'] );
     $stmt->execute();
	 header('Location: ./listingreg.php');
}


include '_commonBlock.php';

writeHead();

echo'
<body>
    <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 

	     $stmt = $mysqli->prepare("SELECT Id, Surname, Name, Birth, GenderId, ClubId, GradeId,LicenceNumber FROM TournamentCompetitor WHERE Id=?");
      	  
      	  $stmt->bind_param("i", $_REQUEST['id'] );
          $stmt->execute();

          $stmt->bind_result($Id,$sn,$nm,$bt,$gid,$cid,$grid,$licence);
          $stmt->fetch();
	      $stmt->close();
	      if (empty($Id)) {
	          $Id=-1;
	      }
echo '	      
	      <form action="./reg.php" method="post">
	         <span class="ftitle">
	             COMPETITEUR
	         </span>
	           <input type="hidden" name="id" value="'.$Id.'"/>
	        <span class="fitem">
               <span class="label">Nom :</span>
               <input class="inputDate"  type="text" name="sm" value="'.$sn.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Prénom :</span>
               <input class="inputDate"  type="text" name="nm" value="'.$nm.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Date de naissence :</span>
               <input class="inputDate"  type="date" name="bt" value="'.$bt.'" /><br/>
	        </span>
	        
	        <span class="fitem">
               <span class="label">Genre :</span>
               <select name="gid">
                   <option value="1">Féminin</option>
                   <option value="2"';
                   if ($gid==2) {
                      echo ' selected ';
                   }
                   echo'>Masculin</option>
               </select><br/>
	        </span>
	        
	        <span class="fitem">
               <span class="label">Club :</span>
               <select name="cid">';
               $stmt = $mysqli->prepare("SELECT Id, Name FROM TournamentClub ORDER BY Name");
      	        $stmt->execute();
               $stmt->bind_result($ccId,$ccname);
               while ($stmt->fetch()){
                  $sel ='';
                  if ($ccId==$cid) { $sel=' selected ';}
                  echo '<option value="'.$ccId.'" '.$sel.'>'.$ccname.'</option>';
               }
	           $stmt->close();
               
               echo'
                </select><br/>
	        </span>
	        
	        <span class="fitem">
               <span class="label">Grade :</span>
                <select name="grid">';
               $stmt = $mysqli->prepare("SELECT Id, Name FROM TournamentGrade ORDER BY Id");
      	       $stmt->execute();
               $stmt->bind_result($ccId,$ccname);
               while ($stmt->fetch()){
                  $sel ='';
                  if ($ccId==$grid) { $sel=' selected ';}
                  echo '<option value="'.$ccId.'" '.$sel.'>'.$ccname.'</option>';
               }
	           $stmt->close();
               
               echo'
                </select><br/>
	        </span>
	        
	        <span class="fitem">
               <span class="label">Numéro de licence :</span>
               <input class="inputDate"  type="text" name="lc" value="'.$licence.'" /><br/>
	        </span>';
	        
	        if ($Id>0) {
	           echo '
	           <span class="ftitle">
	            CATEGORIE(S)
	           </span>
	           <table>
	              <tr><th>Catégorie</th><th>Poid</th><th>Payé</th><th>Action</th></tr>';
	              
	           $stmt = $mysqli->prepare("SELECT 
	                                            TournamentRegistration.Id, 
	                                            TournamentAgeCategory.Name,
	                                            TournamentAgeCategory.ShortName,
	                                            IFNULL(-MaxWeight, IFNULL(MinWeight,'OPEN')),
	                                            TournamentGender.Name,
	                                            TournamentRegistration.Payed
	                                     FROM TournamentRegistration 
	                                     INNER JOIN TournamentCategory ON TournamentRegistration.CategoryId=TournamentCategory.Id
	                                     INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId
	                                     INNER JOIN TournamentGender ON TournamentGender.Id=TournamentAgeCategory.GenderId
	                                     WHERE CompetitorId =?
	                                     ORDER BY IFNULL(MaxAge,1000)");
	                            
               $stmt->bind_param("i", $_REQUEST['id'] );         
      	       $stmt->execute();
               $stmt->bind_result($trid,$trname,$trshort,$wgt,$gender,$payed);
               while ($stmt->fetch()){
                  echo '<tr><td>'.$trshort.' '.$trname.' '.$gender.'</td>
                            <td>'.$wgt.'</td>
                            <td>'.$payed.'</td>
                            <td><a href="./reg.php?trid='.$trid.'&del=1" class="gridButton" >Supprimer</a>
                                <a href="./reg.php?trid='.$trid.'&pay=1" class="gridButton" >Encaisser</a>
                            </td>
                        </tr>';
               }
	           $stmt->close();    
	              
	           //TODO replace date by the tournament date   
	           $ag = date('Y') - date("Y",  strtotime($bt));        
	           echo'
	           </table>
	           Ajouter:
	            <select name="trid">
	            <option value="-1">--</option>';
	            
	            
	             $stmt = $mysqli->prepare("SELECT 
	                                            TournamentCategory.Id, 
	                                            TournamentAgeCategory.Name,
	                                            TournamentAgeCategory.ShortName,
	                                            IFNULL(-MaxWeight, IFNULL(MinWeight,'OPEN')),
	                                            TournamentGender.Name
	                                     FROM TournamentCategory 
	                                     INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId
	                                     INNER JOIN TournamentGender ON TournamentGender.Id=TournamentAgeCategory.GenderId
	                                     WHERE IFNULL(MaxAge,1000)>? AND IFNULL(MinAge,0)<? AND TournamentAgeCategory.GenderId=?
	                                     ORDER BY  IFNULL(MaxAge,1000)");       
	           
               $stmt->bind_param("iii", $ag,$ag,$gid);         
      	       $stmt->execute();
               $stmt->bind_result($trid,$trname,$trshort,$wgt,$gender);
               while ($stmt->fetch()){
                  echo '<option value="'.$trid.'">'.$trshort.' '.$trname.' '.$gender.' '.$wgt.'</option>';
               }
	           $stmt->close();    
	           echo'
                </select> 
                Payement reçu <input type="checkbox"  name="pay" />
                ';
	        
	        
	        }



echo'
	       <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Enregistrer les modifications">
	               <a class="pgeBtn" href="listingreg.php">Annuler/Fermer</a>
	       </span>
	       </form>
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

