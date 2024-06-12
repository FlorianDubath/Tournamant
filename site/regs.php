<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsRegistration']!=1 && $_SESSION['_IsMainTable']!=1 ) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';


 
function loadGrade(){
    $mysqli= ConnectionFactory::GetConnection();
    $stmt = $mysqli->prepare("SELECT Id, Name FROM TournamentGrade ");
    $stmt->execute();
    $stmt->bind_result($Id,$name);
    $grades=array();
    while ($stmt->fetch()){
        $grades[$name]=$Id;
    }
    $stmt->close();
    return $grades;
}

function loadWeight($agecatid){
    $mysqli= ConnectionFactory::GetConnection();
    $stmt = $mysqli->prepare("SELECT Id, IFNULL(MaxWeight, concat('+',IFNULL(MinWeight,'OPEN'))) FROM TournamentCategory WHERE AgeCategoryId=? ");
    $stmt->bind_param('i',$agecatid);
    $stmt->execute();
    $stmt->bind_result($Id,$weight);
    $cats=array();
    while ($stmt->fetch()){
        $cats[$weight]=$Id;
    }
    $stmt->close();
    return $cats;
}

function loadAge($agecatid) {
    $mysqli= ConnectionFactory::GetConnection();
    $stmt = $mysqli->prepare("  SELECT MIN(LEAST(AC_1.MinAge,IFNULL(AC_2.MinAge,100))), MAX(GREATEST(AC_1.MaxAge,IFNULL(AC_2.MaxAge,0))) 
                                FROM TournamentAgeCategory  AC_1
                                LEFT OUTER JOIN TournamentDoubleSatrt on AC_1.Id=AcceptedAgeCategoryId
                                LEFT OUTER JOIN TournamentAgeCategory  AC_2  on AC_1.Id=MainAgeCategoryId
                                WHERE AC_1.Id=?");
    $stmt->bind_param('i',$agecatid);
    $stmt->execute();
    $stmt->bind_result($min,$max);
    $stmt->fetch();
    $stmt->close();
    return array($min,$max);
}

function parseGrade($grades, $str) {
    if (array_key_exists($str,$grades)){
        return array("value"=>$grades[$str], "error"=>False);
    } else {
        return array("value"=>-1, "error"=>True, "message"=>'"'.$str.'" N\'est pas un grade reconnu');
    }
}

function parseDate($ages, $year, $str) {
    $time = strtotime($str);
    if ($time) {
        $age = $year-(int)date("Y", $time );
        if ($age>=$ages[0] && $age<$ages[1]) {
            return array("value"=> date("Y-m-d", $time ), "error"=>False);
        } else {
            return array("value"=>-1, "error"=>True, "message"=>'L\'age ('.$age.') n\'est pas accepté pour cette catégorie (ou les doubles départs)');
        }
    } else {
        return array("value"=>-1, "error"=>True, "message"=>'"'.$str.'" N\'est pas une date reconnue');
    }
}

function parseWCat($wcats, $str) {
    $cleaned = preg_replace('/(\'?-)/', '', $str);
    if (array_key_exists($cleaned,$wcats)){
        return array("value"=>$wcats[$cleaned], "error"=>False);
    } else {
        return array("value"=>-1, "error"=>True, "message"=>'"'.$str.'" N\'est pas un poid reconnu');
    }
}

$mysqli= ConnectionFactory::GetConnection();
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
              INSCRIPTIONS GROUPEES
            </span>
            <span class="h_txt">
                 <span class="btnBar"> 
                           <a class="pgeBtn" href="listingreg.php">Annuler/Fermer</a>
                 </span>

	      
	      ';

if ((int)$_POST['cid'] && (int)$_POST['agcatid'] && $_POST['bulk']) {

    $grades = loadGrade();
    $w_cats = loadWeight((int)$_POST['agcatid'] );
    $ages = loadAge((int)$_POST['agcatid']);

    $lines = preg_split('/\r\n|[\r\n]/', $_POST['bulk']);
    
    foreach($lines as $line){
        if (strlen(trim($line))>0) {
            echo '<br/>'.$line.'<br/>'.'<br/>';
            $words = str_getcsv($line, "\t",  "", $escape = "\\");
            var_dump( parseDate($ages, 2025, $words[2]));
            var_dump( parseGrade($grades, $words[3]));
            var_dump( parseWCat($w_cats, $words[5]));
            echo '<br/>';
        }
    }
}


//TODO register pending and validate them (with message)
// split on linebreak preg_split('/\r\n|[\r\n]/', $_POST['thetextarea'])
/*str_getcsv(
    $_POST['bulk'],
    string $separator = "\t",
    string $enclosure = "",
    string $escape = "\\"
): array
	    
*/	  

	         
if ($message!='') {echo'<span class="fmessage">'.$message.'</span>';}

echo'
               <form action="./regs.php" method="post" Id="F1">
	       <span class="fitem">
               <span class="label">Club :</span>
               <select name="cid"  form="F1">';
               $stmt = $mysqli->prepare("SELECT Id, Name FROM TournamentClub ORDER BY Name");
      	        $stmt->execute();
               $stmt->bind_result($ccId,$ccname);
               while ($stmt->fetch()){
                  $sel ='';
                  if ($ccId==$cid) { $sel=' selected ';}
                  echo '<option value="'.$ccId.'" '.$sel.'>'.$ccname.'</option>';
               }
	           $stmt->close();
               
               echo'
                  </select><br/>
	       </span>
	       <span class="fitem">
                 <span class="label">Catégorie d\'âge :</span>
	        <select name="agcatid"  form="F1">';
	             $stmt = $mysqli->prepare("SELECT 
	                                            TAC_1.Id,
	                                            TAC_1.Name,
	                                            TAC_1.ShortName,
	                                            TournamentGender.Name
	                                     FROM TournamentAgeCategory TAC_1 
	                                     INNER JOIN TournamentGender ON TournamentGender.Id=TAC_1.GenderId
	                                    
	                                     ORDER BY IFNULL(TAC_1.MinAge,IFNULL(TAC_1.MaxAge,1000))");       
      	       $stmt->execute();
               $stmt->bind_result($agcatid,$trname,$trshort,$gender);
               while ($stmt->fetch()){
                  echo '<option value="'.$agcatid.'">'.$trshort.' '.$trname.' '.$gender.'</option>';
               }
	           $stmt->close();    
	           echo'
                </select> 
               </span>
	       <span class="fitem" width="100%" > 
	       <span class="label">Copier le contenu du tableur :</span><br/>
	       <textarea  name="bulk" rows="30"  style="display:block;width:100%;"></textarea>
                </span>
                ';
	        
	        
	        



echo'
	       <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Enregistrer" form="F1">
	               <a class="pgeBtn" href="listingreg.php">Annuler/Fermer</a>
	       </span>';
	       
	       
	       
echo'	       
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

