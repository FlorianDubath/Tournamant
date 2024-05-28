<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsMainTable'] !=1 &&  $_SESSION['_IsMatTable'] !=1) {
	header('Location: ./index.php');
}

$acid=$_POST['acid'];
$cid=$_POST['cid'];
$fid=$_POST['fid'];
$pv_1=$_POST['pv1'];
$pv_2=$_POST['pv2'];


include 'connectionFactory.php';
include '_categoryHelper.php';
add_fight_result($acid, $fid, $pv_1, $pv_2);
header('Location: ./cat.php?cid='.$cid);

