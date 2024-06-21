<?php

ob_start();
session_name("Tournament");	
session_start();

include 'site/_commonBlock.php';
writeHead();


echo'
<body>
    <div class="f_cont">';
    
writeBand(True);



 include 'site/connectionFactory.php';
    $mysqli= ConnectionFactory::GetConnection(); 
    echo'        
       <div class="cont_l">
         <div class="h">	      
           <span class="h_title">
              TEST DE LA WEBAPP POUR LES TOURNOIS
            </span>';
            
if ($_SESSION['_IsAdmin'] ==1){
   echo'
        <span class="h_title">Remise à zéro</span>
        <form action="./index.php" method="post">
              <input type="hidden" name="reset" value="1"/>
	      <input class="pgeBtn"  type="submit" value="Remise à zéro des testes"> 
	</form><br/><br/>
        <span class="h_title">Télécharger des données de test</span>
        <a class="pagebtn" target="_blanck" url="MaterielTest/Inscriptions_1.xlsx">Inscriptions 1 </a><br/>
        <a class="pagebtn" target="_blanck" url="MaterielTest/Inscriptions_2.xlsx">Inscriptions 2 </a><br/><br/>
        <span class="h_title">Changer la date/horaires pour</span>
        <form action="./index.php" method="post">
              <input type="hidden" name="j3" value="1"/>
	      <input class="pgeBtn"  type="submit" value="3 jours avant le tournoi"> 
	</form><br/><br/>
	<form action="./index.php" method="post">
              <input type="hidden" name="j3" value="2"/>
	      <input class="pgeBtn"  type="submit" value="1h avnt la pesée des élite M"> 
	</form><br/><br/>
	<form action="./index.php" method="post">
              <input type="hidden" name="j3" value="3"/>
	      <input class="pgeBtn"  type="submit" value="Milieu de la pesée des élite M"> 
	</form><br/><br/>
	<form action="./index.php" method="post">
              <input type="hidden" name="j3" value="4"/>
	      <input class="pgeBtn"  type="submit" value="Fin de la pesées des élites M"> 
	</form><br/><br/>
	
	
   
   ';
} else {
   echo 'Identifiez-vous en tant qu\'admin pour avoir accès aux fonctionalités!';
}
	
	

	
	
echo '
        
        </div>   
     </div>
</body>
</html>';

?>

