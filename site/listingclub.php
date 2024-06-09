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
         
writeBand();
echo ' 
            <span class="h_title">
               GESTION DES CLUBS
            </span>
            <span class="h_txt">
                 <span class="btnBar"> 
                       <a class="pgeBtn" href="index.php" title="Fermer" >Fermer</a>
	               <a class="pgeBtn"  href="club.php?id=0" title="Nouveau club">Nouveau club</a>
	             </span>
      <table class="wt t4">
      <tr class="tblHeader">
      <th>Nom</th>
      <th>Contact</th>
      <th>Action</th>
      </tr>';
      $mysqli= ConnectionFactory::GetConnection(); 
     $stmt = $mysqli->prepare("SELECT Id,Name, Contact FROM TournamentClub ");
     $stmt->bind_result( $Id, $name, $contact);
     $stmt->execute();
     while ($stmt->fetch()){
     
     echo' <tr >
      <td class="rt">'.$name.'</td>
      <td class="rt">'.$contact.'</td>
      <td class="rt"> <form action="./club.php" method="post">
             <input type="hidden" name="id" value="'.$Id.'"/>
             <input type="hidden" name="del" value="1"/>
             <input class="gridButton" type="submit" value="Supprimer"/> 
         </form><a href="./club.php?id='.$Id.'" class="gridButton" >Modifier</a></td>
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
