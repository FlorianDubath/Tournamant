<?php

function writeHead() {
    echo '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./css/test.css" />
   <!-- 
     <script type="text/javascript">
    if (window.location.protocol == "http:") {
        var restOfUrl = window.location.href.substr(5);
        window.location = "https:" + restOfUrl;
    }
</script>-->
    <script>
        function toggleClass(element,class_name){
            var regex_1 = new RegExp(\'(?:^|\\\s)\'+class_name+\'(?!\\\S)\');
            if ( element.className.match(regex_1) ){
                 var regex_2 = new RegExp(\'(?:^|\\\s)\'+class_name+\'(?!\\\S)\',\'g\');
                 element.className = element.className.replace(regex_2, \'\' );
            } else {
                element.className += " "+class_name;
            }
        }
        
    </script>
</head>';
}

function writeBand() {
    $isLoged =  !empty($_SESSION['_UserId']);
    
    echo '<div id="band" class="band ';
    if (!$isLoged) {echo 'b_closed';}
    echo'" >
             <div class="b_title" onclick="toggleClass(document.getElementById(\'band\'), \'b_closed\')"> 
                  <img id="logo" src="css/Logo_ACG_JJJ.png" height="30px"></img>
                  <div class="b_i_title">ACGJJJ</div>';
             
    if ($isLoged) {echo '<a class="lgout" href="identification.php">logout</a>';}        
             echo' </div>';
    
    if (!$isLoged){
    echo'
       <span class="h_title">
               <form action="./identification.php" method="post">
               <input   type="hidden" name="rtn" value="'.$_SERVER['REQUEST_URI'].'" />
	             <span class="fitem">
	               <span class="label">Nom d\'utilisateur:</span>
	               <input class="inputText"  type="text" name="login" value="" />
	             </span>
	             <span class="fitem">
	               <span class="label">Mot de passe:</span>
                   <input class="inputText"  type="password" name="mdp" value="" />
                 </span>
                 <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Se connecter">
	             </span>
	            ';
	             
	             if ($_GET["CR"]) {
	                echo ' <br/><span class="fitem"><span class="message">Vérifiez vos indentifiants</span><br/>' ;
	             }
	             
	             echo' 
	             
                </form>
               </span>';
       
    
    } else {
     if ($_SESSION['_IsAdmin']==1) {
       echo ' 
       <div class="m_b">
           <span class="m_b_t">CONFIGURATION</span>
           <a href="global_config.php">Configurer les dates du tournois</a>
           <a href="cat_config.php">Configurer les catégories/horaires</a> 
           <a href="double_start_conf.php">configurer les doubles départs</a> 
           <span class="m_b_t">ATTRIBUTION DES ROLES </span>
           <a href="admin.php">Créer les utilisateurs et attribuer des rôles</a>
       </div>';
    }
    
    if ($_SESSION['_IsRegistration']==1) {
       echo '
       <div class="m_b">
           <span class="m_b_t">INSCRIPTIONS</span>
           <a href="listingclub.php">Liste des clubs</a> 
           <a href="listingreg.php">Liste des inscrits</a> 
        </div>';
    }
    
    
    echo ' <div class="m_b">
           <span class="m_b_t">CATEGORIES</span>
           <a href="listingcat.php">Liste des catégories</a> 
           <a href="listingNotW.php">Liste des Compétiteurs manquants</a> 
           <a href="listingresult.php">Résultats</a> 
     </div>';
    
    
    }
    echo'         
    
    
    
         </div>';
}


function formatDate($date_txt, $locale='fr_FR') {
$ts = new DateTime($date_txt);
$formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::LONG);

$formatter->setPattern('EEEE');
$result =$formatter->format($ts);
$formatter->setPattern('d');
$result =$result.' '.$formatter->format($ts);
$formatter->setPattern('MMMM');
$result =$result.' '.$formatter->format($ts);
$formatter->setPattern('Y');
$result =$result.' '.$formatter->format($ts);
return $result;   
}





