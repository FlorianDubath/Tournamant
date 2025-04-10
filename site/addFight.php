<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsMainTable'] !=1 &&  $_SESSION['_IsMatTable'] !=1) {
	header('Location: ./index.php');
}

$acid=$_REQUEST['actid'];
$cid=$_REQUEST['cid'];
$c1=$_POST['c1'];
$c2=$_POST['c2'];
$fid = $_GET['fid'];


include 'connectionFactory.php';
include '_categoryHelper.php';

if ($c1>0){
    add_fight_to_manual_Category($acid,$c1,$c2);
}
if ($fid>0){
   delete_fight_from_manual_Category($acid,$fid);
}

header('Location: ./cat.php?cid='.$cid);

                       
                       
