<?php

ob_start();
session_name("Tournament");	
session_start();

if (empty($_SESSION['_UserId']) || $_SESSION['_IsMainTable']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';

if (!empty($_POST['ccid'])){  
    include '_categoryHelper.php';
    $cid = $_POST['ccid'];
    $ocid=-1;
    if (!empty($_POST['ncid'])){
        $ocid=$_POST['ncid'];
    }
    if (!empty($_POST['pcid'])){
        $ocid=$_POST['pcid'];
    }
    
    open_Category($_POST['ccid'],$ocid,$_POST['name']);

    header('Location: ./listingcat.php');
}

$curr_cat_id = $_GET['cid'];


include '_commonBlock.php';

writeHead();

echo'
<body>
    <div class="f_cont">';

echo'        
       <div class="cont_l">
         <div class="h">'; 
writeBand(); 
         





$message='';
         
if (! empty($_GET['pscid']) ) {
    $mysqli= ConnectionFactory::GetConnection();
    $stmt = $mysqli->prepare("
                                SELECT TournamentCategory.Id,
                                   count(TournamentRegistration.Id),
                                   ActualCategory.Id
                                FROM TournamentCategory
                                LEFT OUTER JOIN ActualCategory ON ActualCategory.CategoryId=TournamentCategory.Id AND ActualCategory.Dummy=1
                                LEFT OUTER JOIN TournamentRegistration ON TournamentRegistration.CategoryId=TournamentCategory.Id AND WeightChecked=1 
                                WHERE TournamentCategory.Id=?
                                GROUP BY TournamentCategory.Id, ActualCategory.Id");
    $stmt->bind_param("i", $curr_cat_id);         
    $stmt->bind_result($cat_id, $number, $acat_id);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close(); 
    
    
    if ($number==1) {
       if(empty($acat_id)){
           include '_categoryHelper.php';
          // crée la actual catégorie
          // crée le résultats 
           promote_Unique($cat_id);
           $message="Promotion d'un participant unique réussie.";
       
       } else {
       // check n'existe pas encore
       $message="Promotion d'un participant unique déjà effectuée.";
       }
    } else {
     // check pas plus d'un inscrit
     $message="Promotion d'un participant: Il n'y a pas qu'un seul inscrit dans cette catégorie.";
    }
     
}





if (! empty($curr_cat_id) ) {
    //// Creation of the category
    
    // Check the cat is not already taken
    $mysqli= ConnectionFactory::GetConnection();
    $stmt = $mysqli->prepare("SELECT Id FROM ActualCategory WHERE (CategoryId=? OR Category2Id=?) AND Dummy=0 ");
    $stmt->bind_param("ii", $curr_cat_id,$curr_cat_id);         
    $stmt->bind_result($actual_cat_id);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close(); 
    
    if ($actual_cat_id && $actual_cat_id>0) {
       // Problem! go out
       header('Location: ./index.php');
    } else {
     echo'<span class="h_title">
               VALIDATION DE LA CATEGORIE
           </span>
           <span class="btnBar"> 
                   <a class="pgeBtn" href="listingcat.php" title="Annuler" >Annuler</a>
               </span>';
               
         
if ($message!='') {echo'<span class="fmessage">'.$message.'</span>';}      
               
   echo '            
           <span class="f_info">(Selectionner une autre catégorie pour les grouper)</span>
	      <form action="./acat.php" method="post" Id="F1"> ';
        // get other category not taken
        $stmt = $mysqli->prepare("SELECT
                                      TC1.Id,
                                      TournamentAgeCategory.Name,
                                      TournamentAgeCategory.ShortName,
                                      TournamentGender.Name,     
                                      IFNULL(-TC1.MaxWeight, IFNULL(concat('+',TC1.MinWeight),'OPEN')),
                                      count(DISTINCT V3.CompetitorId), 
                                      AAC2.Id
                                  FROM TournamentCategory TC1
                                  INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id=TC1.AgeCategoryId
                                  INNER JOIN TournamentGender on TournamentGender.Id=TournamentAgeCategory.GenderId
                                  INNER JOIN TournamentWeighting on TournamentAgeCategory.Id = TournamentWeighting.AgeCategoryId
                                  LEFT OUTER JOIN V_Category V3 on TC1.Id = V3.CategoryId  AND V3.WeightChecked=1
                                  
                                  INNER JOIN TournamentCategory TC2 ON TC1.AgeCategoryId = TC2.AgeCategoryId 
                                  LEFT OUTER JOIN ActualCategory AAC1 ON (AAC1.CategoryId=TC1.Id  OR AAC1.Category2Id=TC1.Id ) AND AAC1.Dummy=0
                                  LEFT OUTER JOIN ActualCategory AAC2 ON AAC2.CategoryId=TC1.Id AND AAC2.Dummy=1
                                  WHERE AAC1.Id IS NULL AND TC2.Id=?
                                  
                                  GROUP BY  TC1.Id, 
                                             TournamentAgeCategory.Name,
                                             TournamentAgeCategory.ShortName,
                                             TournamentGender.Name,
                                             IFNULL(-TC1.MaxWeight, IFNULL(TC1.MinWeight,'OPEN')),
                                             AAC2.Id
                                  ORDER BY IFNULL(-TC1.MaxWeight, IFNULL(TC1.MinWeight,'OPEN'))
                                  ");               
        $stmt->bind_param("i", $curr_cat_id);         
        $stmt->bind_result( $catId, $cat_n,$cat_sn,$cat_gen,$weight,  $weighted, $dummy);   
        $stmt->execute(); 
        $last_cat=''; 
        $found=false;
        
        $choice='';
        while($stmt->fetch()) {
        
           if ($catId==$curr_cat_id) {
                $choice=$choice.$last_cat.'<span class="f_info"> <input type="checkbox" name="ccid"  checked disabled/> <input type="hidden" name="ccid" value="'.$catId.'" /> Category '.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.' avec '.$weighted.' inscrit(s)';
                
                if ($weighted==1 && empty($dummy)){
                    $choice=$choice.' <a class="pgeBtn" href="./acat.php?pscid=1&cid='.$catId.'"> Promouvoir l\'unique inscrit comme champion</a>';
                }
                
                $choice=$choice.'</span>';
                
                $found = true;
                echo '<span class="f_info">Nom de la catégorie : <input type="text" name="name" value="'.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.'" /></span>';
           } else {
               if (!$found) {
                    $last_cat='<span  class="f_info"> <input type="checkbox" name="pcid" value="'.$catId.'" > Category '.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.' avec '.$weighted.' inscrit(s)</span>';
               } else {
               
                $choice=$choice.'<span class="f_info"> <input type="checkbox" name="ncid" value="'.$catId.'" > Category '.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.' avec '.$weighted.' inscrit(s)</span>';
                 break;
               }
           $curr_cat=''; 
           
        }
    
    }
        $stmt->close(); 
        echo $choice.' <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Créer les feuilles de combats" >
	               <a class="pgeBtn" href="listingcat.php">Annuler</a>
	       </span>
	        </form>
	      ';
}
}     
         
         
     echo '   
           </div>     
        </div>   
     </div>
</body>
</html>';
?>
