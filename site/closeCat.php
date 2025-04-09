<?php

ob_start();
session_name("Tournament");	
session_start();

if ($_SESSION['_IsMainTable'] !=1 &&  $_SESSION['_IsMatTable'] !=1) {
	header('Location: ./index.php');
}

$acid=$_POST['actid'];
$cid=$_POST['cid'];

$keys_st = $_POST['kl'];
$keys = explode(",", $keys_st);
$fighter=array();
foreach($keys as $key){
   if (strlen($key)>0){
      $fid = intval($key);
      $r = intval($_POST['r_'.$key]);
      $m = intval($_POST['m_'.$key]);
      $fighter[$fid]=array("Rank"=>$r, "Medal"=>$m);
   }
}



include 'connectionFactory.php';
include '_categoryHelper.php';


close_manual_category($acid, $fighter);


header('Location: ./cat.php?cid='.$cid);

                       
                       
