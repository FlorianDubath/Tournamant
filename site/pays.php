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


$clubid = (int)$_POST['cid'];
$nb = (int)$_POST['nb'];
$message="";

if ($clubid>0 && $nb>0) {
    $stmt = $mysqli->prepare("
         UPDATE TournamentRegistration R
         INNER JOIN TournamentCompetitor C ON R.CompetitorId= C.Id AND R.Payed=0 AND C.ClubId=? 
         SET R.Payed=1 LIMIT ?");
    $stmt->bind_param('ii',$clubid,$nb);
    $stmt->execute();
    $stmt->close();
    $message="Payement enregistré.";
    

}


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
              PAYEMENTS PAR CLUB
            </span>  ';
	         if ($message!='') {echo'<span class="fmessage">'.$message.'</span>';}
	         
	         echo'
            <span class="h_txt">
                 <span class="btnBar"> 
                           <a class="pgeBtn" href="listingreg.php">Annuler/Fermer</a>
                 </span>

	      
	      ';

echo'
        <form action="./pays.php" method="post" Id="F1">
	       <span class="fitem">
               <span class="label">Club :</span>
               <select id="cid" name="cid"  form="F1" onchange="
               
               let select = document.getElementById(\'cid\');
               let val = select.options[select.selectedIndex].text
             
               let b1 = val.split(\': \');
               let b2 =  b1[1].split(\' \');
               
               document.getElementById(\'nb\').value=b2[0];
               document.getElementById(\'nb\').max=b2[0];
               
               
               ">
               <option value="-1" >--</option>';
               $stmt = $mysqli->prepare("select TournamentClub.Id, TournamentClub.Name, count(TournamentRegistration.Id) from TournamentClub LEFT OUTER JOIN TournamentCompetitor ON TournamentCompetitor.ClubId=TournamentClub.Id LEFT OUTER JOIN TournamentRegistration ON TournamentRegistration.CompetitorId= TournamentCompetitor.Id AND TournamentRegistration.Payed=0 GROUP BY TournamentClub.Id, TournamentClub.Name ORDER BY TournamentClub.Name
               ");
      	        $stmt->execute();
               $stmt->bind_result($ccId,$ccname, $ccnumber);
               while ($stmt->fetch()){
                   if($ccnumber>0){
                      $sel ='';
                      if ($ccId==$clubid) { $sel=' selected ';}
                      echo '<option value="'.$ccId.'" '.$sel.'>'.$ccname.' (A payer : '.$ccnumber.' participations)</option>';
                   }
               }
	           $stmt->close();
               
               echo'
                  </select><br/>
	       </span>
	        <span class="fitem">
               <span class="label">Nombre d\'inscriptions payées :</span>
               <input id="nb" name="nb" type="number" step="1" min="1" max="100"/>
               
               </span>
	       ';
	        
	        
	        



echo'
	       <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Payement reçu" form="F1">
	               <a class="pgeBtn" href="listingreg.php">Annuler/Fermer</a>
	       </span>';
	       
	       
	       
echo'	       
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

