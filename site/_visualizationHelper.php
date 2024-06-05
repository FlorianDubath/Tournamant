<?php

function getstepList($actual_cat_Id){
    $mysqli= ConnectionFactory::GetConnection(); 
    $stmt = $mysqli->prepare("SELECT  CS1.Id, 
                                      CS1.Name, 
                                      CS1.CategoryStepsTypeId,
                                      in_step_1_id,
                                      rank_in_step_1 ,
                                      Idin_step_2_id,
                                      rank_in_step_2 
                               FROM CategoryStep CS1 
                               LEFT OUTER JOIN StepLinking ON CS1.Id=StepLinking.out_step_id
                               WHERE CS1.ActualCategoryId=? ORDER BY CS1.Id DESC");
     $stmt->bind_param("i", $actual_cat_Id );
     $stmt->bind_result( $step_id, $step_name, $step_type, $parent_id_1,$parent_rank_1, $parent_id_2,$parent_rank_2);
     $stmt->execute();
     $res = array();
     while ($stmt->fetch()){
         $res[$step_id] = array("name"=>$step_name,"type"=>$step_type, "parent_1"=>$parent_id_1, "parent_rank_1"=>$parent_rank_1, "parent_2"=>$parent_id_2, "parent_rank_2"=>$parent_rank_2 );
     }
     $stmt->close();
     return $res;
}

function plot_pool($step_id, $catName, $stepName){ 
    $mysqli= ConnectionFactory::GetConnection(); 
    $stmt = $mysqli->prepare("SELECT  TC1.Id, 
                                      TC1.Name, 
                                      TC1.Surname,
                                      pv1,
                                      TC2.Id, 
                                      TC2.Name, 
                                      TC2.Surname,
                                      pv2
                               FROM Fight 
                               INNER JOIN TournamentCompetitor TC1 on TC1.Id = TournamentCompetitor1Id
                               INNER JOIN TournamentCompetitor TC2 on TC2.Id = TournamentCompetitor2Id
                               WHERE Fight.step_id=?");
     $stmt->bind_param("i", $step_id );
     $stmt->bind_result( $tc1_id, $tc1_name, $tc1_surname,$pv1, $tc2_id, $tc2_name, $tc2_surname,$pv2);
     $stmt->execute();
     $fgt = array();
     $comp = array();
     while ($stmt->fetch()){
         $comp[$tc1_id] = array("name"=>$tc1_name,"surname"=>$tc1_surname );
         $comp[$tc2_id] = array("name"=>$tc2_name,"surname"=>$tc2_surname );
         if (!empty($pv1) || !empty($pv2)){
             $fgt[$tc1_id.'-'.$tc2_id] =  array("pv1"=>$pv1,"pv2"=>$pv2 );
         }
     }
     $stmt->close(); 
    // background and grid
     
    echo '
      <svg height="'.(202+100*count($comp)).'" width="'.(300+100*count($comp)).'">';
    for ($counter=0; $counter<count($comp); $counter+=1) {
        echo ' 
            <rect width="200" height="100" x="0" y="'.(200+100*$counter).'" style="fill:rgb(230,230,230);"/>
            <rect width="200" height="100" x="0" y="'.(200+100*$counter).'" transform=" translate(200 0)rotate(-90 0,200)"  style="fill:rgb(230,230,230);"/>
            <rect width="100" height="100" x="'.(200+100*$counter).'" y="'.(200+100*$counter).'"  fill="gray" />
            ';
    }
    
    echo '<line x1="0" y1="200" x2="'.(200+100*count($comp)).'" y2="200" style="stroke:black;stroke-width:2" />
          <line x1="200" y1="0" x2="200" y2="'.(200+100*count($comp)).'" style="stroke:black;stroke-width:2" />
          <line x1="0" y1="'.(200+100*count($comp)).'" x2="'.(200+100*count($comp)).'" y2="'.(200+100*count($comp)).'" style="stroke:black;stroke-width:2" />
          <line x1="'.(200+100*count($comp)).'" y1="0" x2="'.(200+100*count($comp)).'" y2="'.(200+100*count($comp)).'" style="stroke:black;stroke-width:2" />
    ';
     for ($counter=0; $counter<count($comp); $counter+=1) {
        echo ' 
              <line x1="0" y1="'.(200+100*$counter).'" x2="'.(200+100*count($comp)).'" y2="'.(200+100*$counter).'" style="stroke:black;stroke-width:1" />
              <line x1="'.(200+100*$counter).'" y1="0" x2="'.(200+100*$counter).'" y2="'.(200+100*count($comp)).'" style="stroke:black;stroke-width:1" />
        ';
         for ($counter_2=$counter+1; $counter_2<count($comp); $counter_2+=1) {
              echo '<line x1="'.(200+100*$counter_2).'" y1="'.(200+100*$counter).'" x2="'.(300+100*$counter_2).'" y2="'.(300+100*$counter).'" style="stroke:black;stroke-width:1" /> 
                    <line x1="'.(200+100*$counter).'" y1="'.(200+100*$counter_2).'" x2="'.(300+100*$counter).'" y2="'.(300+100*$counter_2).'" style="stroke:black;stroke-width:1" />';
         }
    }
     for ($counter_2=$counter+1; $counter_2<count($comp); $counter_2+=1) {
        echo ' 
              <line x1="0" y1="'.(200+100*$counter).'" x2="'.(200+100*count($comp)).'" y2="'.(200+100*$counter).'" style="stroke:black;stroke-width:1" />
              <line x1="'.(200+100*$counter).'" y1="0" x2="'.(200+100*$counter).'" y2="'.(200+100*count($comp)).'" style="stroke:black;stroke-width:1" />
        ';
    }
   
    // Title   
    echo '       
        <text x="100" y="100" fill="black" font-size="20" transform="rotate(-45 100,100)" text-anchor="middle">'.$catName.'</text>
        <text x="120" y="120" fill="black" font-size="25" transform="rotate(-45 120,120)" text-anchor="middle">'.$stepName.'</text>';
       
    // Competitors  
    $counter=0;
    $total_v=array();
    $total_pv=array();
    foreach($comp as $cid=>$comp_obj) {
        echo '
            <text x="100" y="'.(240+100*$counter).'" fill="black" font-size="20" text-anchor="middle">'.$comp_obj["surname"].'</text>
            <text x="100" y="'.(270+100*$counter).'" fill="black" font-size="20" text-anchor="middle">'.$comp_obj["name"].'</text>
            <text x="100" y="'.(240+100*$counter).'" fill="black" font-size="20" text-anchor="middle" transform=" translate(200 0)rotate(-90 0,200)">'.$comp_obj["surname"].'</text>
            <text x="100" y="'.(270+100*$counter).'" fill="black" font-size="20" text-anchor="middle" transform=" translate(200 0)rotate(-90 0,200)">'.$comp_obj["name"].'</text>
        ';
         $comp[$cid]['index']= $counter;
         $total_v[$cid]=0;
         $total_pv[$cid]=0;
         
         $counter+=1;   
    } 
    
  
    // Results   
    foreach ($fgt  as $fid=>$fgt_object){
        $cids = explode('-',$fid);
        $idx_1 = $comp[(int)$cids[0]]['index'];
        $idx_2 = $comp[(int)$cids[1]]['index'];
        
        $r1='';
        $r2='';
        if ($fgt_object['pv1']>0) {
            $r1='2('.$fgt_object['pv1'].')';
            $total_v[(int)$cids[0]]+=2;
            $total_pv[(int)$cids[0]]+=$fgt_object['pv1'];
            $r2='0';
        } else {
            $r1='0';
            $r2='2('.$fgt_object['pv2'].')';
            $total_v[(int)$cids[1]]+=2;
            $total_pv[(int)$cids[1]]+=$fgt_object['pv2'];
        }
        
        echo '
            <text x="'.(238+100*$idx_2).'" y="'.(280+100*$idx_1).'" fill="black" font-size="25" text-anchor="middle">'.$r1.'</text>
            <text x="'.(262+100*$idx_2).'" y="'.(230+100*$idx_1).'" fill="black" font-size="25" text-anchor="middle">'.$r2.'</text>
            <text x="'.(238+100*$idx_1).'" y="'.(280+100*$idx_2).'" fill="black" font-size="25" text-anchor="middle">'.$r2.'</text>
            <text x="'.(262+100*$idx_1).'" y="'.(230+100*$idx_2).'" fill="black" font-size="25" text-anchor="middle">'.$r1.'</text>
        ';
    }
    
   
    // Totals  
    $position_x = count($total_pv) *100 + 210;
    foreach($total_v as $cid=>$victory){
        $pv_tot =  $total_pv[$cid];
        $index = $comp[$cid]['index'];
        echo ' <text x="'.$position_x.'" y="'.(250+100*$index).'" fill="black" font-size="25">'.$victory.' ('.$pv_tot.')</text>';
    }
        echo'
    </svg>';
}


function plot_table($top_step_id, $catName){
    $mysqli= ConnectionFactory::GetConnection(); 
    $stmt = $mysqli->prepare("SELECT  
                                      
                                      TCH11.name,
                                      TCH11.Surname,
                                      h1.pv1,  
                                      CSH11.Name,
                                      h1l.rank_in_step_1,
                                       
                                      TCH12.name,
                                      TCH12.Surname,
                                      h1.pv2,
                                      CSH12.Name,
                                      h1l.rank_in_step_2,
                                      
                                      TCH21.name,
                                      TCH21.Surname,
                                      h2.pv1,  
                                      CSH21.Name,
                                      h2l.rank_in_step_1,
                                       
                                      TCH22.name,
                                      TCH22.Surname,
                                      h2.pv2,
                                      CSH22.Name,
                                      h2l.rank_in_step_2,
                                      
                                      TC1.name,
                                      TC1.Surname,
                                      f.pv1,  
                                      CSF1.Name,
                                      fl.rank_in_step_1,
                                      CSF1.CategoryStepsTypeId,
                                      
                                      TC2.name,
                                      TC.Surname,
                                      f.pv2,
                                      CSF2.Name,
                                      fl.rank_in_step_2,
                                      CSF2.CategoryStepsTypeId
                                       
                                
                               FROM Fight as f 
                               LEFT OUTER JOIN TournamentCompetitor TC1 on TC1.Id = f.TournamentCompetitor1Id
                               LEFT OUTER JOIN TournamentCompetitor TC2 on TC2.Id = f.TournamentCompetitor2Id
                               LEFT OUTER JOIN StepLinking fl ON fl.out_step_id = f.step_id
                               LEFT OUTER JOIN CategoryStep CSF1 on CSF1.Id = f.in_step_1_id 
                               LEFT OUTER JOIN CategoryStep CSF2 on CSF1.Id = f.in_step_2_id
                               
                               LEFT OUTER JOIN Fight as h1 ON h1.step_id = CSF1.Id
                               LEFT OUTER JOIN TournamentCompetitor TCH11 on TCH11.Id = h1.TournamentCompetitor1Id
                               LEFT OUTER JOIN TournamentCompetitor TCH12 on TCH12.Id = h1.TournamentCompetitor2Id
                               LEFT OUTER JOIN StepLinking h1l ON h1l.out_step_id = h1.step_id
                               LEFT OUTER JOIN CategoryStep CSH11 on CSH11.Id = h1l.in_step_1_id
                               LEFT OUTER JOIN CategoryStep CSH12 on CSH12.Id = h1l.in_step_2_id
                               
                               LEFT OUTER JOIN Fight as h2 ON h2.step_id = CSF2.Id
                               LEFT OUTER JOIN TournamentCompetitor TCH21 on TCH21.Id = h2.TournamentCompetitor1Id
                               LEFT OUTER JOIN TournamentCompetitor TCH22 on TCH22.Id = h2.TournamentCompetitor2Id
                               LEFT OUTER JOIN StepLinking h2l ON h2l.out_step_id = h2.step_id
                               LEFT OUTER JOIN CategoryStep CSH21 on CSH21.Id = h2l.in_step_1_id
                               LEFT OUTER JOIN CategoryStep CSH22 on CSH22.Id = h2l.in_step_2_id
                               WHERE f.step_id=? ");
     $stmt->bind_param("i", $top_step_id );
     $stmt->bind_result($h1_1_name, $h1_1_surname, $h1_1_pv, $h1_1_grp, $h1_1_rank,$h1_2_name, $h1_2_surname, $h1_2_pv, $h1_2_grp, $h1_2_rank,
                        $h2_1_name, $h2_1_surname, $h2_1_pv, $h2_1_grp, $h2_1_rank,$h2_2_name, $h2_2_surname, $h2_2_pv, $h2_2_grp, $h2_2_rank,
                        $f_1_name,$f_1_surname,$f_1_pv, $f_1_grp, $f_1_rank,$h1_type, $f_2_name,$f_2_surname,$f_2_pv, $f_2_grp, $f_2_rank, $h2_type);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close(); 

     // 2 or 3 fights 
     $multiple = $h2_type==1 || $h1_type==1;


    // background and grid
    if ($multiple) {
         echo '<svg height="400" width="900">';
         if ($h1_type==1) {
            echo '      
            <rect width="200" height="80" x="200" y="10"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
            <rect width="200" height="80" x="200" y="110"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
            <line x1="400" y1="50" x2="422" y2="50" style="stroke:black;stroke-width:4" />
            <line x1="400" y1="150" x2="422" y2="150" style="stroke:black;stroke-width:4" />
            <line x1="420" y1="50" x2="420" y2="150" style="stroke:black;stroke-width:4" />
            <line x1="420" y1="100" x2="440" y2="100" style="stroke:black;stroke-width:4" />
            
            <text x="100" y="40" fill="black" font-size="20" text-anchor="middle">'.$h1_1_grp.'</text>
	        <text x="100" y="60" fill="black" font-size="20" text-anchor="middle">'.$h1_1_rank.'</text>
            <text x="100" y="140" fill="black" font-size="20" text-anchor="middle">'.$h1_2_grp.'</text>
            <text x="100" y="160" fill="black" font-size="20" text-anchor="middle">'.$h1_2_rank.'</text>   
            
            <text x="300" y="40" fill="black" font-size="20" text-anchor="middle">'.$h1_1_surname.'</text>
            <text x="300" y="60" fill="black" font-size="20" text-anchor="middle">'.$h1_1_name.'</text>
            <text x="300" y="140" fill="black" font-size="20"  text-anchor="middle" >'.$h1_2_surname.'</text>
            <text x="300" y="160" fill="black" font-size="20"  text-anchor="middle">'.$h1_2_name.'</text> 
            ';
         } else {
            echo ' 
            <text x="340" y="100" fill="black" font-size="20" text-anchor="middle">'.$f_1_grp.'</text>
	        <text x="340" y="120" fill="black" font-size="20" text-anchor="middle">'.$f_1_rank.'</text>
             ';
         }
         
         if ($h2_type==1) {
            echo ' 
            <rect width="200" height="80" x="200" y="210"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
            <rect width="200" height="80" x="200" y="310"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
            <line x1="400" y1="250" x2="422" y2="250" style="stroke:black;stroke-width:4" />
            <line x1="400" y1="350" x2="422" y2="350" style="stroke:black;stroke-width:4" />
            <line x1="420" y1="250" x2="420" y2="350" style="stroke:black;stroke-width:4" />
            <line x1="420" y1="300" x2="440" y2="300" style="stroke:black;stroke-width:4" />
            
	        <text x="100" y="240" fill="black" font-size="20" text-anchor="middle">'.$h2_1_grp.'</text>
	        <text x="100" y="260" fill="black" font-size="20" text-anchor="middle">'.$h2_1_rank.'</text>
            <text x="100" y="340" fill="black" font-size="20" text-anchor="middle">'.$h2_2_grp.'</text>
            <text x="100" y="360" fill="black" font-size="20" text-anchor="middle">'.$h2_2_rank.'</text>
            
            <text x="300" y="240" fill="black" font-size="20" text-anchor="middle">'.$h2_1_surname.'</text>
            <text x="300" y="260" fill="black" font-size="20" text-anchor="middle">'.$h2_1_name.'</text>
            <text x="300" y="340" fill="black" font-size="20"  text-anchor="middle" >'.$h2_2_surname.'</text>
            <text x="300" y="360" fill="black" font-size="20"  text-anchor="middle">'.$h2_2_name.'</text>';
         }  else {
            echo ' 
            <text x="340" y="300" fill="black" font-size="20" text-anchor="middle">'.$f_2_grp.'</text>
	        <text x="340" y="320" fill="black" font-size="20" text-anchor="middle">'.$f_2_rank.'</text>
             ';
         }
         
         echo ' 
         <rect width="200" height="80" x="440" y="60"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
         <rect width="200" height="80" x="440" y="260"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
         <rect width="200" height="80" x="680" y="160"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
         <line x1="640" y1="100" x2="662" y2="100" style="stroke:black;stroke-width:4" />
         <line x1="640" y1="300" x2="662" y2="300" style="stroke:black;stroke-width:4" />
         <line x1="660" y1="100" x2="660" y2="300" style="stroke:black;stroke-width:4" />
         <line x1="660" y1="200" x2="680" y2="200" style="stroke:black;stroke-width:4" />
        
         <text x="540" y="210" fill="black" font-size="20"  text-anchor="middle">'.$catName.'</text>
         
         
        <text x="540" y="100" fill="black" font-size="20" text-anchor="middle">'.$f_1_surname.'</text>
        <text x="540" y="120" fill="black" font-size="20" text-anchor="middle">'.$f_1_name.'</text>
        <text x="540" y="300" fill="black" font-size="20" text-anchor="middle">'.$f_2_surname.'</text>
        <text x="540" y="320" fill="black" font-size="20" text-anchor="middle">'.$f_2_name.'</text>
        ';
        
        if ($f_1_pv>0) {
            echo'
            <text x="780" y="240" fill="black" font-size="20" text-anchor="middle">'.$f_1_surname.'</text>
            <text x="780" y="260" fill="black" font-size="20" text-anchor="middle">'.$f_1_name.'</text>
            ';
        } else if ($f_2_pv>0) {
            echo'
            <text x="780" y="240" fill="black" font-size="20" text-anchor="middle">'.$f_2_surname.'</text>
            <text x="780" y="260" fill="black" font-size="20" text-anchor="middle">'.$f_2_name.'</text>
            ';       
        }
      
    } else {
        echo '<svg height="200" width="650">
        <rect width="200" height="80" x="200" y="10"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        <rect width="200" height="80" x="200" y="110"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        <rect width="200" height="80" x="440" y="60"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
   
        <line x1="400" y1="50" x2=422" y2="50" style="stroke:black;stroke-width:4" />
        <line x1="400" y1="150" x2="422" y2="150" style="stroke:black;stroke-width:4" />
        <line x1="420" y1="50" x2="420" y2="150" style="stroke:black;stroke-width:4" />
        <line x1="420" y1="100" x2="440" y2="100" style="stroke:black;stroke-width:4" />';
        
        // Parent steps   
        echo ' 
            <text x="100" y="40" fill="black" font-size="20" text-anchor="middle">'.$f_1_grp.'</text>
            <text x="100" y="60" fill="black" font-size="20" text-anchor="middle">'.$f_1_rank.'</text>
            <text x="100" y="140" fill="black" font-size="20" text-anchor="middle">'.$f_2_grp.'</text>
            <text x="100" y="160" fill="black" font-size="20" text-anchor="middle">'.$f_2_rank.'</text>';
        
        // Competitors   
        echo '        
            <text x="300" y="40" fill="black" font-size="20" text-anchor="middle">'.$f_1_surname.'</text>
            <text x="300" y="70" fill="black" font-size="20" text-anchor="middle">'.$f_1_name.'</text>
                  
            <text x="300" y="140" fill="black" font-size="20"  text-anchor="middle" >'.$f_2_surname.'</text>
            <text x="300" y="170" fill="black" font-size="20"  text-anchor="middle">'.$f_2_name.'</text>';
       
        // Results 
        
          if ($f_1_pv>0) {
              echo'
                <text x="490" y="90" fill="black" font-size="20" text-anchor="middle">'.$f_1_surname.'</text>
                <text x="490" y="120" fill="black" font-size="20" text-anchor="middle">'.$f_1_name.'</text>';
          } else if ($f_2_pv>0) {
             echo'
                <text x="490" y="90" fill="black" font-size="20" text-anchor="middle">'.$f_2_surname.'</text>
                <text x="490" y="120" fill="black" font-size="20" text-anchor="middle">'.$f_2_name.'</text>';
          }
    }
     
    echo ' 
     </svg> ';
}     


?>
