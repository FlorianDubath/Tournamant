<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsMainTable']!=1 && $_SESSION['_IsAdmin']!=1) {
	header('Location: ./index.php');
}

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
           GENERER LA LISTE DES POINTS VALEURS
       </span>
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
$stmt = $mysqli->prepare("SELECT Name, TournamentStart,TournamentEnd FROM TournamentVenue order by Id desc limit 1");
$stmt->bind_result( $trName,$TournamentStart,$TournamentEnd);
$stmt->execute();
$stmt->fetch();
$stmt->close();

$date_txt = formatDate($TournamentStart);
if ($TournamentStart!=$TournamentEnd) {
    $date_txt =  $date_txt.' - '. formatDate($TournamentEnd);
}

$stmt = $mysqli->prepare("SELECT
			    TournamentCompetitor.Id,
			    TournamentCompetitor.Name,
			    TournamentCompetitor.Surname,
			    TournamentCompetitor.LicenceNumber,
			    TournamentClub.Name
			  FROM TournamentCompetitor
			  INNER JOIN TournamentClub on TournamentClub.Id = TournamentCompetitor.ClubId
			  INNER JOIN TournamentGrade on TournamentGrade.Id = TournamentCompetitor.GradeId
			  WHERE TournamentGrade.CollectVP=1 
			  ORDER BY TournamentCompetitor.Surname, TournamentCompetitor.Name");
$stmt->execute();
$stmt->bind_result( $cid,$name,$surname,$licenceNumber,$club);

$competitors = array();
$counter = 0;
while ($stmt->fetch()){
    $competitors[$counter] = array(
				    "Id"  => $cid,
				    "Name" => $surname." ".$name,
				    "License" => $licenceNumber,
				    "Club" => $club,
				    "FightNumber" => 0,
				    "PV" => 0,
				    "IpponNumber" => 0
				);
    $counter += 1;
}
$stmt->close();



$stmt = $mysqli->prepare(" SELECT 
                                 G1.CollectVP + G2.CollectVP,
                                 TC1.Id,
                                 pv1,
                                 TC2.Id,
                                 pv2
                         FROM Fight
                         INNER JOIN TournamentCompetitor TC1 ON TournamentCompetitor1Id = TC1.Id
                         INNER JOIN TournamentCompetitor TC2 ON TournamentCompetitor2Id = TC2.Id
                         INNER JOIN TournamentGrade G1 ON TC1.GradeId = G1.Id
                         INNER JOIN TournamentGrade G2 ON TC2.GradeId = G2.Id
                         WHERE pv1 IS NOT NULL AND forfeit1<>1 AND forfeit2<>1 
                         ORDER BY TC1.Id, TC2.Id 
                         ");   
$stmt->bind_result($collect,$tc1, $pv1, $tc2, $pv2);
$stmt->execute();

while ($stmt->fetch()){
    foreach($competitors as $counter => $competitor) {
        if ($competitor["Id"]==$tc1){
            $competitors[$counter]["FightNumber"]+=1;
            if ($collect==2 && $pv1 > $pv2){
               $competitors[$counter]["PV"]+=$pv1;
               if ($pv1 == 10){
                   $competitors[$counter]["IpponNumber"]+=1;
               }
            }
        } else if ($competitor["Id"]==$tc2){
            $competitors[$counter]["FightNumber"]+=1;
            if ($collect==2 && $pv1 < $pv2){
               $competitors[$counter]["PV"]+=$pv2;
               if ($pv2 == 10){
                   $competitors[$counter]["IpponNumber"]+=1;
               }
            }
        }
    }
}
$stmt->close();



$pdf_name="Liste des Points Valeurs.pdf";

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
  doc.text(\''.$trName.'\', doc.internal.pageSize.width/2, 20, {align: \'center\'});
  doc.setFontSize(16).setFont("helvetica", "normal");
  doc.text(\''.$date_txt.'\', doc.internal.pageSize.width/2,  28, {align: \'center\'});
  doc.setFontSize(14).setFont("helvetica", "normal");
}

function makePDF(pdf_name) {

  var doc = new jsPDF({format: \'a4\',orientation:\'p\'});';
 
  $step= 12;
  $position= 42;
             echo " add_title(doc);
                    doc.setFontSize(12).setFont('helvetica', 'bold');
                      
                    doc.text('N°', 14, ".$position.");
		    doc.text('Nom Prénom', 25, ".$position.");
	            doc.text('Licence', 68, ".$position.");
		    doc.text('Club', 90, ".$position.");
		    doc.text('Combats', 142, ".$position.");
		    doc.text('Points', 166, ".$position.");
		    doc.text('Ippons', 185, ".$position.");
		    
             doc.setFontSize(12).setFont('helvetica', 'normal');
                    "; 
             $position= 55;
             $pos=0;
  foreach($competitors as $counter => $competitor) {
        if ($position>280){
             echo " doc.addPage(); ";
             $position= 42;
             echo " add_title(doc);
                    doc.setFontSize(12).setFont('helvetica', 'bold');
                      
                    doc.text('N°', 14, ".$position.");
		    doc.text('Nom Prénom', 25, ".$position.");
	            doc.text('Licence', 68, ".$position.");
		    doc.text('Club', 90, ".$position.");
		    doc.text('Combats', 142, ".$position.");
		    doc.text('Points', 166, ".$position.");
		    doc.text('Ippons', 185, ".$position.");
		    
             doc.setFontSize(12).setFont('helvetica', 'normal');
                    "; 
             $position= 55;
        }
        
        if ($competitor["FightNumber"]>0){
            echo "  doc.text('".($pos+1)."', 17, ".$position.", {align: 'right',});
		    doc.text('".$competitor["Name"]."', 25, ".$position.");
		    doc.text('".$competitor["License"]."', 84, ".$position.", {align: 'right',});
		    doc.text('".$competitor["Club"]."', 90, ".$position.");
		    doc.text('".$competitor["FightNumber"]."', 160, ".$position.", {align: 'right',});
		    doc.text('".$competitor["PV"]."', 177, ".$position.", {align: 'right',});
		    doc.text('".$competitor["IpponNumber"]."', 193, ".$position.", {align: 'right',});"; 
            $position=$position + $step;  
            $pos+=1;
        }
  }
     
 echo' 
    doc.save(pdf_name);
}

 makePDF(\''.$pdf_name.'\');

</script>';


echo '
</html>';

?>

