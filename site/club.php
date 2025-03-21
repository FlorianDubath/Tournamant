<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsRegistration']!=1) {
	header('Location: ./index.php');
}


include 'connectionFactory.php';
$mysqli= ConnectionFactory::GetConnection(); 
 $message='';
if($_POST && !empty($_POST['id'])) {
   if (!empty($_POST['del']) &&  $_POST['del']==1){
       $stmt = $mysqli->prepare("DELETE FROM TournamentClub WHERE Id=?");
       $stmt->bind_param("i", $_POST['id'] );
       $stmt->execute();
	   header('Location: ./listingclub.php');
   } else {
     if ((int)$_POST['id']<=0) {
         $stmt = $mysqli->prepare("INSERT INTO TournamentClub  (Name, Contact) VALUES (?,?)");
         $stmt->bind_param("ss", $_POST['nm'], $_POST['ct']);
         $stmt->execute();
         $stmt->close();
	     header('Location: ./listingclub.php');
     } else {
         $stmt = $mysqli->prepare("UPDATE TournamentClub  SET Name=?, Contact=? WHERE Id=?");
         $stmt->bind_param("ssi", $_POST['nm'], $_POST['ct'],$_POST['id']);
         $stmt->execute();
         $stmt->close();
     }
	 $message='Modifications enregistrées';
   }
}

include '_commonBlock.php';

writeHead();

echo'
<body>
    <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 
         
writeBand();

	     $stmt = $mysqli->prepare("SELECT TournamentClub.Id,
	                                            TournamentClub.Name,
	                                            TournamentClub.Contact
	                                     FROM TournamentClub 
	                                     WHERE TournamentClub.Id=?");
      	  
      	  $stmt->bind_param("i", $_REQUEST['id'] );
          $stmt->execute();

          $stmt->bind_result($Id,$Name,$contact);
          $stmt->fetch();
	      $stmt->close();
	      if (empty($Id)) {
	          $Id=-1;
	      }
echo '	      
           <span class="h_title">
              CLUB
            </span>
            <span class="h_txt">
                 <span class="btnBar"> 
                        <a class="pgeBtn" href="listingclub.php">Annuler/Fermer</a>
                 </span>

	      <form action="./club.php" method="post">
	        ';
	         if ($message!='') {echo'<span class="fmessage">'.$message.'</span>';}
	         
	         echo'
	        <input type="hidden" name="id" value="'.$Id.'"/>
	       
	        <span class="fitem">
               <span class="label">Nom :</span>
               <input class="inputDate"  type="text" name="nm" value="'.$Name.'" /><br/>
	        </span>
	        <span class="fitem">
               <span class="label">Contact :</span>
               <input class="inputDate"  type="text" name="ct" value="'.$contact.'" /><br/>
	        </span>
	       <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Enregistrer les modifications">
	               <a class="pgeBtn" href="listingclub.php">Annuler/Fermer</a>
	       </span>
	       </form>
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

