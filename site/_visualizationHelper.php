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
                                      forfeit2,
                                      TC2.Id, 
                                      TC2.Name, 
                                      TC2.Surname,
                                      pv2,
                                      forfeit1,
                                      noWinner
                               FROM Fight 
                               INNER JOIN TournamentCompetitor TC1 on TC1.Id = TournamentCompetitor1Id
                               INNER JOIN TournamentCompetitor TC2 on TC2.Id = TournamentCompetitor2Id
                               WHERE Fight.step_id=? order by TieBreakFight ASC");
     $stmt->bind_param("i", $step_id );
     $stmt->bind_result( $tc1_id, $tc1_name, $tc1_surname,$pv1, $ff2, $tc2_id, $tc2_name, $tc2_surname,$pv2, $ff1, $nowin);
     $stmt->execute();
     $fgt = array();
     $comp = array();
     while ($stmt->fetch()){
         $comp[$tc1_id] = array("name"=>$tc1_name,"surname"=>$tc1_surname );
         $comp[$tc2_id] = array("name"=>$tc2_name,"surname"=>$tc2_surname );
         $a_key = $tc1_id.'-'.$tc2_id;
         if (!empty($pv1) || !empty($pv2) || $nowin==1){
             $fgt[$a_key] =  array("pv1"=>$pv1,"pv2"=>$pv2,"ff1"=>$ff1,"ff2"=>$ff2, "nowin"=>$nowin );
         } else if (array_key_exists($a_key, $fgt)){
             unset($fgt[$a_key]);
         }
     }
     $stmt->close(); 
     
     
    // background and grid
     
    echo '
      <svg width="100%" height="auto" viewBox="0 0 '.(300+100*count($comp)).' '.(202+100*count($comp)).'">';
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
        if ($fgt_object['nowin']==1){
             $r1='0'; 
             $r2='0';  
        } else if ($fgt_object['pv1']>0 || $fgt_object['ff2']>0) {
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

function plot_cell($pos_x,$pos_y, $name, $surname){
   echo '<rect width="200" height="80" x="'.$pos_x.'" y="'.$pos_y.'"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
         <text x="'.($pos_x+100).'" y="'.($pos_y+30).'" fill="black" font-size="20" text-anchor="middle">'.$surname.'</text>
         <text x="'.($pos_x+100).'" y="'.($pos_y+60).'" fill="black" font-size="20" text-anchor="middle">'.$name.'</text>';
}

function plot_grp($pos_x,$pos_y, $grp, $rank){
   echo '<text x="'.($pos_x+100).'" y="'.($pos_y+30).'" fill="black" font-size="20" text-anchor="middle">'.$grp.'</text>
	     <text x="'.($pos_x+100).'" y="'.($pos_y+60).'" fill="black" font-size="20" text-anchor="middle">'.$rank.'</text>';
}

function plot_brace($pos_x, $top_y, $bottom_y, $target_y, $isRight){
     echo ' <line x1="'.$pos_x.'" y1="'.$top_y.'" x2="'.($pos_x+22).'" y2="'.$top_y.'" style="stroke:black;stroke-width:4" />
            <line x1="'.$pos_x.'" y1="'.$bottom_y.'" x2="'.($pos_x+22).'" y2="'.$bottom_y.'" style="stroke:black;stroke-width:4" />
            <line x1="'.($pos_x+20).'" y1="'.$top_y.'" x2="'.($pos_x+20).'" y2="'.$bottom_y.'" style="stroke:black;stroke-width:4" />';
    if ($isRight) {
        echo '<line x1="'.($pos_x+20).'" y1="'.$target_y.'" x2="'.($pos_x+40).'" y2="'.$target_y.'" style="stroke:black;stroke-width:4" />';
    } else {
         echo '<line x1="'.($pos_x).'" y1="'.$target_y.'" x2="'.($pos_x+22).'" y2="'.$target_y.'" style="stroke:black;stroke-width:4" />';
    }
            
}

function plot_table($top_step_id, $catName){
    $mysqli= ConnectionFactory::GetConnection(); 
    $stmt = $mysqli->prepare("SELECT  
                                      TCH111.name,
                                      TCH111.Surname,
                                      h11.pv1, 
                                      h11.forfeit2, 
                                      CSH111.Name,
                                      h11l.rank_in_step_1,
                                      CSH111.CategoryStepsTypeId,
                                       
                                      TCH112.name,
                                      TCH112.Surname,
                                      h11.pv2,
                                      h11.forfeit1,
                                      CSH112.Name,
                                      h11l.rank_in_step_2,
                                      CSH112.CategoryStepsTypeId,
                                      
                                      h11.noWinner,
                                      
                                      TCH121.name,
                                      TCH121.Surname,
                                      h12.pv1, 
                                      h12.forfeit2, 
                                      CSH121.Name,
                                      h12l.rank_in_step_1,
                                      CSH121.CategoryStepsTypeId,
                                       
                                      TCH122.name,
                                      TCH122.Surname,
                                      h12.pv2,
                                      h12.forfeit1,
                                      CSH122.Name,
                                      h12l.rank_in_step_2,
                                      CSH122.CategoryStepsTypeId,
                                      
                                      h12.noWinner,
                                      
                                      TCH211.name,
                                      TCH211.Surname,
                                      h21.pv1, 
                                      h21.forfeit2, 
                                      CSH211.Name,
                                      h21l.rank_in_step_1,
                                      CSH211.CategoryStepsTypeId,
                                       
                                      TCH212.name,
                                      TCH212.Surname,
                                      h21.pv2,
                                      h21.forfeit1,
                                      CSH212.Name,
                                      h21l.rank_in_step_2,
                                      CSH212.CategoryStepsTypeId,
                                      
                                      h21.noWinner,
                                      
                                      TCH221.name,
                                      TCH221.Surname,
                                      h22.pv1, 
                                      h22.forfeit2, 
                                      CSH221.Name,
                                      h22l.rank_in_step_1,
                                      CSH221.CategoryStepsTypeId,
                                       
                                      TCH222.name,
                                      TCH222.Surname,
                                      h22.pv2,
                                      h22.forfeit1,
                                      CSH222.Name,
                                      h22l.rank_in_step_2,
                                      CSH222.CategoryStepsTypeId,
                                      
                                      h22.noWinner,
                                      
                                      
                                      TCH11.name,
                                      TCH11.Surname,
                                      h1.pv1, 
                                      h1.forfeit2, 
                                      CSH11.Name,
                                      h1l.rank_in_step_1,
                                      CSH11.CategoryStepsTypeId,
                                       
                                      TCH12.name,
                                      TCH12.Surname,
                                      h1.pv2,
                                      h1.forfeit1,
                                      CSH12.Name,
                                      h1l.rank_in_step_2,
                                      CSH12.CategoryStepsTypeId,
                                      
                                      h1.noWinner,
                                      
                                      
                                      TCH21.name,
                                      TCH21.Surname,
                                      h2.pv1,  
                                      h2.forfeit2,
                                      CSH21.Name,
                                      h2l.rank_in_step_1,
                                      CSH21.CategoryStepsTypeId,
                                       
                                      TCH22.name,
                                      TCH22.Surname,
                                      h2.pv2,
                                      h2.forfeit1,
                                      CSH22.Name,
                                      h2l.rank_in_step_2,
                                      CSH22.CategoryStepsTypeId,
                                      
                                      h2.noWinner,
                                      
                                      
                                      TC1.name,
                                      TC1.Surname,
                                      f.pv1, 
                                      f.forfeit2,
                                      CSF1.Name,
                                      fl.rank_in_step_1,
                                      CSF1.CategoryStepsTypeId,
                                      
                                      TC2.name,
                                      TC2.Surname,
                                      f.pv2,
                                      f.forfeit1,
                                      CSF2.Name,
                                      fl.rank_in_step_2,
                                      CSF2.CategoryStepsTypeId,
                                      
                                      f.noWinner
                                       
                               FROM Fight as f 
                               LEFT OUTER JOIN TournamentCompetitor TC1 on TC1.Id = f.TournamentCompetitor1Id
                               LEFT OUTER JOIN TournamentCompetitor TC2 on TC2.Id = f.TournamentCompetitor2Id
                               LEFT OUTER JOIN StepLinking fl ON fl.out_step_id = f.step_id
                               LEFT OUTER JOIN CategoryStep CSF1 on CSF1.Id = fl.in_step_1_id 
                               LEFT OUTER JOIN CategoryStep CSF2 on CSF2.Id = fl.in_step_2_id
                               
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
                               
                               
                               LEFT OUTER JOIN Fight as h11 ON h11.step_id = CSH11.Id
                               LEFT OUTER JOIN TournamentCompetitor TCH111 on TCH111.Id = h11.TournamentCompetitor1Id
                               LEFT OUTER JOIN TournamentCompetitor TCH112 on TCH112.Id = h11.TournamentCompetitor2Id
                               LEFT OUTER JOIN StepLinking h11l ON h11l.out_step_id = h11.step_id
                               LEFT OUTER JOIN CategoryStep CSH111 on CSH111.Id = h11l.in_step_1_id
                               LEFT OUTER JOIN CategoryStep CSH112 on CSH112.Id = h11l.in_step_2_id
                               
                               LEFT OUTER JOIN Fight as h12 ON h12.step_id = CSH12.Id
                               LEFT OUTER JOIN TournamentCompetitor TCH121 on TCH121.Id = h12.TournamentCompetitor1Id
                               LEFT OUTER JOIN TournamentCompetitor TCH122 on TCH122.Id = h12.TournamentCompetitor2Id
                               LEFT OUTER JOIN StepLinking h12l ON h12l.out_step_id = h12.step_id
                               LEFT OUTER JOIN CategoryStep CSH121 on CSH121.Id = h12l.in_step_1_id
                               LEFT OUTER JOIN CategoryStep CSH122 on CSH122.Id = h12l.in_step_2_id
                               
                               LEFT OUTER JOIN Fight as h21 ON h21.step_id = CSH21.Id
                               LEFT OUTER JOIN TournamentCompetitor TCH211 on TCH211.Id = h21.TournamentCompetitor1Id
                               LEFT OUTER JOIN TournamentCompetitor TCH212 on TCH212.Id = h21.TournamentCompetitor2Id
                               LEFT OUTER JOIN StepLinking h21l ON h21l.out_step_id = h21.step_id
                               LEFT OUTER JOIN CategoryStep CSH211 on CSH211.Id = h21l.in_step_1_id
                               LEFT OUTER JOIN CategoryStep CSH212 on CSH212.Id = h21l.in_step_2_id
                               
                               LEFT OUTER JOIN Fight as h22 ON h22.step_id = CSH22.Id
                               LEFT OUTER JOIN TournamentCompetitor TCH221 on TCH221.Id = h22.TournamentCompetitor1Id
                               LEFT OUTER JOIN TournamentCompetitor TCH222 on TCH222.Id = h22.TournamentCompetitor2Id
                               LEFT OUTER JOIN StepLinking h22l ON h22l.out_step_id = h22.step_id
                               LEFT OUTER JOIN CategoryStep CSH221 on CSH221.Id = h22l.in_step_1_id
                               LEFT OUTER JOIN CategoryStep CSH222 on CSH222.Id = h22l.in_step_2_id
                               
                               WHERE f.step_id=? LIMIT 1");
     $stmt->bind_param("i", $top_step_id );
     $stmt->bind_result(
                        $h11_1_name, $h11_1_surname, $h11_1_pv, $h11_2_ff, $h11_1_grp, $h11_1_rank, $h111_type,
                        $h11_2_name, $h11_2_surname, $h11_2_pv, $h11_1_ff, $h11_2_grp, $h11_2_rank, $h112_type,
                        $h11_nowin,
                        
                        $h12_1_name, $h12_1_surname, $h12_1_pv, $h12_2_ff, $h12_1_grp, $h12_1_rank, $h121_type,
                        $h12_2_name, $h12_2_surname, $h12_2_pv, $h12_1_ff, $h12_2_grp, $h12_2_rank, $h122_type,
                        $h12_nowin,
                        
                        $h21_1_name, $h21_1_surname, $h21_1_pv, $h21_2_ff, $h21_1_grp, $h21_1_rank, $h211_type,
                        $h21_2_name, $h21_2_surname, $h21_2_pv, $h21_1_ff, $h21_2_grp, $h21_2_rank, $h212_type,
                        $h21_nowin,
                        
                        $h22_1_name, $h22_1_surname, $h22_1_pv, $h22_2_ff, $h22_1_grp, $h22_1_rank, $h221_type,
                        $h22_2_name, $h22_2_surname, $h22_2_pv, $h22_1_ff, $h22_2_grp, $h22_2_rank, $h222_type,
                        $h22_nowin,
     
                        $h1_1_name, $h1_1_surname, $h1_1_pv, $h1_2_ff, $h1_1_grp, $h1_1_rank, $h11_type,
                        $h1_2_name, $h1_2_surname, $h1_2_pv, $h1_1_ff, $h1_2_grp, $h1_2_rank, $h12_type,
                        $h1_nowin,
                        
                        $h2_1_name, $h2_1_surname, $h2_1_pv, $h2_2_ff, $h2_1_grp, $h2_1_rank, $h21_type,
                        $h2_2_name, $h2_2_surname, $h2_2_pv, $h2_1_ff, $h2_2_grp, $h2_2_rank, $h22_type,
                        $h2_nowin,
                        
                        $f_1_name,$f_1_surname,$f_1_pv, $f_2_ff, $f_1_grp, $f_1_rank,$h1_type, 
                        $f_2_name,$f_2_surname,$f_2_pv, $f_1_ff, $f_2_grp, $f_2_rank, $h2_type,
                        $f_nowin);
     $stmt->execute();
     $stmt->fetch();
     $stmt->close(); 

     // 2 or 3 fights 
     $withHalf = $h2_type==1 || $h1_type==1;
     $withQuarter = $h11_type==1 || $h12_type==1 || $h21_type==1 || $h22_type==1;
     if ($withQuarter) {
         echo '<svg  width="100%" height="auto" viewBox="0 0 902 800" >';
         echo '  <text x="560" y="390" fill="black" font-size="20"  text-anchor="middle">'.$catName.'</text> ';
         //finale 
         if ($f_1_pv>0 || $f_2_ff>0) {
            plot_cell(680, 360, $f_1_surname, $f_1_name);
        } else if ($f_2_pv>0|| $f_1_ff>0) {
            plot_cell(680, 360, $f_2_surname, $f_2_name);    
        } else {
            plot_cell(680, 360, "", "");
        }
        
        // half
         plot_cell(680, 150, $f_1_surname, $f_1_name);
         plot_cell(680, 510, $f_2_surname, $f_2_name);
         plot_brace(880, 190, 550, 400, false);
         if ($h1_type==1) {
            plot_cell(440, 60, $h1_1_surname, $h1_1_name);
            plot_cell(440, 260, $h1_2_surname, $h1_2_name);
            plot_brace(640, 100, 300, 190, true);
         } else {
            plot_grp(440, 150, $f_1_grp, $f_1_rank);
         }
         
         if ($h2_type==1) {
            plot_cell(440, 460, $h2_1_surname, $h2_1_name);
            plot_cell(440, 660, $h2_2_surname, $h2_2_name);
            plot_brace(640, 500, 700, 550, true);
         }  else {
            plot_grp(440, 510, $f_2_grp, $f_2_rank);
         }
         
         //quarter
         if ($h1_type==1) {
             if ($h11_type==1) {
                plot_grp(0, 10, $h11_1_grp, $h11_1_rank);
                plot_grp(0, 110, $h11_2_grp, $h11_2_rank);
                plot_cell(200, 10, $h11_1_surname, $h11_1_name);
                plot_cell(200, 110, $h11_2_surname, $h11_2_name);
                plot_brace(400, 50, 150, 100, true);
             } else {
                plot_grp(200, 60, $h1_1_grp, $h1_1_rank);
             }
             
             if ($h12_type==1) {
                plot_grp(0, 210, $h12_1_grp, $h12_1_rank);
                plot_grp(0, 310, $h12_2_grp, $h12_2_rank);
                plot_cell(200, 210, $h12_1_surname, $h12_1_name);
                plot_cell(200, 310, $h12_2_surname, $h12_2_name);
                plot_brace(400, 250, 350, 300, true);
             } else {
                plot_grp(200, 260, $h1_2_grp, $h1_2_rank);
             }
         }
         
         if ($h2_type==1) {
             if ($h21_type==1) {
                plot_grp(0, 410, $h21_1_grp, $h21_1_rank);
                plot_grp(0, 510, $h21_2_grp, $h21_2_rank);
                plot_cell(200, 410, $h21_1_surname, $h21_1_name);
                plot_cell(200, 510, $h21_2_surname, $h21_2_name);
                plot_brace(400, 450, 550, 500, true);
             } else {
                plot_grp(200, 460, $h2_1_grp, $h2_1_rank);
             }
             
             if ($h22_type==1) {
                plot_grp(0, 610, $h22_1_grp, $h22_1_rank);
                plot_grp(0, 710, $h22_2_grp, $h22_2_rank);
                plot_cell(200, 610, $h22_1_surname, $h22_1_name);
                plot_cell(200, 710, $h22_2_surname, $h22_2_name);
                plot_brace(400, 650, 750, 700, true);
             } else {
                plot_grp(200, 660, $h2_2_grp, $h2_2_rank);
             }
         }
         
         
     
     } else if ($withHalf) {
         echo '<svg  width="100%" height="auto" viewBox="0 0 900 400" >';
         echo '  <text x="540" y="210" fill="black" font-size="20"  text-anchor="middle">'.$catName.'</text> ';
         if ($h1_type==1) {
            plot_grp(0, 10, $h1_1_grp, $h1_1_rank);
            plot_grp(0, 110, $h1_2_grp, $h1_2_rank);
            plot_cell(200, 10, $h1_1_surname, $h1_1_name);
            plot_cell(200, 110, $h1_2_surname, $h1_2_name);
            plot_brace(400, 50, 150, 100, true);
         } else {
            plot_grp(240, 90, $f_1_grp, $f_1_rank);
         }
         
         if ($h2_type==1) {
            plot_grp(0, 210, $h2_1_grp, $h2_1_rank);
            plot_grp(0, 310, $h2_2_grp, $h2_2_rank);
            plot_cell(200, 210, $h2_1_surname, $h2_1_name);
            plot_cell(200, 310, $h2_2_surname, $h2_2_name);
            plot_brace(400, 250, 350, 300, true);
         }  else {
            plot_grp(240, 270, $f_2_grp, $f_2_rank);
         }
         
         plot_cell(440, 60, $f_1_surname, $f_1_name);
         plot_cell(440, 260, $f_2_surname, $f_2_name);
         plot_brace(640, 100, 300, 200, true);
      
        
        if ($f_1_pv>0 || $f_2_ff>0) {
            plot_cell(680, 160, $f_1_surname, $f_1_name);
        } else if ($f_2_pv>0|| $f_1_ff>0) {
            plot_cell(680, 160, $f_2_surname, $f_2_name);    
        } else {
            plot_cell(680, 160, "", "");   
        }
      
    } else {
        echo '<svg  width="100%" height="auto" viewBox="0 0 650 200" > ';
        plot_brace(400, 50, 150, 100, true);
        
        plot_cell(200, 10, $f_1_surname, $f_1_name);
        plot_cell(200, 110, $f_2_surname, $f_2_name);
        
        plot_grp(0, 10, $f_1_grp, $f_1_rank);
        plot_grp(0, 110, $f_2_grp, $f_2_rank);
        
        // Results 
        
        if ($f_1_pv>0 || $f_2_ff>0) {
            plot_cell(440, 60, $f_1_surname, $f_1_name);
        } else if ($f_2_pv>0|| $f_1_ff>0) {
            plot_cell(440, 60, $f_2_surname, $f_2_name);    
        } else {
            plot_cell(440, 60, "", "");
        }
    }
     
    echo ' 
     </svg> ';
}     


?>
