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
             <div class="b_title" onclick="toggleClass(document.getElementById(\'band\'), \'b_closed\')"> Titre';
             
    if ($isLoged) {echo '<a href="identification.php">logout</a>';}        
             echo' </div>';
    
    if (!$isLoged){
    echo'
       <span class="h_title">
               CONNECTION
               </span>
               
               <span class="h_txt">
               <form action="./identification.php" method="post">
               <input   type="hidden" name="rtn" value="'.$_SERVER['REQUEST_URI'].'" />
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
       
    
    } else {
     if ($_SESSION['_IsAdmin']==1) {
       echo ' 
       <div class="m_b">
           <span class="m_b_t">CONFIGURATION</span>
           <a href="global_config.php">configurer les dates du tournois</a>
           <a href="cat_config.php">configurer les catégories/horaires</a> 
           <a href="double_start_conf.php">configurer les doubles départs</a> 
           <span class="m_b_t">ATTRIBUTION dES ROLES </span>
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





