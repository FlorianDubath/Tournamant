<?php

ob_start();
session_name("Tournament");	
session_start();

if (empty($_SESSION['_UserId'])) {
	header('Location: ./index.php');
}


include '_commonBlock.php';
writeHead();


echo '
<body>
    <div class="f_cont">
    
    <div>
       Génération des résultats: 
       <a href="" OnClick=""></a>
       <span Id="progress"> </span>
       <a class="pgeBtn" href="listingresult.php" title="Fermer" >Fermer</a>
    </div>

    <div id="print" style="display:none;">
         <img id="logo" src="css/Logo_ACG_JJJ_light.png"></img>
    </div>
    
    </div>
';

 include 'connectionFactory.php';

$mysqli= ConnectionFactory::GetConnection(); 

  
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
if(!empty($_GET['acid']) ) {	
     $where_clause=" WHERE ActualCategoryResult.ActualCategoryId=".(int)$_GET['acid']." ";
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
  var imgAddData = wrapImgData(getImgData("logo"));
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
                             ORDER bY TournamentWeighting.WeightingEnd, TournamentAgeCategory.MinAge ASC, TournamentAgeCategory.GenderId ASC, IFNULL(MaxWeight, 100+MinWeight) ASC, ActualCategoryResult.RankId ASC;
                           ");
     $stmt->bind_result( $acat_id, $agcat_name,$rank,$name,$surname,$club);
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
        
        echo "doc.text('".$rank." : ".$surname." ".$name." ". $club."', 30, ".$position.");";
       
        $position=$position + $step;
     }
     $stmt->close();
              
  
  
  
 echo' 
    doc.save(pdf_name);
}

 makePDF(\'Results.pdf\');

</script>
</html>';

?>

