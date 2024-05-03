<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsRegistration']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';

include '_commonBlock.php';

writeHead();

echo'
<body>
    <div class="f_cont">';

echo'        
       <div class="cont_l">
         <div class="h">'; 
         
         
         //TODO enregistrement commun / filtre sur les noms
echo ' 
            <span class="h_title">
               GESTION DES INSCRIPTIONS
            </span>
            <span class="h_txt">
                 <span class="btnBar"> 
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
      $mysqli= ConnectionFactory::GetConnection(); 
     $stmt = $mysqli->prepare("SELECT 
                                        TournamentCompetitor.Id, StrId, Surname, Name, Birth, TournamentGender.Name, TournamentClub.Name, TournamentGrade.Name,LicenceNumber, TournamentAgeCategory.ShortName, Payed
                               FROM TournamentCompetitor
                               LEFT OUTER JOIN TournamentRegistration ON TournamentRegistration.CompetitorId = TournamentCompetitor.Id
                               LEFT OUTER JOIN TournamentCategory ON TournamentCategory.Id = TournamentRegistration.CategoryId
                               INNER JOIN TournamentGender ON GenderId=TournamentGender.Id
                               INNER JOIN TournamentGrade ON GradeId=TournamentGrade.Id
                               INNER JOIN TournamentClub ON ClubId=TournamentClub.Id
                               ORDER BY TournamentClub.Name, Surname, Name, ShortName
 ");
     $stmt->bind_result( $Id, $strId,$Surname,$Name,$Birth, $Gender, $Club, $Grade, $licence, $cat, $payed);
     $stmt->execute();
     while ($stmt->fetch()){
     
     $date='';
     if (isset($Birth)){
       $d1=new DateTime($Birth);
       $date=$d1->format('d/m/Y');
     }
     echo' <tr >
     <td >'. $Club.'</td>
      <td class="rt">'.$Surname.' '.$Name.'</td>
      <td class="rt">'.$IsRegistration.'</td>
      <td class="rt">'.$date.'</td>
      <td class="rt">'.$Gender.'</td>
      <td class="rt">'.$Grade.'</td>
      <td class="rt">'.$licence.'</td>
      <td class="rt">'.$cat.'</td>
      <td class="rt">'.$payed.'</td>
      <td class="rt">
         <a href="./reg.php?id='.$Id.'&del=1" class="gridButton" >Supprimer</a>
         <a href="./reg.php?id='.$Id.'" class="gridButton" >Modifier</a>
         <a href="./reg.php?id='.$Id.'&pay=1" class="gridButton" >Enregistrer Payement</a>
         <a href="./card.php?id='.$Id.'" class="gridButton" >Carte</a>
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
