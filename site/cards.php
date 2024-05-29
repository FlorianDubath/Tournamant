<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsRegistration']!=1 && $_SESSION['_IsMainTable']!=1) {
	header('Location: ./index.php');
}


include '_commonBlock.php';
writeHead();


echo '
<body>
    <div class="f_cont">
    
    <div>
       Génération des cartes: 
       <a href="" OnClick=""></a>
       <span Id="progress"> </span>
       <form action="./cards.php" method="get">
         Génération de <input type="number"  name="empty" min="1" max="16" step="1" value="1"/>  cartes vierges: <input class="pgeBtn" type="submit" value="Générer">
             <a class="pgeBtn" href="listingreg.php" title="Fermer" >Fermer</a>
       </form>
    </div>

    <div id="print" style="display:none;">
         <div id="qrcode"></div>
         <img id="logo" src="css/Logo_ACG_JJJ_light.png"></img>
    </div>
    
    </div>
';

 include 'connectionFactory.php';

$mysqli= ConnectionFactory::GetConnection(); 
$person_dict = array();
if (empty($_GET['empty'])){

    $stmt = $mysqli->prepare("SELECT 
                                      TournamentCompetitor.Id, 
                                      StrId, 
                                      Surname, 
                                      TournamentCompetitor.Name, 
                                      Birth, 
                                      TournamentGender.Name, 
                                      TournamentClub.Name, 
                                      TournamentGrade.Name,
                                      LicenceNumber,
                                      TournamentCompetitor.CheckedIn
                               FROM TournamentCompetitor 
                               INNER JOIN TournamentGender ON TournamentCompetitor.GenderId=TournamentGender.Id
                               INNER JOIN TournamentGrade ON GradeId=TournamentGrade.Id
                               INNER JOIN TournamentClub ON ClubId=TournamentClub.Id
                               order by TournamentClub.Name, Surname, TournamentCompetitor.Name
                         
 ");
 
$stmt->bind_result( $Id, $strId,$Surname,$Name,$Birth, $Gender, $Club, $Grade, $licence,$chin);
$stmt->execute();

while ( $stmt->fetch()) {

    $records =  array("strId"=>$strId, "surname"=>$Surname, "name"=>$Name, "club"=>$Club, "cats"=>array());
    $person_dict[$Id] = $records;
}
$stmt->close();



	              
$stmt = $mysqli->prepare("SELECT 
                                TournamentCompetitor.Id, 
                                TournamentAgeCategory.Name,
                                TournamentAgeCategory.ShortName,
                                IFNULL(-MaxWeight, IFNULL(MinWeight,'OPEN')),
                                TournamentGender.Name,
                                TournamentRegistration.Payed,
                                TournamentCompetitor.CheckedIn,
                                TournamentRegistration.WeightChecked,
                                TournamentWeighting.WeightingBegin,
                                TournamentWeighting.WeightingEnd
                         FROM TournamentRegistration 
                         INNER JOIN TournamentCategory ON TournamentRegistration.CategoryId=TournamentCategory.Id
                         INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId
                         INNER JOIN TournamentWeighting ON TournamentWeighting.AgeCategoryId = TournamentAgeCategory.Id 
                         INNER JOIN TournamentGender ON TournamentGender.Id=TournamentAgeCategory.GenderId
                         INNER JOIN TournamentCompetitor ON TournamentCompetitor.Id=TournamentRegistration.CompetitorId
                         ORDER BY TournamentWeighting.WeightingEnd");
                    
$stmt->execute();
$stmt->bind_result($trid,$trname,$trshort,$wgt,$gender,$payed,$checkedin,$weight_checked, $weighting_begin, $weighting_end);

while ($stmt->fetch()){
    $w_end = new DateTime($weighting_end);
    $rec = array("name"=>$trshort.' '.$trname.' '.$gender.' '.$wgt,"end_wgt"=>$w_end, "date"=>$w_end->format('j/m H\hi'));
    if (array_key_exists($trid,$person_dict)) {
        array_push($person_dict[$trid]["cats"],  $rec);
    }
}
$stmt->close();    
} else {
    for ($index=0; $index<(int)$_GET['empty'];$index++) {
         $strId = substr(md5(date('Y-m-d:h:m:s').rand()),0,12);
         $records =  array("strId"=>$strId, "surname"=>"", "name"=>"", "club"=>"", "cats"=>array());
         $person_dict[-($index+1)] = $records;
    }
}

  
$stmt = $mysqli->prepare("SELECT Name, TournamentStart,TournamentEnd FROM TournamentVenue order by Id desc limit 1");
$stmt->execute();
$stmt->bind_result( $trName,$TournamentStart,$TournamentEnd);
$stmt->fetch();
$stmt->close();

$date_txt = formatDate($TournamentStart);
if ($TournamentStart!=$TournamentEnd) {
    $date_txt =  $date_txt.' - '. formatDate($TournamentEnd);
}

echo'
</body>

<script type="text/javascript" src="js/qrcode.js"></script>
<script type="text/javascript" src="js/jspdf.min.js"></script>

<script type="text/javascript">
var qrcode = new QRCode(document.getElementById("qrcode"), {
	width : 250,
	height : 250
});

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

function addCard(doc,h_pos,v_pos,name,club,categories,tournament_name,tournament_date){
  var imgAddData = wrapImgData(getImgData("logo"));
  doc.addImage(imgAddData, "PNG", h_pos*doc.internal.pageSize.width/2+40, v_pos*doc.internal.pageSize.height/2+15, 70, 70);
   
  var imgAddData = wrapImgData(document.getElementById("qrcode").getElementsByTagName("img")[0].src);
  doc.addImage(imgAddData, "PNG", h_pos*doc.internal.pageSize.width/2+100, v_pos*doc.internal.pageSize.height/2+25, 40, 40);


  doc.setFontSize(16).setFont("helvetica", "bold");
  doc.text(tournament_name, h_pos*doc.internal.pageSize.width/2+doc.internal.pageSize.width/4, v_pos*doc.internal.pageSize.height/2+12, {align: \'center\'});
  doc.setFontSize(16).setFont("helvetica", "normal");
  doc.text(tournament_date, h_pos*doc.internal.pageSize.width/2+doc.internal.pageSize.width/4,  v_pos*doc.internal.pageSize.height/2+20, {align: \'center\'});
  doc.setFontSize(14).setFont("helvetica", "normal");
  
  doc.text("Compétiteur :",h_pos*doc.internal.pageSize.width/2+10 , v_pos*doc.internal.pageSize.height/2+30) ; 
  doc.setFont("helvetica", "bold");
  doc.text(name,h_pos*doc.internal.pageSize.width/2+15 , v_pos*doc.internal.pageSize.height/2+40) ; 
  doc.setFont("helvetica", "normal");
  doc.text("Club :",h_pos*doc.internal.pageSize.width/2+10 , v_pos*doc.internal.pageSize.height/2+50) ;
  doc.setFontSize(11).setFont("helvetica", "bold");
  doc.text(club,h_pos*doc.internal.pageSize.width/2+15 , v_pos*doc.internal.pageSize.height/2+60) ;
  doc.setFontSize(14).setFont("helvetica", "normal");
  doc.text("Catégorie(s):",h_pos*doc.internal.pageSize.width/2+10 , v_pos*doc.internal.pageSize.height/2+70);
  doc.setFont("helvetica", "bold");
  
  var position = v_pos*doc.internal.pageSize.height/2 +80;
  var step = 10;
  for (let i = 0; i < categories.length; i++) {
      doc.text(categories[i].name,h_pos*doc.internal.pageSize.width/2+15, position);
      doc.text("Pesée > "+categories[i].date ,h_pos*doc.internal.pageSize.width/2+94 ,position);
      position=position+step;
  }
  
}



function get_h_pos(counter){
    var reduced = counter % 4;
    return reduced % 2;
}

function get_v_pos(counter){
    var reduced = counter % 4;
    return reduced > 1;
}

var intervall_id = 0;
const trn_name ="'.$trName.'";
const trn_date ="'.$date_txt.'";
const data = '.json_encode($person_dict).';
var current_index = 0;

function addPdfCard(doc, pdf_name){
      document.getElementById("progress").innerHTML=(current_index+1)+"/"+Object.keys(data).length;
      qrcode.clear();
      var curr_rec = data[Object.keys(data)[current_index]];
      qrcode.makeCode("http://'.$_SERVER['HTTP_HOST'].'/card.php&sid=" + curr_rec.strId);
      
      intervall_id = setInterval(function () {
        if (document.getElementById("qrcode").getElementsByTagName("img")[0].src!=""){
            clearInterval(intervall_id);
            const rec = data[Object.keys(data)[current_index]];
            addCard(doc, get_h_pos(current_index), get_v_pos(current_index), rec.surname + \' \' + rec.name, rec.club, rec.cats, trn_name, trn_date);
            if (current_index< Object.keys(data).length -1){
                 
                  if ((current_index+1)%4==0){
                      doc.addPage();
                  }
                  current_index+=1;
                  addPdfCard(doc, pdf_name);
              } else {
                  doc.save(pdf_name);
              }
     }}, 10);
}

function makePDF(pdf_name) {

  var doc = new jsPDF({format: \'a4\',orientation:\'l\'});
  addPdfCard(doc, pdf_name);

}

 makePDF(\'test.pdf\');

</script>
</html>';

?>

