<?php

include '_commonBlock.php';
writeHead();

echo'
<body>
    <div class="f_cont">';
echo'        
       <div class="cont_l">
         <div class="h">'; 
echo ' 
            <span class="h_title">
               CONNECTION
               </span>
               
               <span class="h_txt">
               <form action="./identification.php" method="post">
	             <span class="fitem">
	               <span class="label">Nom d\'utilisateur:</span>
	               <input class="inputText"  type="text" name="login" value="" /><br/>
	             </span>
	             <span class="fitem">
	               <span class="label">Mot de passe:</span>
                   <input class="inputText"  type="password" name="mdp" value="" /><br/>
                 </span>
	            ';
	             
	             if ($_GET["CR"]) {
	                echo ' <span class="fitem"><span class="message">Vérifiez vos indentifiants</span><br/>' ;
	             }
	             
	             echo' 
	             <span class="btnBar"> 
	               <input class="pgeBtn" type="submit" value="Se connecter">
	             </span>
                </form>
               </span>
           </div>     
        </div>   
     </div>
</body>
</html>';

?>

