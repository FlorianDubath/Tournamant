<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsAdmin']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';

include '_commonBlock.php';
$mysqli= ConnectionFactory::GetConnection(); 

writeHead();

	$isReadonly=0;
	$readonly='';
	$button='Cr&eacute;er';
	if ($_GET['uid']>0 ){
	  $isReadonly=1;
	  $readonly=' readonly="readonly" ';
	  $button='Modifier';
	}
	
	$stmt = $mysqli->prepare("SELECT Id,EMail,DisplayName,LastLoggedIn,Salt,IsAdmin, IsRegistration, IsWelcome, IsWeighting, IsMainTable, IsMatTable FROM TournamentSiteUser WHERE Id=? ");
	$stmt->bind_param("i",$_GET['uid'] );
    $stmt->bind_result( $Id, $EMail,$disp, $last,$s, $IsAdmin, $IsRegistration, $IsWelcome, $IsWeighting, $IsMainTable, $IsMatTable);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    


echo'
<body>
    <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 
         
writeBand();
echo ' 
            <span class="h_title">UTILISATEUR</span>
           
            <span class="h_txt">
            <form action="./addUser.php" method="post"> 
            <input type="hidden" name="uid" value="'.$Id.'"/>
                <span class="fitem">
                    <span class="label">Identifiant* :</span>
                    <input type="text" name="mail" value="'.$EMail.'" '.$readonly.'/><br/>
                </span>
                <span class="fitem">
                    <span class="label">Nom publique* :</span>
                    <input type="text" name="disp" value="'.$disp.'" /><br/>
                </span>';
if ($isReadonly==0){
   echo' 
                <span class="fitem">
                     <span class="label">Mot de passe* :</span>
                     <input type="text" name="psw" /><br/>
                </span>';
}
echo'  
               <span class="fitem">
                    <span class="label">Droits Admin :</span>
                    <input type="checkbox" name="da" value="1" '; if ($IsAdmin==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
               <span class="fitem">
                    <span class="label">Droits Inscription :</span>
                    <input type="checkbox" name="dr" value="1" '; if ($IsRegistration==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
               <span class="fitem">
                    <span class="label">Droits Acceuil :</span>
                    <input type="checkbox" name="dwc" value="1" '; if ($IsWelcome==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
                <span class="fitem">
                    <span class="label">Droits Pesée :</span>
                    <input type="checkbox" name="dwg" value="1" '; if ($IsWeighting==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
               <span class="fitem">
                    <span class="label">Droits Table Centrale :</span>
                    <input type="checkbox" name="dmt" value="1" '; if ($IsMainTable==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
               <span class="fitem">
                    <span class="label">Droits Table Tatami :</span>
                    <input type="checkbox" name="dtt" value="1" '; if ($IsMatTable==1) {echo 'checked="cheched"';} echo'/><br/>
               </span>
             
               <span class="btnBar "> 
                    <input class="pgeBtn" type="submit" value="'.$button.'" title="'.$button.'" /> 
               </span>
               
           </form>
            </span>
           <span class="h_txt"> 
                <span class="btnBar"> 
                   <a class="pgeBtn" href="admin.php" title="Fermer" >Fermer</a>
               </span>
           </span>    
           </div>     
        </div>   
     </div>
</body>
</html>';
?>  
