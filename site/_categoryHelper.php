<?php

function create_step_direct($ActualCategory_id){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId) VALUES (?,1)");
    $stmt->bind_param("i", $ActualCategory_id);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId,step_id) VALUES (?,?)");
    $stmt->bind_param("ii", $ActualCategory_id, $step_id);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();


    return $step_id;
}

function create_link($ActualCategory_id, $out_step_id, $step_id_1_id, $rank_1, $step_id_2_id, $rank_2){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO StepLinking (ActualCategoryId, out_step_id, in_step_1_id, rank_in_step_1, in_step_2_id, rank_in_step_2) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("iiiii", $ActualCategory_id, $out_step_id, $step_id_1_id, $rank_1, $step_id_2_id, $rank_2);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
}

function update_step_direct($step_id, $user_id_1, $user_id_2){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("SELECT Id FROM CategoryStep WHERE Id=? AND CategoryStepsTypeId=1");
    $stmt->bind_param("i", $step_id);   
    $stmt->bind_result( $r_step_Id);
    $stmt->execute();
    $stmt->fetch(); 
    $stmt->close();
    
    if (!empty($r_step_Id)){
        $mysqli= ConnectionFactory::GetConnection(); 
        $stmt = $mysqli->prepare("UPDATE Fight SET TournamentCompetitor1Id=?, TournamentCompetitor2Id=? WHERE step_id=?");
        $stmt->bind_param("iii", $user_id_1, $user_id_2, $step_id);         
        $stmt->execute();
        $stmt->close();
    }
}


function create_step_pool_2($ActualCategory_id, $user_id_1, $user_id_2){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId) VALUES (?,1)");
    $stmt->bind_param("i", $ActualCategory_id);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_2);         
    $stmt->execute();
    $stmt->close();

    return $step_id;
}


function create_step_pool_3($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId) VALUES (?,3)");
    $stmt->bind_param("i", $ActualCategory_id);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_2);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_3);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_3);         
    $stmt->execute();
    $stmt->close();

    return $step_id;
}

function create_step_pool_4($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId) VALUES (?,3)");
    $stmt->bind_param("i", $ActualCategory_id);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_2);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_3, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_3);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_3);         
    $stmt->execute();
    $stmt->close();
    

    return $step_id;
}

function create_step_pool_5($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId) VALUES (?,3)");
    $stmt->bind_param("i", $ActualCategory_id);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_2);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_3, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_5);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_3, $user_id_5);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_5);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_3);         
    $stmt->execute();
    $stmt->close();   
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_4, $user_id_5);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_3);         
    $stmt->execute();
    $stmt->close();
    
    return $step_id;
}

function create_steps_6($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6) {
    $pool_1=create_step_pool_3($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5);
    $pool_2=create_step_pool_3($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6);
    
    $half_1=create_step_direct($ActualCategoryId);
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId);
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId)
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_7($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7);
    $pool_2=create_step_pool_3($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6);
    
    $half_1=create_step_direct($ActualCategoryId);
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId);
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId)
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_7($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7, $user_id_8) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7);
    $pool_2=create_step_pool_3($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, $user_id_8);
    
    $half_1=create_step_direct($ActualCategoryId);
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId);
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId)
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_7($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7, $user_id_8, $user_id_9) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7, $user_id_9);
    $pool_2=create_step_pool_3($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, $user_id_8);
    
    $half_1=create_step_direct($ActualCategoryId);
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId);
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId)
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_7($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7, $user_id_8, $user_id_9, $user_id_10) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7, $user_id_9);
    $pool_2=create_step_pool_3($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, $user_id_8, $user_id_10);
    
    $half_1=create_step_direct($ActualCategoryId);
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId);
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId)
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function get_list_from_val($exp_val,$dict){
    $res=array();
    foreach ($dict as $key => $val) {
        if ($val==$exp_val){
             array_push($res, $key);
        }
    }
    return $res;
}

function get_step_results($step_id){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("SELECT CategoryStepsTypeId FROM CategoryStep WHERE Id=?");
    $stmt->bind_param("i", $step_id);         
    $stmt->bind_result($step_type);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close();
    
    if ($step_type==1) {
        //direct step 
        $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id,pv1,TournamentCompetitor2Id,pv2 FROM Fight WHERE step_id=?");
        $stmt->bind_param("i", $step_id);         
        $stmt->bind_result($TournamentCompetitor1Id, $pv1, $TournamentCompetitor2Id, $pv2);     
        $stmt->execute();  
        $stmt->fetch();
        $stmt->close();
        if (!empty($pv1)){
            if ($pv1>0){
                return {1=>$TournamentCompetitor1Id, 2=>$TournamentCompetitor2Id};
            } 
            else {
                return {1=>$TournamentCompetitor2Id, 2=>$TournamentCompetitor1Id};
            }
        } else {
            return NULL;
        }
    } else {
        // pool step  //TODO
        $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id,pv1,TournamentCompetitor2Id,pv2 FROM Fight WHERE step_id=?");
        $stmt->bind_param("i", $step_id);         
        $stmt->bind_result($TournamentCompetitor1Id, $pv1, $TournamentCompetitor2Id, $pv2);     
        $stmt->execute();  
        $results = array();
        // victory and PV
        while ($stmt->fetch()) {
            if (empty($pv1)){
                return NULL;
            }
            
            if (!array_key_exists($TournamentCompetitor1Id, $results)){
                $results[$TournamentCompetitor1Id]=0;
            }
            
            if (!array_key_exists($TournamentCompetitor2Id, $results)){
                $results[$TournamentCompetitor2Id]=0;
            }
            
            $results[$TournamentCompetitor1Id] += 10000*(int)($pv1>0)+10*$pv1;
            $results[$TournamentCompetitor2Id] += 10000*(int)($pv2>0)+10*$pv2;
        }
        $stmt->close();
        
        // Direct fight in case of equality
        $counts = array_count_values($results)
        foreach ($counts as $value => $mult) {   
            $tied_keys=get_list_from_val($value,$results);
            $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id, pv1, TournamentCompetitor2Id FROM Fight WHERE step_id=? AND TournamentCompetitor1Id IN (".implode(',',$tied_keys).") AND TournamentCompetitor2Id IN (".implode(',',$tied_keys).")");
            $stmt->bind_param("i", $step_id);         
            $stmt->bind_result($TCompetitor1Id,$pv1,$TCompetitor2Id);     
            $stmt->execute();  
            while ($stmt->fetch()) {
                $results[$TCompetitor1Id] += (int)($pv1>0)
                $results[$TCompetitor2Id] += (int)($pv1==0)
            }
            $stmt->close(); 
        }
        
        // TODO tie
        if(max(array_count_values($results))>1){
           echo 'ERROR IN the step: tied results';
        }
        
        ksort($results, SORT_NUMERIC)
        
        $res= array();
        $idx=count($results);
        foreach ($results as $cmp => $point) {
            $res[$idx]=$cmp;
            $idx-=1;
        }
        
        return $res;
    }
}





