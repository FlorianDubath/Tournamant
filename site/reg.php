<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsRegistration']!=1) {
	header('Location: ./index.php');
}


include 'connectionFactory.php';
$mysqli= ConnectionFactory::GetConnection(); 

//todo multiple delete en cascade et insert à l'avenant

if($_POST && !empty($_POST['id'])) {
     if ($_POST['id']==-1) {
         $stmt = $mysqli->prepare("INSERT INTO TournamentCompetitor  (Name, Contact) VALUES (?,?)");
         $stmt->bind_param("ss", $_POST['nm'], $_POST['ct']);
         $stmt->execute();
         $stmt->close();
	     header('Location: ./listingreg.php');
     } else {
         $stmt = $mysqli->prepare("UPDATE TournamentCompetitor  SET Name=?, Contact=? WHERE Id=?");
         $stmt->bind_param("ssi", $_POST['nm'], $_POST['ct'],$_POST['id']);
         $stmt->execute();
         $stmt->close();
     }
}
if (!empty($_GET['id']) && $_GET['del']==1) {
     $stmt = $mysqli->prepare("DELETE FROM TournamentClub WHERE Id=?");
     $stmt->bind_param("i", $_GET['id'] );
     $stmt->execute();
	 header('Location: ./listingclub.php');
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

          $stmt->bind_result($Id,$sn,$$nm,$bt,$gid,$cid,$grid,$licence);
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
               <input class="inputDate"  type="text" name="sm" value="'.$surnam.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Prénom :</span>
               <input class="inputDate"  type="text" name="nm" value="'.$name.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Date de naissence :</span>
               <input class="inputDate"  type="date" name="bt" value="'.$birth.'" /><br/>
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
               <input class="inputDate"  type="text" name="bt" value="'.$licence.'" /><br/>
	        </span>';

// todo  $Id=-1 listing catégories +filtrée par genre, âge et double départ

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

