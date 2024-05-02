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
  if (!($stmt = $mysqli->prepare("SELECT Id,Name,Place,Transport,Organization, Admition, System,Prize, Judge,Dressing,Contact,RegistrationEnd,TournamentStart,TournamentEnd FROM TournamentVenue order by Id desc limit 1"))){
      	     echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
      	  }
          if (!($stmt->execute())){
             echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
          }

          $stmt->bind_result( $Id,$Name,$Place,$Transport,$Organization, $Admition, $System,$Prize, $Judge,$Dressing,$Contact,$RegistrationEnd,$TournamentStart,$TournamentEnd);
          $stmt->fetch();
	      $stmt->close();
	     
          $date_txt = formatDate($TournamentStart);
	      if ($TournamentStart!=$TournamentEnd) {
	          $date_txt =  $date_txt.' - '. formatDate($TournamentEnd);
	      }
	      
	      $begin_t = strtotime($TournamentStart);
	      $end_t = strtotime($TournamentEnd);
	      
	      
	echo '<span class="flyer">
	        <span class="f_title">'.$Name.'</span>
	        
	        <span class="f_info strong">
	            <span class="f_i_ti">Date :</span><span class="f_i_tx">'.$date_txt.'</span>
	        </span>
	        <span class="f_info strong">
	            <span class="f_i_ti">Lieu :</span><span class="f_i_tx">'.$Place.'</span>
	        </span>
	        <span class="f_info strong">
	            <span class="f_i_ti">Transports :</span><span class="f_i_tx">'.$Transport.'</span>
	        </span>
	        <span class="f_info strong">
	            <span class="f_i_ti">Organisation :</span><span class="f_i_tx">'.$Organization.'</span>
	        </span>
	        <span class="f_info">
	            <span class="f_i_ti">Admission :</span><span class="f_i_tx">'.$Admition.'</span>
	        </span>
	        <span class="f_info">
	            <span class="f_i_ti">Système :</span><span class="f_i_tx">'.$System.'</span>
	        </span>
	        <span class="f_info">
	            <span class="f_i_ti">Récompenses :</span><span class="f_i_tx">'.$Prize.'</span>
	        </span>
	        <span class="f_info">
	            <span class="f_i_ti">Arbitrage :</span><span class="f_i_tx">'.$Judge.'</span>
	        </span>
	        <span class="f_info">
	            <span class="f_i_ti">Tenue :</span><span class="f_i_tx">'.$Dressing.'</span>
	        </span>
	        <span class="f_info">
	            <span class="f_i_ti">Assurances :</span><span class="f_i_tx">À la charge des participants</span>
	        </span>
	        <span class="f_info">
	            <span class="f_i_ti">Contact :</span><span class="f_i_tx">'.$Contact.'</span>
	        </span>';
	        
	    for ($day_counter = $begin_t;   $day_counter<= $end_t ;   $day_counter+=86400) {
	       $day_date =  date('Y-m-d',  $day_counter);
	       echo ' <span class="f_g_info strong">Programme : '.formatDate($day_date).'  </span>';  //Todo opening
	       echo '<table class="t_prog">
	                 <tr><th>Catégrie</th><th>Année naiss.</th><th>Catégorie de poids</th><th>Horaire pesée</th></tr>';
	       
	       
	       
	       $stmt = $mysqli->prepare("SELECT IFNULL(-MaxWeight, IFNULL(MinWeight,'OPEN')), 
	                                              TournamentAgeCategory.Name,
	                                              TournamentAgeCategory.ShortName, 
	                                              TournamentAgeCategory.MaxAge, 
	                                              TournamentGender.Name,
	                                              TournamentWeighting.WeightCategoryBasedOnAttendence,
	                                              TournamentWeighting.WeightingBegin,
	                                              TournamentWeighting.WeightingEnd
	                                       FROM TournamentCategory 
	                                       INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId 
	                                       INNER JOIN TournamentGender ON GenderId = TournamentGender.Id 
	                                       INNER JOIN TournamentWeighting ON TournamentAgeCategory.Id = TournamentWeighting.AgeCategoryId
	                                       WHERE DATE(WeightingEnd)=?
	                                       order by WeightingEnd, TournamentAgeCategory.MinAge ASC, GenderId ASC, IFNULL(MaxWeight, 100+MinWeight) ASC");
	      $stmt->bind_param("s",  $day_date);
          $stmt->execute();
     
          $stmt->bind_result( $max_w, $ageCatN, $AgeCatAbd, $MaxAge, $gen,$cat_on_att, $w_begin, $w_end);

	      
         $current_cat='';
         $current_AgeCatAbd='';
         $curr_gen='';
         $curr_w_b=0;
         $curr_w_e=0;
         $accumulation='';
         while ($stmt->fetch()) {
           if ( $current_cat!=$ageCatN || $curr_gen!=$gen) {
             if ($accumulation!=''){
                echo  '<tr><td>'.$curr_AgeCatAbd.' '.$current_cat.' '.$curr_gen.'</td><td></td><td>'.$accumulation.'</td><td>'.date('H\hi', strtotime($curr_w_b)).' à '.date('H\hi', strtotime($curr_w_e)).'</td></tr>' ;
             }
             $current_cat=$ageCatN;
             $curr_gen=$gen;
             $curr_AgeCatAbd = $AgeCatAbd;
             $curr_w_b = $w_begin;
             $curr_w_e = $w_end;
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
                echo  '<tr><td>'.$curr_AgeCatAbd.' '.$current_cat.' '.$curr_gen.'</td><td></td><td>'.$accumulation.'</td><td>'.date('H\hi', strtotime($curr_w_b)).' à '.date('H\hi', strtotime($curr_w_e)).'</td></tr>' ;
             }
        $stmt->close();
	       
	       
	       
	       echo '</table>';
	       
	       
	       
	    }
	        
	        echo'
	        
	      </span>';
	
	

	
	
echo '
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