function check_link($ActualCategoryId) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("SELECT out_step_id, in_step_1_id, rank_in_step_1, in_step_2_id, rank_in_step_2 FROM StepLinking WHERE ActualCategoryId=?");
    $stmt->bind_param("i", $ActualCategoryId);         
    $stmt->bind_result($out_step,$in_step_1,$rank_1,$in_step_2,$rank_2);     
    $stmt->execute();  
    $links=array();
    while ($stmt->fetch()){
        $res_1 = get_step_results($in_step_1);
        $res_2 = get_step_results($in_step_2);
        if (!empty($res_1) and !empty($res_2)) {
            $to_update = {"step_Id"=>$out_step, 
                          "f1"=>$res_1[$rank_1], 
                          "f2"=>$res_2[$rank_2]};
            $links[]=$to_update;
        }
    }
    $stmt->close();
    
    foreach ($links as $link){
        update_step_direct($link["step_Id"],$link["f1"],$link["f2"]);
    }
}

function add_fight_result($ActualCategoryId, $fight_id, $pv_1, $pv_2){
    if ($pv_1>=0 && pv_2>=0 && $pv_1*$pv_2==0 && $pv_1+$pv_2>0) {
        $mysqli= ConnectionFactory::GetConnection(); 
        
        $stmt = $mysqli->prepare("UPDATE Fight SET pv1=?, pv2=? WHERE Id=?");
        $stmt->bind_param("iii", $pv_1, $pv_2, $fight_id);         
        $stmt->execute();
        $stmt->close();
        
        check_link($ActualCategoryId);
    }
    
}

function get_full_result($ActualCategoryId){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    // look for linking and more specifically for the step which is only in the output never in the inputs
    $stmt = $mysqli->prepare("SELECT 
                                ST1.out_step_id
                              FROM StepLinking AS ST1
                              LEFT OUTER JOINT StepLinking AS ST2 ON ST1.ActualCategoryId = ST2.ActualCategoryId AND ST1.out_step_id=ST2.in_step_1_id
                              LEFT OUTER JOINT StepLinking AS ST3 ON ST1.ActualCategoryId = ST3.ActualCategoryId AND ST1.out_step_id=ST3.in_step_2_id
                              WHERE ST2.ActualCategoryId IS NULL AND ST3.ActualCategoryId IS NULL AND ST1.ActualCategoryId =?");
    $stmt->bind_param("i", $ActualCategoryId);         
    $stmt->bind_result($final_step_id);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close();
    
    if (empty($final_step_id)){
        stmt = $mysqli->prepare("SELECT Id FROM CategoryStep WHERE ActualCategoryId=?");
        $stmt->bind_param("i", $ActualCategoryId);         
        $stmt->bind_result($step_id);     
        $stmt->execute();  
        $stmt->fetch();
        $stmt->close();
        return get_step_results($step_id);
    } else {
        $result=array();
        $counter=0;
        $current_steps=array($final_step_id);
        $alredy_in=array();
        while (count($current_steps)){
            $new_current_steps=array();
            foreach($current_steps as $stp_id) {
                $step_result = get_step_results($stp_id);
                foreach($step_result as $rank=>$comp_id){
                    if (!in_array($comp_id,$alredy_in)) {
                        array_push($alredy_in, $comp_id);
                        
                        if (!array_key_exists($rank+$counter,$result)){
                            $result[$rank+$counter]=array();
                        }
                        
                        array_push($result[$rank+$counter], $comp_id);
                    }
                }
                
                // GET the parent steps from Links
                 $stmt = $mysqli->prepare("SELECT ST1.in_step_1_id, ST1.in_step_2_id 
                                           FROM StepLinking AS ST1
                                           WHERE ST1.out_step_id =?");
                $stmt->bind_param("i", $stp_id);         
                $stmt->bind_result($in_st_id_1,$in_st_id_2);     
                $stmt->execute();  
                while($stmt->fetch()) {
                    array_push($new_current_steps, $in_st_id_1);
                    array_push($new_current_steps, $in_st_id_2);
                }
                $stmt->close();
            }
            
            $counter+=1
            $current_steps=$new_current_steps;
        }
        
        return $result; 
    }  
}

?>
