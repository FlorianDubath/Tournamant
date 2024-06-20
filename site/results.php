<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsMainTable']!=1 && $_SESSION['_IsAdmin']!=1) {
	header('Location: ./index.php');
}


$acid = (int)$_GET['acid'];

include '_commonBlock.php';
writeHead();

 include 'connectionFactory.php';

$mysqli= ConnectionFactory::GetConnection(); 

echo '
<body>
    <div class="f_cont">
       <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 
    
    
writeBand();
    echo'
    
    <div>
        <span class="h_title">
               GENERER LES RESULTATS PAR CATEGORIE
            </span>
      
        <form action="./results.php" method="get">
        <span class="label">Catégorie :</span>
              <select name="acid"><option value="-1" >Tous</option> <option style="font-size: 1pt; background-color: #000000;" disabled>&nbsp;</option>';
               $stmt = $mysqli->prepare(" SELECT 
                           ActualCategory.Id, 
                           ActualCategory.Name 
               FROM ActualCategory 
               INNER JOIN TournamentCategory ON TournamentCategory.Id = ActualCategory.CategoryId
               INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId
               INNER JOIN TournamentWeighting ON TournamentWeighting.AgeCategoryId = TournamentAgeCategory.Id
               ORDER bY TournamentWeighting.WeightingEnd, TournamentAgeCategory.MinAge ASC, TournamentAgeCategory.GenderId ASC, IFNULL(MaxWeight, 100+MinWeight) ASC");
      	        $stmt->execute();
               $stmt->bind_result($ccId,$ccname);
               while ($stmt->fetch()){
                  $sel ='';
                  if ($ccId==$acid) { $sel=' selected ';}
                  echo '<option value="'.$ccId.'" '.$sel.'>'.$ccname.'</option>';
               }
	           $stmt->close();
               
               echo'
                </select>
        <input class="pgeBtn" type="submit" value="Générer">
       </form><br/>
       <span Id="progress"> </span>
        <span class="btnBar"> 
       <a class="pgeBtn" href="listingresult.php" title="Fermer" >Fermer</a>
       </span>
    </div>

    <div id="print" style="display:none;">
         <img id="logo_lt" src="css/Logo_ACG_JJJ_light.png"></img>
         <img id="gold" src="css/gold.png"></img>
         <img id="silver" src="css/silver.png"></img>
         <img id="bronze" src="css/bronze.png"></img>
    </div>
    
    </div>
    </div>
    </div>
';

if (!empty($acid)) {

  
$stmt = $mysqli->prepare("SELECT Name, TournamentStart,TournamentEnd FROM TournamentVenue order by Id desc limit 1");
$stmt->execute();
$stmt->bind_result( $trName,$TournamentStart,$TournamentEnd);
$stmt->fetch();
$stmt->close();

$date_txt = formatDate($TournamentStart);
if ($TournamentStart!=$TournamentEnd) {
    $date_txt =  $date_txt.' - '. formatDate($TournamentEnd);
}

$where_clause='';
if($acid>0 ) {	
     $where_clause=" WHERE ActualCategoryResult.ActualCategoryId=".$acid." ";
}

echo'
</body>

<script type="text/javascript" src="js/jspdf.min.js"></script>

<script type="text/javascript">


function wrapImgData(img){
    if (img.substring(0, 4) == "url("){
        img=img.substring(4,img.length-5);
        if (img.substring(0, 1) == "\""){
            img=img.substring(1,img.length-2)
        }

    }
    return img;
}

function getImgData(id) {
    var c = document.createElement("canvas");
    var img = document.getElementById(id);
    c.height = img.naturalHeight;
    c.width = img.naturalWidth;
    var ctx = c.getContext("2d");

    ctx.drawImage(img, 0, 0, c.width, c.height);
    return c.toDataURL();
}

function add_title(doc){
  var imgAddData = wrapImgData(getImgData("logo_lt"));
  
  
  doc.addImage(imgAddData, "PNG", doc.internal.pageSize.width/2-60,doc.internal.pageSize.height/2-60, 120, 120);
  
  doc.setFontSize(16).setFont("helvetica", "bold");
  doc.text(\''.$trName.'\', doc.internal.pageSize.width/2, 12, {align: \'center\'});
  doc.setFontSize(16).setFont("helvetica", "normal");
  doc.text(\''.$date_txt.'\', doc.internal.pageSize.width/2,  20, {align: \'center\'});
  doc.setFontSize(14).setFont("helvetica", "normal");
}

function makePDF(pdf_name) {

  var doc = new jsPDF({format: \'a4\',orientation:\'p\'});
  var imgGold = wrapImgData(getImgData("gold"));
  var imgSilver = wrapImgData(getImgData("silver"));
  var imgBronze = wrapImgData(getImgData("bronze"));';
  
     $stmt = $mysqli->prepare("select
                                 ActualCategory.Id,
                                 ActualCategory.Name,
                                 ActualCategoryResult.RankId,
                                 ActualCategoryResult.Medal,
                                 TournamentCompetitor.Name,
                                 TournamentCompetitor.Surname,
                                 TournamentClub.Name
                             from ActualCategoryResult
                             INNER JOIN ActualCategory ON ActualCategory.Id=ActualCategoryResult.ActualCategoryId
                             INNER JOIN TournamentCompetitor ON TournamentCompetitor.Id = ActualCategoryResult.Competitor1Id
                             INNER JOIN TournamentClub on TournamentClub.Id=TournamentCompetitor.ClubId
                             INNER JOIN TournamentCategory ON TournamentCategory.Id = ActualCategory.CategoryId
                             INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId
                             INNER JOIN TournamentWeighting ON TournamentWeighting.AgeCategoryId = TournamentAgeCategory.Id
                             ".$where_clause."
                             ORDER bY TournamentWeighting.WeightingEnd, TournamentAgeCategory.MinAge ASC, TournamentAgeCategory.GenderId ASC, IFNULL(MaxWeight, 100+MinWeight) ASC, ActualCategoryResult.RankId ASC;
                           ");
     $stmt->bind_result( $acat_id, $agcat_name,$rank,$medal,$name,$surname,$club);
     $stmt->execute();
     
     
     
    
     $current_cat ='';
     $position= 55;
     $step= 12;
     
     
     while ($stmt->fetch()){
         
         if ( $current_cat!=$agcat_name){
               if ( $current_cat!=''){
                   echo ' doc.addPage();';
               } 
              
               echo ' add_title(doc);
                      doc.setFontSize(18).setFont("helvetica", "bold");
                      doc.text(\''.$agcat_name.'\', doc.internal.pageSize.width/2, 35, {align: \'center\'});
                      doc.setFontSize(16).setFont("helvetica", "bold");'; 
              $position= 55;
              $current_cat=$agcat_name;
        }
        
        
       if ($medal==1){
          echo 'doc.addImage(imgGold, "PNG", 22, '.($position-6).', 5, 8);';
       } else if ($medal==2){
          echo 'doc.addImage(imgSilver, "PNG", 22, '.($position-6).', 5, 8);';
       } else if ($medal==3){
          echo 'doc.addImage(imgBronze, "PNG", 22, '.($position-6).', 5, 8);';
       } 
        
        echo "doc.text('".$rank." : ".$surname." ".$name." ". $club."', 30, ".$position.");"; 
       
        $position=$position + $step;
     }
     $stmt->close();
              
    $pdf_name="Résultats_Globaux.pdf";
    if ($where_clause!='') {
        $pdf_name="Résultats_".$current_cat.".pdf";
    }

  
  
 echo' 
    doc.save(pdf_name);
}

 makePDF(\''.$pdf_name.'\');

</script>';

}
echo '
</html>';

?>

