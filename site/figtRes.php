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
$ff_1=$_POST['ff1'];
$ff_2=$_POST['ff2'];
$hmd_1=array_key_exists('hmd1',$_POST)?$_POST['hmd1']:0;
$hmd_2=array_key_exists('hmd2',$_POST)?$_POST['hmd2']:0;
$noWin=$_POST['noWin'];

if ($hmd_1+$hmd_2==1){
    $pv_1=10*$hmd_2;
    $pv_2=10*$hmd_1;
}

include 'connectionFactory.php';
include '_categoryHelper.php';
add_fight_result($acid, $fid, $pv_1, $pv_2, $ff_1, $ff_2, $noWin);

if ($hmd_1+$hmd_2==1){
    add_HMD($acid, $fid, $hmd_1, $hmd_2);
}

header('Location: ./cat.php?cid='.$cid);

