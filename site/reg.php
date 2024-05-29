<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsRegistration']!=1 && $_SESSION['_IsMainTable']!=1 && $_SESSION['_IsWelcome']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';
$mysqli= ConnectionFactory::GetConnection(); 

  if(!empty($_REQUEST['sid'])) {
  
    if (strlen($_REQUEST['sid'])!=12) {
      	header('Location: ./index.php');
    } else {
        
        $stmt = $mysqli->prepare("SELECT Id FROM TournamentCompetitor  WHERE StrId=?");
        $stmt->bind_param("s", $_REQUEST['sid']);
        $stmt->bind_result($nsId);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();
        if (!empty($nsId)){
            header('Location: ./index.php');
        }
     }
  }





$New_Id=$_REQUEST['id'];
 $message='';
if($_POST && !empty($_POST['id'])) {
     if ($_POST['id']==-1) {
         
         $StrId = substr(md5($_POST['nm'].$_POST['sm'].date('Y-m-d:h:m:s')),0,12);
         
         if(!empty($_REQUEST['sid'])) {
               $StrId = $_REQUEST['sid'];
         }
         
         $stmt = $mysqli->prepare("INSERT INTO TournamentCompetitor  (StrId, Name, Surname, Birth, GenderId, LicenceNumber, GradeId, ClubId) VALUES (?,?,?,?,?,?,?,?)");
         $stmt->bind_param("ssssisii", $StrId, $_POST['nm'], $_POST['sm'], $_POST['bt'], $_POST['gid'], $_POST['lc'], $_POST['grid'], $_POST['cid']);
         $stmt->execute();
         $stmt->close();
         
         $stmt = $mysqli->prepare("SELECT Id FROM TournamentCompetitor WHERE StrId=?");
         $stmt->bind_param("s", $StrId);
         $stmt->execute();        
         $stmt->bind_result($New_Id);
         $stmt->fetch();
	     $stmt->close();
	     
	     $message='Enregistrement effectué';
         
         
     } else {
         $stmt = $mysqli->prepare("UPDATE TournamentCompetitor  SET Name=?, Surname=?, Birth=?, GenderId=?, LicenceNumber=?, GradeId=?, ClubId=? WHERE Id=?");
         $stmt->bind_param("sssisiii", $_POST['nm'], $_POST['sm'], $_POST['bt'], $_POST['gid'], $_POST['lc'], $_POST['grid'], $_POST['cid'], $_POST['id']);
         $stmt->execute();
         $stmt->close();
         
	    $message='Modifications enregistrées';
     }
     
     
     if (!empty($_POST['trid']) && ((int)$_POST['trid'])>0) {
         $stmt = $mysqli->prepare("INSERT INTO TournamentRegistration  (CompetitorId, CategoryId, Payed) VALUES (?,?,?)");
         $p =0;
         if (!empty($_POST['pay'])){
              $p =(int)$_POST['pay'];
         }
         $stmt->bind_param("iii", $_POST['id'], $_POST['trid'], $p);
         $stmt->execute();
         $stmt->close();
     }
     
}

if (!empty($_GET['trid']) && $_GET['delt']==1) {
     $stmt = $mysqli->prepare("DELETE FROM TournamentRegistration WHERE Id=?");
     $stmt->bind_param("i", $_GET['trid'] );
     $stmt->execute();
}

if (!empty($_GET['trid']) && $_GET['pay']==1) {
     $stmt = $mysqli->prepare("UPDATE TournamentRegistration SET Payed=1 WHERE Id=?");
     $stmt->bind_param("i", $_GET['trid'] );
     $stmt->execute();
}



