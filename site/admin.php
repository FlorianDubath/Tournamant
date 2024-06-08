<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsAdmin']!=1) {
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
echo ' 
            <span class="h_title">
               GESTION DES UTILISATEURS
            </span>
            <span class="h_txt">
                 <span class="btnBar"> 
                 
                       <a class="pgeBtn" href="index.php" title="Fermer" >Fermer</a>
	               <a class="pgeBtn"  href="changeUser.php?id=0" title="Nouvel Utilisateur">Nouvel Utilisateur</a>
	             </span>
      <table class="wt t4"><tr class="tblHeader"><td >Identifiant</td><td>Derni&egrave;re connection</td>
      <td >Admin</td>
      <td >Inscription</td>
      <td >Acceuil</td>
      <td >Pesée</td>
      <td >Table centrale</td>
      <td >Table tatami</td>
      <td >Action</td>
      </tr>';
      $mysqli= ConnectionFactory::GetConnection(); 
     $stmt = $mysqli->prepare("SELECT Id,EMail,DisplayName,LastLoggedIn,Salt,IsAdmin, IsRegistration, IsWelcome, IsWeighting, IsMainTable, IsMatTable FROM TournamentSiteUser ");
     $stmt->bind_result( $Id, $EMail,$disp,$last,$s, $IsAdmin, $IsRegistration, $IsWelcome, $IsWeighting, $IsMainTable, $IsMatTable);
     $stmt->execute();
     while ($stmt->fetch()){
     
     $date='';
     if (isset($last)){
       $d1=new DateTime($last);
       $date=$d1->format('d/m/Y');
     }
     echo' <tr ><td >'. $disp.'('.$EMail.')</td><td>'.$date.'</td>
      <td class="rt">'.$IsAdmin.'</td>
      <td class="rt">'.$IsRegistration.'</td>
      <td class="rt">'.$IsWelcome.'</td>
      <td class="rt">'.$IsWeighting.'</td>
      <td class="rt">'.$IsMainTable.'</td>
      <td class="rt">'.$IsMatTable.'</td>
      <td class="rt">';
     if($IsAdmin==0){
        echo' 
        
         <form action="./addUser.php" method="post">
             <input type="hidden" name="uid" value="'.$Id.'"/>
             <input type="hidden" name="del" value="1"/>
             <input class="gridButton" type="submit" value="Supprimer"/> 
         </form>';
        
     }
     
     echo'
     <a href="./changeUser.php?uid='.$Id.'" class="gridButton" >Modifier</a>
     <a href="./ota.php?uid='.$Id.'" class="gridButton" target="_blanck">Accès Unique</a>
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
