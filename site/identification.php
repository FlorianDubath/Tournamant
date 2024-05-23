<?php
      ob_start();
      session_name("Tournament");	
      session_start(); 	
      
      $rtn_url='index.php';
      $rtn_url_err='?CR=1';
      if (!empty($_POST['rtn']) && $_POST['rtn']!="" && $_POST['rtn']!="/") {
          $rtn_url=$_POST['rtn'];
          str_replace($rtn_url,'CR=1&','');
          str_replace($rtn_url,'&CR=1','');
          str_replace($rtn_url,'?CR=1','');
      }
      
      if (str_contains($rtn_url,'?')) {
          $rtn_url_err='&CR=1';
      }
      			
      if($_POST && !empty($_POST['login']) && !empty($_POST['mdp'])) {
	    
         include 'connectionFactory.php';
         $mysqli= ConnectionFactory::GetConnection(); 
         

	      if (!($stmt = $mysqli->prepare("SELECT Id,EMail,Salt,Password,DisplayName, IsAdmin, IsRegistration,IsWelcome, IsWeighting,IsMainTable,IsMatTable FROM TournamentSiteUser WHERE EMail =?"))){
      	     echo '<span class="error">Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
      	  }

  	      $stmt->bind_param("s", $_POST['login'] );
          if (!($stmt->execute())){
             echo '<span class="error">Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error.'</span>';
          }

          $stmt->bind_result( $UserId,$email, $Salt, $Password, $DisplayName, $IsAdmin, $IsRegistration, $IsWelcome, $IsWeighting, $IsMainTable, $IsMatTable);
          $stmt->fetch();
	      $stmt->close();

          if (!isset($UserId)){
	          header('Location: '.$rtn_url.$rtn_url_err);
             exit();	
          }
          $password_md5 = md5($_POST['mdp'].$Salt);
         //  echo $password_md5; exit();
	      if(similar_text($password_md5, $Password, $percent)==32){
	        $_SESSION['_UserId'] = $UserId;
	        $_SESSION['_DisplayName'] = $DisplayName;
	        $_SESSION['_IsAdmin'] = $IsAdmin;
	        $_SESSION['_IsRegistration'] = $IsRegistration;
	        $_SESSION['_IsWelcome'] = $IsWelcome;
	        $_SESSION['_IsWeighting'] = $IsWeighting;
	        $_SESSION['_IsMainTable'] = $IsMainTable;
	        $_SESSION['_IsMatTable'] = $IsMatTable;
              
	        $stmt = $mysqli->prepare("Update  TournamentSiteUser set LastLoggedIn=now() WHERE Id =?");
            $stmt->bind_param("i", $UserId );
	        $stmt->execute();
     	    $stmt->close();

	       	header('Location: '.$rtn_url);
	       
	     } else {
	       header('Location: '.$rtn_url.$rtn_url_err);	 
	     }
     } else {
        // LogOut
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
	   $params = session_get_cookie_params();
	   setcookie(session_name(), '', time() - 42000,$params["path"], $params["domain"],$params["secure"], $params["httponly"]);
	}
	session_destroy();
        header('Location: '.$rtn_url);
     }
     exit();	
?>




	
