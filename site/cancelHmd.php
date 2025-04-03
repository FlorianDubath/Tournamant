<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsMainTable']!=1) {
	header('Location: ./index.php');
}

include 'connectionFactory.php';
   $mysqli= ConnectionFactory::GetConnection(); 
   $stmt = $mysqli->prepare("UPDATE TournamentCompetitor SET Hansokumake=0 WHERE Id=?");
   $stmt->bind_param("i", $_POST['id']);
   $stmt->execute();
   $stmt->close();
	      
header('Location: ./card.php?sid='.$_POST['sid']);
	      
?>
