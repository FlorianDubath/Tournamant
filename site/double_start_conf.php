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

         $mysqli= ConnectionFactory::GetConnection(); 
	     $stmt = $mysqli->prepare("SELECT TournamentDoubleSatrt.Id,
	                                            TournamentDoubleSatrt.MainAgeCategoryId,
	                                            TAC_M.Name,
	                                            TAC_M.ShortName,
	                                            G_M.Name,
	                                            TournamentDoubleSatrt.AcceptedAgeCategoryId,
	                                            TAC_A.Name,
	                                            TAC_A.ShortName,
	                                            G_M.Name
	                                            
	                                     FROM TournamentDoubleSatrt 
	                                     INNER JOIN TournamentAgeCategory as TAC_M on TAC_M.Id = MainAgeCategoryId
	                                     INNER JOIN TournamentAgeCategory as TAC_A on TAC_A.Id = AcceptedAgeCategoryId
	                                     INNER JOIN TournamentGender as G_M on G_M.Id = TAC_M.GenderId
	                                     INNER JOIN TournamentGender as G_A on G_A.Id = TAC_A.GenderId");
          $stmt->execute();

          $stmt->bind_result( $Id,$m_c_id,$m_c_name,$m_c_short,$m_c_gen, $a_c_id,$a_c_name,$a_c_short,$a_c_gen);
          
          echo '  <span class="h_title">
               CONFIGURATION DES DOUBLE DEPARTS
            </span>
            <span class="h_txt">
                 <span class="btnBar"> 
                 
                       <a class="pgeBtn" href="index.php" title="Fermer" >Fermer</a>
	           
	               <a class="pgeBtn" href="dbstartconf.php?id=-1">Ajouter</a>
	         </span>
	               <table>
          <tr><th>Catégorie orginale</th><th>Catégorie acceptée</th><th>Action</th></tr>';
          
          
         while( $stmt->fetch()){
         echo'  <tr><td>'.$m_c_name.'('.$m_c_short.' '.$m_c_gen.')</td><td>'.$a_c_name.'('.$a_c_short.' '.$a_c_gen.')</td><td>
         <form action="./dbstartconf.php" method="post">
             <input type="hidden" name="id" value="'.$Id.'"/>
             <input type="hidden" name="del" value="1"/>
             <input class="pgeBtn" type="submit" value="Supprimer"/> 
         </form></td></tr>';
         }
         
         echo '</table>
           
	       <span class="btnBar"> 
	               <a class="pgeBtn" href="index.php">Fermer</a>
	       </span>';
	      $stmt->close();
	
echo '	
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

