<?php

ob_start();
session_name("Tournament");	
session_start();

if (empty($_SESSION['_UserId'] || $_SESSION['_IsMainTable']!=1)) {
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






include '_commonBlock.php';

writeHead();

echo'
<body>
    <div class="f_cont">';

echo'        
       <div class="cont_l">
         <div class="h">'; 
         
if (! empty($_GET['cid']) ) {
    //// Creation of the category
    
    // Check the cat is not already taken
    $mysqli= ConnectionFactory::GetConnection();
    $stmt = $mysqli->prepare("SELECT Id FROM ActualCategory WHERE CategoryId=? OR Category2Id=?");
    $stmt->bind_param("ii", $_GET['cid'],$_GET['cid']);         
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
           (Selectionner une autre catégorie pour les grouper)
	      <form action="./acat.php" method="post"> ';
        // get other category not taken
        $stmt = $mysqli->prepare("SELECT 
                                      TC1.Id,
                                      TournamentAgeCategory.Name,
                                      TournamentAgeCategory.ShortName,
                                      TournamentGender.Name,     
                                      IFNULL(-TC1.MaxWeight, IFNULL(TC1.MinWeight,'OPEN')),
                                      count(DISTINCT V3.CompetitorId) 
                                  FROM TournamentCategory TC1
                                  INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id=TC1.AgeCategoryId
                                  INNER JOIN TournamentGender on TournamentGender.Id=TournamentAgeCategory.GenderId
                                  INNER JOIN TournamentWeighting on TournamentAgeCategory.Id = TournamentWeighting.AgeCategoryId
                                  LEFT OUTER JOIN V_Category V3 on TC1.Id = V3.CategoryId  AND V3.WeightChecked=1
                                  
                                  INNER JOIN TournamentCategory TC2 ON TC1.AgeCategoryId = TC2.AgeCategoryId 
                                  LEFT OUTER JOIN ActualCategory ON ActualCategory.CategoryId=TC1.Id  OR ActualCategory.Category2Id=TC1.Id 
                                  WHERE ActualCategory.Id IS NULL AND TC2.Id=?
                                  
                                  GROUP BY  TC1.Id, 
                                             TournamentAgeCategory.Name,
                                             TournamentAgeCategory.ShortName,
                                             TournamentGender.Name,
                                             IFNULL(-TC1.MaxWeight, IFNULL(TC1.MinWeight,'OPEN'))
                                  ORDER BY IFNULL(-TC1.MaxWeight, IFNULL(TC1.MinWeight,'OPEN'))
                                  ");               
        $stmt->bind_param("i", $_GET['cid']);         
        $stmt->bind_result( $catId, $cat_n,$cat_sn,$cat_gen,$weight,  $weighted);   
        $stmt->execute(); 
        $last_cat=''; 
        $found=false;
        
        $choice='';
        while($stmt->fetch()) {
           if ($catId==$_GET['cid']) {
                $choice=$choice.$last_cat.'<span> <input type="checkbox" name="ccid" checked disabled/> <input type="hidden" name="ccid" value="'.$catId.'" /> Category '.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.' avec '.$weighted.' inscrit(s)</span>';
                $found = true;
                echo 'Nom de la catégorie <input type="text" name="name" value="'.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.'" />';
           } else {
               if (!$found) {
                    $last_cat='<span> <input type="checkbox" name="pcid" value="'.$catId.'" > Category '.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.' avec '.$weighted.' inscrit(s)</span>';
               } else {
               
                $choice=$choice.'<span> <input type="checkbox" name="ncid" value="'.$catId.'"> Category '.$cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight.' avec '.$weighted.' inscrit(s)</span>';
                 break;
               }
           $curr_cat=''; 
           
        }
    
    }
        $stmt->close(); 
        echo $choice.' <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Crée les feuilles de combats">
	               <a class="pgeBtn" href="listingcat.php">Annuler</a>
	       </span>
	       </form>';
}
}     
         
         
     echo '   
           </div>     
        </div>   
     </div>
</body>
</html>';
?>
