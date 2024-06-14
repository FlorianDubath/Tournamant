<?php

ob_start();
session_name("Tournament");	
session_start();

if (empty($_SESSION['_UserId'])) {
	header('Location: ./index.php');
}


include '_commonBlock.php';
writeHead();

include 'connectionFactory.php';
$mysqli= ConnectionFactory::GetConnection(); 


$cid = (int)$_GET['cid'];

echo '
<body>
    <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 
    
    
writeBand();
    echo'
    
    <div>
        <span class="h_title">
               GENERER LES RESULTATS PAR CLUBS
            </span>
      
        <form action="./resultsclub.php" method="get">
        <span class="label">Club :</span>
              <select name="cid"><option value="-1" >Tous</option> <option style="font-size: 1pt; background-color: #000000;" disabled>&nbsp;</option>';
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
                </select>
        <input class="pgeBtn" type="submit" value="Générer">
       </form>
       <span Id="progress"> </span>
        <span class="btnBar"> 
       <a class="pgeBtn" href="listingresult.php" title="Fermer" >Fermer</a>
       </span>
    </div>

    <div id="print" style="display:none;">
         <img id="logo_lt" src="css/Logo_ACG_JJJ_light.png"></img>
    </div>
    
    </div>
    </div>
    </div>
';

if (!empty($cid)) {
  
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
if($cid>0 ) {	
     $where_clause=" WHERE TournamentCompetitor.ClubId=".$cid." ";
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

  var doc = new jsPDF({format: \'a4\',orientation:\'p\'});';
  
     $stmt = $mysqli->prepare("select
                                 ActualCategory.Id,
                                 ActualCategory.Name,
                                 ActualCategoryResult.RankId,
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
                             ORDER bY TournamentClub.Name, ActualCategoryResult.RankId ASC, TournamentWeighting.WeightingEnd, TournamentAgeCategory.MinAge ASC, TournamentAgeCategory.GenderId ASC, IFNULL(MaxWeight, 100+MinWeight) ASC;
                           ");
     $stmt->bind_result( $acat_id, $agcat_name,$rank,$name,$surname,$club);
     $stmt->execute();
     
     
     
    
     $current_club ='';
     $position= 55;
     $step= 12;
     
     
     while ($stmt->fetch()){
         
         if ( $current_club!=$club){
               if ( $current_club!=''){
                   echo ' doc.addPage();';
               } 
              
               echo ' add_title(doc);
                      doc.setFontSize(18).setFont("helvetica", "bold");
                      doc.text(\'Répicatulatif pour '.$club.'\', doc.internal.pageSize.width/2, 35, {align: \'center\'});
                      doc.setFontSize(16).setFont("helvetica", "bold");'; 
              $position= 55;
              $current_club=$club;
        }
        
        echo "doc.text('".$agcat_name." - ".$rank."e : ".$surname." ".$name."', 30, ".$position.");";
       
        $position=$position + $step;
     }
     $stmt->close();
              
    $pdf_name="Résultats_Clubs.pdf";
    if ($where_clause!='') {
        $pdf_name="Résultats_Club_".$current_club.".pdf";
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

