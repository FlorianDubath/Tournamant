<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsRegistration']!=1 && $_SESSION['_IsMainTable']!=1 ) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';
$mysqli= ConnectionFactory::GetConnection(); 

 


include '_commonBlock.php';

writeHead();

echo'
<body>
    <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 
         
writeBand();

//TODO register pending and validate them (with message)
// split on linebreak preg_split('/\r\n|[\r\n]/', $_POST['thetextarea'])
/*str_getcsv(
    $_POST['bulk'],
    string $separator = ",",
    string $enclosure = "",
    string $escape = "\\"
): array
	    
*/	  
echo '	      

          <span class="h_title">
              INSCRIPTIONS GROUPEES
            </span>
            <span class="h_txt">
                 <span class="btnBar"> 
                           <a class="pgeBtn" href="listingreg.php">Annuler/Fermer</a>
                 </span>

	      
	      ';
	         
if ($message!='') {echo'<span class="fmessage">'.$message.'</span>';}

echo'
               <form action="./regs.php" method="post" Id="F1">
	       <span class="fitem">
               <span class="label">Club :</span>
               <select name="cid"  form="F1">';
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
                 <span class="label">Catégorie d\'âge :</span>
	        <select name="agcatid"  form="F1">';
	             $stmt = $mysqli->prepare("SELECT 
	                                            TAC_1.Id,
	                                            TAC_1.Name,
	                                            TAC_1.ShortName,
	                                            TournamentGender.Name
	                                     FROM TournamentAgeCategory TAC_1 
	                                     INNER JOIN TournamentGender ON TournamentGender.Id=TAC_1.GenderId
	                                    
	                                     ORDER BY IFNULL(TAC_1.MinAge,IFNULL(TAC_1.MaxAge,1000))");       
      	       $stmt->execute();
               $stmt->bind_result($agcatid,$trname,$trshort,$gender);
               while ($stmt->fetch()){
                  echo '<option value="'.$agcatid.'">'.$trshort.' '.$trname.' '.$gender.'</option>';
               }
	           $stmt->close();    
	           echo'
                </select> 
               </span>
	       <span class="fitem" width="100%" > 
	       <span class="label">Copier le contenu du tableur :</span><br/>
	       <textarea  name="bulk" rows="30"  style="display:block;width:100%;"></textarea>
                </span>
                ';
	        
	        
	        



echo'
	       <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Enregistrer" form="F1">
	               <a class="pgeBtn" href="listingreg.php">Annuler/Fermer</a>
	       </span>';
	       
	       
	       
echo'	       
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

