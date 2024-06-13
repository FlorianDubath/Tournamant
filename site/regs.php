<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsRegistration']!=1 && $_SESSION['_IsMainTable']!=1 ) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';

function isOpenCat($agecatid){
    $mysqli= ConnectionFactory::GetConnection();
    $stmt = $mysqli->prepare("SELECT Name FROM TournamentAgeCategory WHERE Id=? ");
    $stmt->bind_param('i',$agecatid);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
    return $name=='Open';
}

function loadYear(){
    $mysqli= ConnectionFactory::GetConnection();
    $stmt = $mysqli->prepare("SELECT TournamentStart FROM TournamentVenue order by Id desc limit 1");
    $stmt->execute();
    $stmt->bind_result( $TournamentStart);
    $stmt->fetch();
    $stmt->close();
    return (int)date('Y', strtotime($TournamentStart)) ;
}

function loadGender($agecatid){
    $mysqli= ConnectionFactory::GetConnection();
    $stmt = $mysqli->prepare("SELECT GenderId FROM TournamentAgeCategory WHERE Id=? ");
    $stmt->bind_param('i',$agecatid);
    $stmt->execute();
    $stmt->bind_result($genderid);
    $stmt->fetch();
    $stmt->close();
    return $genderid;
}
 
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

function getOpenCat($agecatid){
    $mysqli= ConnectionFactory::GetConnection();
    $stmt = $mysqli->prepare("SELECT Id FROM TournamentCategory WHERE AgeCategoryId=? ");
    $stmt->bind_param('i',$agecatid);
    $stmt->execute();
    $stmt->bind_result($catId);
    $stmt->fetch();
    $stmt->close();
    return array("value"=>$catId, "error"=>False);;
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

function parseLicence($str) {
    $i_l = (int)$str;
    if ($i_l>=100000 && $i_l<1000000) {
        return array("value"=>$i_l, "error"=>False);
    } else {
        return array("value"=>-1, "error"=>True, "message"=>'"'.$str.'" N\'est pas un numéro de licence reconnu');
    }
}

function parseName($str) {
    $nm = trim($str);
    if (strlen($nm)>0) {
        return array("value"=>$nm, "error"=>False);
    } else {
        return array("value"=>-1, "error"=>True, "message"=>'"'.$str.'" N\'est pas un nom valide');
    }
}

/*
CREATE TABLE TournamentCompetitor(
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY , 
  StrId  VARCHAR( 12 ) NOT NULL,
  Name VARCHAR( 255 ) NOT NULL,
  Surname VARCHAR( 255 ) NOT NULL,
  Birth  Date NOT NULL, 
  GenderId INT NOT NULL , 
  LicenceNumber INT NOT NULL , 
  GradeId  INT NOT NULL , 
  ClubId  INT NOT NULL , 
  CheckedIn TINYINT NOT NULL DEFAULT 0,
  
  CONSTRAINT fk_comp_gen FOREIGN KEY (GenderId) REFERENCES TournamentGender(Id),
  CONSTRAINT fk_comp_grade FOREIGN KEY (GradeId) REFERENCES TournamentGrade(Id),
  CONSTRAINT fk_comp_club FOREIGN KEY (ClubId) REFERENCES TournamentClub(Id)
);  


CREATE TABLE TournamentRegistration(
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  CompetitorId INT NOT NULL, 
  CategoryId INT NOT NULL,
  Payed TINYINT NOT NULL DEFAULT 0,
  WeightChecked TINYINT NOT NULL DEFAULT 0,
  CONSTRAINT fk_reg_com FOREIGN KEY (CompetitorId) REFERENCES TournamentCompetitor(Id),
  CONSTRAINT fk_reg_cat FOREIGN KEY (CategoryId) REFERENCES  TournamentCategory(Id)
);
*/

function insertCompetitor($name, $surname, $birth, $gender, $licence, $grade, $club){
   // check for user with same license, if same name warning + return id else error
   // else return inserted id
}

function insertRegistration($compid, $catid){
   // check the combinaison do not exist => error
   // else insert +ok
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
    $clubid = (int)$_POST['cid'];
    $agecatid = (int)$_POST['agcatid'];
    $isOpen = isOpenCat($agecatid);
    
    $year = loadYear();
    $grades = loadGrade();
    $w_cats = loadWeight($agecatid);
    $ages = loadAge($agecatid);
    $gender = loadGender($agecatid);

    $lines = preg_split('/\r\n|[\r\n]/', $_POST['bulk']);
    echo '<span class="ftitle"> DONNEES TRAITEES :</span>
          <table class="wt t4">
	     <tr class="tblHeader">
	         <th>Nom</th>
	         <th>Prénom</th>
	         <th>Status</th>
	     </tr>';
    foreach($lines as $line){
        if (strlen(trim($line))>0) {
            $words = str_getcsv($line, "\t",  "", $escape = "\\");
            $parsed=array();
            if ($isOpen && count($words)==5){
                $parsed['surname'] = parseName($words[0]);
                $parsed['name']  = parseName($words[1]);
                $parsed['birth']  = parseDate($ages, $year, $words[2]);
                $parsed['grade'] = parseGrade($grades, $words[3]);
                $parsed['licence'] = parseLicence($words[4]);
                $parsed['cat'] = getOpenCat($agecatid);
            
            } else if (!$isOpen && count($words)==6){
                $parsed['surname'] = parseName($words[0]);
                $parsed['name']  = parseName($words[1]);
                $parsed['birth']  = parseDate($ages, $year, $words[2]);
                $parsed['grade'] = parseGrade($grades, $words[3]);
                $parsed['licence'] = parseLicence($words[4]);
                $parsed['cat'] = parseWCat($w_cats, $words[5]);
            } 
            
            if (count($parsed)!=6) {
                echo '<tr class="dta_inv"><td colspan="3">La ligne "'.$line.'" n\'as pas le bon nombre de champs, elle a été ignorée.</td></tr>';
            } else {
                 $valid=True;
                 $message='';
                 foreach($parsed as $filed=>$res){
                     if ($res["error"]){
                         $valid=False;
                         $message=$message.'<br/>'.$res["message"];
                     }
                 }
                 
                 if ($valid){
                     //TODO check for unique License number, unique in this category, insert into DB, display result
                     echo '<tr><td >'.$parsed["surname"]["value"].'</td><td >'.$parsed["name"]["value"].'</td><td >&check; Enregistrement réussi.</td></tr>';
                 } else {
                    echo '<tr class="dta_inv"><td colspan="3">La ligne "'.$line.'" n\'est pas valide, elle a été ignorée : '.$message.'</td></tr>';
                 }
            }
           
            
        }
    }
    
    echo '</table><br/>
    <span class="ftitle"> NOUVELLES DONNEES :</span>';
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
	                                     INNER JOIN TournamentWeighting ON TournamentWeighting.AgeCategoryId = TAC_1.Id
	                                    
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

