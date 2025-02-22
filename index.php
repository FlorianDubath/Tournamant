<?php

ob_start();
session_name("Tournament");	
session_start();

$message='';

include 'site/connectionFactory.php';
$mysqli= ConnectionFactory::GetConnection(); 
if ($_SESSION['_IsAdmin'] ==1){

    if(!empty($_POST['j3'])){
         $cmd = (int)$_POST['j3'];
         
         
        date_default_timezone_set('Europe/Zurich');
         
         $stmt = $mysqli->prepare("SELECT Id FROM TournamentAgeCategory WHERE ShortName='ME'");
         $stmt->bind_result($elitMCatId);
         $stmt->execute();
         $stmt->fetch();
	     $stmt->close();
         
         
         
         
         if ($cmd==-1){
           // reset all
           $mysqli->begin_transaction();
            try {
                $mysqli->query("DELETE FROM ActualCategoryResult;");
              
                $mysqli->query("DELETE FROM Fight;");
                $mysqli->query("DELETE FROM StepLinking;");
                $mysqli->query("DELETE FROM CategoryStep;");
                $mysqli->query("DELETE FROM ActualCategory;");
                $mysqli->query("DELETE FROM TournamentRegistration;");
                $mysqli->query("DELETE FROM TournamentCompetitor;");
                
                 
                $mysqli->query("DELETE FROM TournamentDoubleSatrt;");
                $mysqli->query("DELETE FROM TournamentWeighting;");
                $mysqli->query("DELETE FROM TournamentVenue;");
      
                $mysqli->query("
INSERT INTO TournamentVenue(Name, Place, Transport, Organization, Admition, `System`, Prize, Judge, Dressing, Contact, RegistrationEnd, TournamentStart, TournamentEnd) VALUES (
     '52e Championnats Genevois Individuels de Judo',
     'Centre Omnisport du Sapay, ch. le Sapay 3, 1212 Grand-Lancy',
     'Transports publics recommandés. Arrêt Tpg Lancy-Bachet ou gare CEVA Lancy-Bachet (10mn à pied). Accès et parking difficiles pour les voitures.',
     'ACGJJJ',
     'Membre d\'un club de l\'association cantonale genevoise de judo et ju-jitsu Licence annuelle 2024 obligatoire. Ceux nés en 2014 ne sont pas autorisés à combattre.',
     'Compétitions individuelles. Pool jusqu\'à cinq combattants. Dès six combattants : pools au premier tour puis tableau sans repêchage.',
     'Une médaille pour les quatre premiers + le titre de « champion(ne) genevois(e) pour la première place.',
     'Assurés par les arbitres officiels de la Fédération Suisse de Judo.',
     'Judogi blanc uniquement. Cheveux longs attachés (chignon). T-shirt blanc pour les combattantes.',
     'info@acgjjj.ch, Alexandre Perles 079 260 79 67, Stéphane Fischer 077 421 15 67',
     '2024-06-01',
     '2024-06-04',
     '2024-06-04'   
)");  
                $mysqli->query("INSERT INTO TournamentDoubleSatrt (MainAgeCategoryId, AcceptedAgeCategoryId)
SELECT TCA_1.Id, TCA_2.Id FROM TournamentAgeCategory TCA_1
INNER JOIN TournamentAgeCategory TCA_2 ON TCA_2.ShortName='FE'
WHERE TCA_1.ShortName='F21'");
                $mysqli->query("INSERT INTO TournamentDoubleSatrt (MainAgeCategoryId, AcceptedAgeCategoryId)
SELECT TCA_1.Id, TCA_2.Id FROM TournamentAgeCategory TCA_1
INNER JOIN TournamentAgeCategory TCA_2 ON TCA_2.ShortName='ME'
WHERE TCA_1.ShortName='M21'");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 11:30:00', '2024-06-04 12:00:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='F13';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 08:00:00', '2024-06-04 08:30:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='M13';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 11:30:00', '2024-06-04 12:00:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='F15';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 10:45:00', '2024-06-04 11:15:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='M15';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 13:00:00', '2024-06-04 13:30:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='F18';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 12:30:00', '2024-06-04 13:00:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='M18';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 14:30:00', '2024-06-04 15:00:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='F21';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 14:00:00', '2024-06-04 14:30:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='M21';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 15:00:00', '2024-06-04 15:30:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='FE';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 14:45:00', '2024-06-04 15:15:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='ME';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 13:30:00', '2024-06-04 14:00:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='MV';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 15:15:00', '2024-06-04 15:45:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='FO';");
                $mysqli->query("INSERT INTO TournamentWeighting (AgeCategoryId, WeightingBegin, WeightingEnd ) SELECT TCA_1.Id, '2024-06-04 15:15:00', '2024-06-04 15:45:00' FROM TournamentAgeCategory TCA_1 WHERE TCA_1.ShortName='MO';
");

                $mysqli->query("DELETE FROM TournamentClub;");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(1, 'BUDO SCHOOLS ASHITA');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(2, 'DOJO LANCY–PALETTES');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(3, 'ECOLE DE JUDO DE COLLONGE-BELLERIVE');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(4, 'GOSHINJUTSU-KWAI DARDAGNY');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(5, 'JU-JITSU JUDO CLUB COMPESIÈRES');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(6, 'JUDO CLUB BUDOKAN VERNIER');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(7, 'JUDO CLUB CAROUGE');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(8, 'JUDO CLUB DES EAUX-VIVES');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(9, 'JUDO CLUB GENEVE');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(10, 'JUDO CLUB GRAND-SACONNEX');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(11, 'JUDO CLUB LE SAMOURAI BERNEX');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(12, 'JUDO CLUB LEMANIQUE');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(13, 'JUDO CLUB MEYRIN');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(14, 'JUDO CLUB SATIGNY');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(15, 'JUDO CLUB TROIS-CHÊNES');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(16, 'JUDO CLUB VERSOIX');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(17, 'JUDO KWAI LANCY');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(18, 'JUDO PREGNY-CHAMBESY');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(19, 'SHINBUDO JUDO');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(20, 'SHINBUDO-ONEX');");
                $mysqli->query("INSERT INTO TournamentClub (Id ,Name) VALUES(21, 'SHUNG DO KWAN BUDO');");
                
                
                $mysqli->query("DELETE FROM OTA;");
                $mysqli->query("DELETE FROM TournamentSiteUser;");
                $mysqli->query("INSERT INTO TournamentSiteUser(CreatedOn,EMail,Salt, Password, DisplayName, IsAdmin) VALUES('2024-06-21 09:56:33', 'admin', '2024-06-21:07:06:33', 'bad7751919f019226e87a0722aa14343', 'Utilisateur Test', 1);");
                
                
                
// P@ssW0rd_test_Admin

  
                $mysqli->commit();
                header('Location: site/identification.php');
                
            } catch (mysqli_sql_exception $exception) {
                $mysqli->rollback();
                throw $exception;
            }
           
           
           
         } else  if ($cmd==1){
            //3days before
            $NewDate=Date('Y-m-d', strtotime('+3 days'));
            $stmt = $mysqli->prepare("UPDATE TournamentVenue SET RegistrationEnd=?,TournamentStart=?,TournamentEnd=?");
            $stmt->bind_param("sss", $NewDate, $NewDate, $NewDate  );
            $stmt->execute();
            $stmt->close();

            $stmt = $mysqli->prepare("
            UPDATE TournamentWeighting TW1 
            INNER JOIN TournamentWeighting TW2 ON TW1.AgeCategoryId = TW2.AgeCategoryId
            SET TW1.WeightingBegin = concat(DATE(?),' ',TIME(TW2.WeightingBegin)), TW1.WeightingEnd = concat(DATE(?),' ',TIME(TW2.WeightingEnd))
            ");
            $stmt->bind_param("ss", $NewDate, $NewDate);
            $stmt->execute();
            $stmt->close();
            $message='Modifications enregistrées: Tournois dans 3 jours';
         } else  if ($cmd==2){
           // 1h before weighting
            $new_dt = new DateTime("now",new DateTimeZone('Europe/Zurich'));
            $NewDate=  $new_dt->format('Y-m-d');    
            $new_dt->modify('+ 1 hour');
            $NewTime= $new_dt->format('Y-m-d H:i:s');    
            $new_dt->modify('+ 1 hour');
            $NewTimeEnd= $new_dt->format('Y-m-d H:i:s'); 
            $stmt = $mysqli->prepare("UPDATE TournamentVenue SET RegistrationEnd=?,TournamentStart=?,TournamentEnd=?");
            $stmt->bind_param("sss", $NewDate, $NewDate, $NewDate  );
            $stmt->execute();
            $stmt->close();
            
            $stmt = $mysqli->prepare("
            UPDATE TournamentWeighting TW1 
            INNER JOIN TournamentWeighting TW2 ON TW1.AgeCategoryId = TW2.AgeCategoryId
            SET TW1.WeightingBegin = concat(DATE(?),' ',TIME(TW2.WeightingBegin)), TW1.WeightingEnd = concat(DATE(?),' ',TIME(TW2.WeightingEnd))
            ");
            $stmt->bind_param("ss", $NewDate, $NewDate);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $mysqli->prepare("
            UPDATE TournamentWeighting SET WeightingBegin=?,  WeightingEnd=? WHERE AgeCategoryId=?");
            $stmt->bind_param("ssi", $NewTime, $NewTimeEnd, $elitMCatId);
            $stmt->execute();
            $stmt->close();
            $message='Modifications enregistrées: Ouverture de la pesée des Elites M dans 1h'. $new_dt->format('Y-m-d H:i:s'); ;
            
         } else  if ($cmd==3){
           // during weighting
            $new_dt = new DateTime("now",new DateTimeZone('Europe/Zurich'));
            $NewDate=  $new_dt->format('Y-m-d');    
            $new_dt->modify('- 1 hour');
            $NewTime= $new_dt->format('Y-m-d H:i:s');    
            $new_dt->modify('+ 2 hour');
            $NewTimeEnd= $new_dt->format('Y-m-d H:i:s'); 
            $stmt = $mysqli->prepare("UPDATE TournamentVenue SET RegistrationEnd=?,TournamentStart=?,TournamentEnd=?");
            $stmt->bind_param("sss", $NewDate, $NewDate, $NewDate  );
            $stmt->execute();
            $stmt->close();
            
            $stmt = $mysqli->prepare("
            UPDATE TournamentWeighting TW1 
            INNER JOIN TournamentWeighting TW2 ON TW1.AgeCategoryId = TW2.AgeCategoryId
            SET TW1.WeightingBegin = concat(DATE(?),' ',TIME(TW2.WeightingBegin)), TW1.WeightingEnd = concat(DATE(?),' ',TIME(TW2.WeightingEnd))
            ");
            $stmt->bind_param("ss", $NewDate, $NewDate);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $mysqli->prepare("
            UPDATE TournamentWeighting SET WeightingBegin=?,  WeightingEnd=? WHERE AgeCategoryId=?");
            $stmt->bind_param("ssi", $NewTime, $NewTimeEnd, $elitMCatId);
            $stmt->execute();
            $stmt->close();
            $message='Modifications enregistrées: Pendant la pesée des Elites M';
         } else  if ($cmd==4){
           // after weighting 
            $new_dt = new DateTime("now",new DateTimeZone('Europe/Zurich'));
            $NewDate=  $new_dt->format('Y-m-d');    
            $new_dt->modify('- 2 hour');
            $NewTime= $new_dt->format('Y-m-d H:i:s');    
            $new_dt->modify('+ 1 hour');
            $NewTimeEnd= $new_dt->format('Y-m-d H:i:s'); 
            $stmt = $mysqli->prepare("UPDATE TournamentVenue SET RegistrationEnd=?,TournamentStart=?,TournamentEnd=?");
            $stmt->bind_param("sss", $NewDate, $NewDate, $NewDate  );
            $stmt->execute();
            $stmt->close();
            
            $stmt = $mysqli->prepare("
            UPDATE TournamentWeighting TW1 
            INNER JOIN TournamentWeighting TW2 ON TW1.AgeCategoryId = TW2.AgeCategoryId
            SET TW1.WeightingBegin = concat(DATE(?),' ',TIME(TW2.WeightingBegin)), TW1.WeightingEnd = concat(DATE(?),' ',TIME(TW2.WeightingEnd))
            ");
            $stmt->bind_param("ss", $NewDate, $NewDate);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $mysqli->prepare("
            UPDATE TournamentWeighting SET WeightingBegin=?,  WeightingEnd=? WHERE AgeCategoryId=?");
            $stmt->bind_param("ssi", $NewTime, $NewTimeEnd, $elitMCatId);
            $stmt->execute();
            $stmt->close();
            $message='Modifications enregistrées: Après la pesée des Elites M';
         }  
    }
}




echo'<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="site/css/test.css" />
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


echo'
<body>
    <div class="f_cont">';
    
    $isLoged =  !empty($_SESSION['_UserId']);
 echo '<div id="band" class="band" >
             <div class="b_title" onclick="toggleClass(document.getElementById(\'band\'), \'b_closed\')"> 
                  <img id="logo" src="site/css/Logo_ACG_JJJ.png" height="30px"></img>
                  <div class="b_i_title">ACGJJJ</div>';
             
    if ($isLoged) {echo '<a class="lgout" href="site/identification.php">logout</a>';}        
             echo' </div>';
    
    if (!$isLoged){
    echo'
       <span class="h_title">
               <form action="site/identification.php" method="post">
               <input   type="hidden" name="rtn" value="../index.php" />
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
   
       echo ' 
       <div class="m_b">
           <span class="m_b_t">SITE DE TEST</span>
           <a href="site/index.php" target="_blanck">Page d\'accueil</a>
         
       </div>';
    
    
    }
    echo'         
    
    
    
         </div>';



    echo'        
       <div class="cont_l">
         <div class="h">	      
           <span class="h_title">
              TEST DE LA WEBAPP POUR LES TOURNOIS
            </span>';
            
	if ($message!='') {echo'<span class="fmessage">'.$message.'</span>';}
if ($_SESSION['_IsAdmin'] ==1){
   echo'
        <span class="h_title">Remise à zéro</span>
        <form action="./index.php" method="post" style="text-align:center;">
              <input type="hidden" name="j3" value="-1"/>
              Tous remettre à zéro (y compris le mot de passe de l\'admin) - vous devrez vous identifier à nouveau avec les information originales
	      <input class="pgeBtn"  type="submit" value="Remise à zéro des testes"> 
	</form><br/><br/> 
	<span class="h_title">Télécharger le mode d\'emplois</span>
        <div style="display:block; width:100%;text-align:center;">
        Fichier  pdf : <a class="pgeBtn" target="_blanck" href="ModeEmplois.pdf">Mode d\'emploi</a><br/><br/>
        </div>
        <span class="h_title">Télécharger des données de test</span>
        <div style="display:block; width:100%;text-align:center;">
        Fichier  Excel : <a class="pgeBtn" target="_blanck" href="MaterielTest/Inscriptions_1.xlsx">Inscriptions 1 </a><br/><br/>
        Fichier  Excel : <a class="pgeBtn" target="_blanck" fref="MaterielTest/Inscriptions_2.xlsx">Inscriptions 2 </a><br/><br/>
        Fichier  Excel : <a class="pgeBtn" target="_blanck" fref="MaterielTest/Inscriptions_3.xlsx">Inscriptions 3 </a><br/><br/>
        </div>
        <span class="h_title">Changer la date/horaires pour...</span>
        tester les inscriptions groupées, la générations des cartes de combatants, ... :
        <form action="./index.php" method="post"  style="text-align:center;">
              <input type="hidden" name="j3" value="1"/>
	      <input class="pgeBtn"  type="submit" value="3 jours avant le tournoi"> 
	</form><br/><br/>
	 tester l\'accueil, remise de la carte de combatant, l\'inscription depuis une carte vierge, ... :
	<form action="./index.php" method="post"  style="text-align:center;">
              <input type="hidden" name="j3" value="2"/>
	      <input class="pgeBtn"  type="submit" value="1h avnt la pesée des élite M"> 
	</form><br/><br/>
	 tester la pesée des catégories Elites M :
	<form action="./index.php" method="post"  style="text-align:center;">
              <input type="hidden" name="j3" value="3"/>
	      <input class="pgeBtn"  type="submit" value="Milieu de la pesée des élite M"> 
	</form><br/><br/>
	 tester la création des catégories Elites M, simuler des combats, les résutats, les cartes pour noter les PV, ... :
	<form action="./index.php" method="post"  style="text-align:center;">
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