if (!empty($_GET['id']) && $_GET['del']==1) {
     $stmt = $mysqli->prepare("DELETE FROM TournamentRegistration WHERE CompetitorId=?");
     $stmt->bind_param("i", $_GET['id'] );
     $stmt->execute();

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

	     $stmt = $mysqli->prepare("SELECT Id, StrId, Surname, Name, Birth, GenderId, ClubId, GradeId,LicenceNumber FROM TournamentCompetitor WHERE Id=?");
      	  
      	  $stmt->bind_param("i", $New_Id );
          $stmt->execute();

          $stmt->bind_result($Id,$sId,$sn,$nm,$bt,$gid,$cid,$grid,$licence);
          $stmt->fetch();
	      $stmt->close();
	      if (empty($Id)) {
	          $Id=-1;
	      }
echo '	      
	      <form action="./reg.php" method="post">
	         <span class="ftitle">
	             COMPETITEUR
	         </span>';
	         
	         if ($message!='') {echo'<span class="fmessage">'.$message.'</span>';}
	         echo'
	           <input type="hidden" name="id" value="'.$Id.'"/>';
	           if(!empty($_REQUEST['sid']) and $Id==-1) {
	               echo'<input type="hidden" name="sid" value="'.$_REQUEST['sid'].'"/>';
	           }
	           
	           echo'
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
	                            
               $stmt->bind_param("i", $New_Id );         
      	       $stmt->execute();
               $stmt->bind_result($trid,$trname,$trshort,$wgt,$gender,$payed);
               while ($stmt->fetch()){
                  echo '<tr><td>'.$trshort.' '.$trname.' '.$gender.'</td>
                            <td>'.$wgt.'</td>
                            <td>'.$payed.'</td>
                            <td><a href="./reg.php?id='.$Id.'&trid='.$trid.'&delt=1" class="gridButton" >Supprimer</a>';
                 if ($payed!=1) {
                       echo'         <a href="./reg.php?id='.$Id.'&trid='.$trid.'&pay=1" class="gridButton" >Encaisser</a>';
                       }
                 echo'
                            </td>
                        </tr>';
               }
	           $stmt->close();    
	              
	              
	           $stmt = $mysqli->prepare("SELECT TournamentStart FROM TournamentVenue order by Id desc limit 1");
               $stmt->execute();
               $stmt->bind_result( $TournamentStart);
               $stmt->fetch();
	           $stmt->close();
	              
	              
	              
	              
	               
	           $ag = date('Y', strtotime($TournamentStart)) - date("Y",  strtotime($bt));        
	           echo'
	           </table>
	           Ajouter:
	            <select name="trid">
	            <option value="-1">--</option>';
	            
	             $stmt = $mysqli->prepare("SELECT DISTINCT
	                                            Cat.Id, 
	                                            TournamentAgeCategory.Name,
	                                            TournamentAgeCategory.ShortName,
	                                            IFNULL(-Cat.MaxWeight, IFNULL(Cat.MinWeight,'OPEN')),
	                                            TournamentGender.Name
	                                     FROM TournamentCategory Cat
	                                     INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = Cat.AgeCategoryId
	                                     INNER JOIN TournamentGender ON TournamentGender.Id=TournamentAgeCategory.GenderId
	                                     LEFT OUTER JOIN V_Age_Reg on V_Age_Reg.AgeCategoryId=Cat.AgeCategoryId and V_Age_Reg.CompetitorId=?
	                                     WHERE IFNULL(MaxAge,1000)>? AND IFNULL(MinAge,0)<? AND TournamentAgeCategory.GenderId=? AND V_Age_Reg.AgeCategoryId IS NULL
	                                     ORDER BY IFNULL(MinAge,IFNULL(MaxAge,1000)),IFNULL(-Cat.MaxWeight, IFNULL(Cat.MinWeight,'OPEN'))");       
	           
               $stmt->bind_param("iiii", $Id, $ag,$ag,$gid);         
      	       $stmt->execute();
               $stmt->bind_result($trid,$trname,$trshort,$wgt,$gender);
               while ($stmt->fetch()){
                  echo '<option value="'.$trid.'">'.$trshort.' '.$trname.' '.$gender.' '.$wgt.'</option>';
               }
	           $stmt->close();    
	           echo'
                </select> 
                Payement reçu <input type="checkbox"  name="pay" value="1"/>
                ';
	        
	        
	        }



echo'
	       <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Enregistrer les modifications">
	               <a class="pgeBtn" href="listingreg.php">Annuler/Fermer</a>
	               <a class="pgeBtn" href="card.php?sid='.$sId.'" target="blanck">Carte</a> 
	       </span>
	       </form>
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

