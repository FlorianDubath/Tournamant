<?php

ob_start();
session_name("Tournament");	
session_start();

include '_commonBlock.php';
writeHead();

echo'
<body>
    <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 
         
if ($_SESSION['_IsAdmin']==1) {
   echo 'ADMIN<br/>
Configuration<br/>
   <a href="global_config.php">configurer les dates du tournois</a> <br/>
   <a href="cat_config.php">configurer les catégories/horaires</a> <br/>
   <a href="double_start_conf.php">configurer les doubles départs</a> <br/>
Attribution des rôle <br/>
   <a href="user_config.php">Créer les utilisateurs et attribuer des rôles</a> <br/>';


} else {
echo ' 
            <span class="h_title">
               CONNECTION
               </span>
               
               <span class="h_txt">
               <form action="./identification.php" method="post">
	             <span class="fitem">
	               <span class="label">Nom d\'utilisateur:</span>
	               <input class="inputText"  type="text" name="login" value="" /><br/>
	             </span>
	             <span class="fitem">
	               <span class="label">Mot de passe:</span>
                   <input class="inputText"  type="password" name="mdp" value="" /><br/>
                 </span>
	            ';
	             
	             if ($_GET["CR"]) {
	                echo ' <span class="fitem"><span class="message">Vérifiez vos indentifiants</span><br/>' ;
	             }
	             
	             echo' 
	             <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Se connecter">
	             </span>
                </form>
               </span>';
}


 include 'connectionFactory.php';
    $mysqli= ConnectionFactory::GetConnection(); 
    if (!($stmt = $mysqli->prepare("SELECT  RegistrationEnd, TournamentStart, TournamentEnd FROM TournamentVenue ORDER BY TournamentStart DESC LIMIT 1"))){
      echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
    }
    if (!($stmt->execute())){
      echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
    }
    $stmt->bind_result( $RegistrationEnd,$TournamentStart, $TournamentEnd);
    $stmt->fetch();
	$stmt->close();
	
	echo ' Date: '.$TournamentStart;
	if ($TournamentStart!=$TournamentEnd) {
	    echo ' - '.$TournamentEnd;
	}
	echo '<br/>';
	
	if (!($stmt = $mysqli->prepare("SELECT IFNULL(-MaxWeight, IFNULL(MinWeight,'OPEN')), TournamentAgeCategory.Name,TournamentAgeCategory.ShortName, TournamentAgeCategory.MaxAge , TournamentGender.Name FROM TournamentCategory INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId INNER JOIN TournamentGender ON GenderId = TournamentGender.Id order by TournamentAgeCategory.MinAge ASC, GenderId ASC, IFNULL(MaxWeight, 100+MinWeight) ASC"))){
      echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
    }
    if (!($stmt->execute())){
      echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
    }
    $stmt->bind_result( $max_w, $ageCatN, $AgeCatAbd, $MaxAge, $gen);
    $current_cat='';
    $current_AgeCatAbd='';
    $curr_gen='';
    $accumulation='';
    while ($stmt->fetch()) {
      if ( $current_cat!=$ageCatN || $curr_gen!=$gen) {
         if ($accumulation!=''){
            echo $curr_AgeCatAbd.' '.$current_cat.' '.$curr_gen.' '.$accumulation. '<br/>' ;
         }
         $current_cat=$ageCatN;
         $curr_gen=$gen;
         $curr_AgeCatAbd = $AgeCatAbd;
         $accumulation='';
      }
      if ($accumulation!=''){
          $accumulation=$accumulation.'/';
      }
      if ($max_w<0) {
          $accumulation=$accumulation. $max_w;
      } else {
          $accumulation=$accumulation. '+'.$max_w;
      }
      
    }
     if ($accumulation!=''){
            echo $curr_gen.'-'.$current_cat.' '.$accumulation. '<br/>' ;
         }
	$stmt->close();
	
	
	
	
echo '
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

