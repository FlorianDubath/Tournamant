<?php

 
function create_step_direct($ActualCategory_id, $name){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,1,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId,step_id) VALUES (?,?)");
    $stmt->bind_param("ii", $ActualCategory_id, $step_id);         
    $stmt->execute();
    $stmt->close();


    return $step_id;
}

function create_link($ActualCategory_id, $out_step_id, $step_id_1_id, $rank_1, $step_id_2_id, $rank_2){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO StepLinking (ActualCategoryId, out_step_id, in_step_1_id, rank_in_step_1, in_step_2_id, rank_in_step_2) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("iiiiii", $ActualCategory_id, $out_step_id, $step_id_1_id, $rank_1, $step_id_2_id, $rank_2);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
}

function update_step_direct($step_id, $user_id_1, $user_id_2){
    $need_recheck=false;
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("SELECT Id FROM CategoryStep WHERE Id=? AND CategoryStepsTypeId=1");
    $stmt->bind_param("i", $step_id);   
    $stmt->bind_result( $r_step_Id);
    $stmt->execute();
    $stmt->fetch(); 
    $stmt->close();
    
    $stmt = $mysqli->prepare("SELECT count(Id) FROM Fight WHERE step_id=? AND noWinner IS NULL");
    $stmt->bind_param("i", $step_id);   
    $stmt->bind_result( $to_update);
    $stmt->execute();
    $stmt->fetch(); 
    $stmt->close();
    
    if (!empty($r_step_Id) &&  $to_update==1){
        if ($user_id_1<0 && $user_id_2>0) {
		    $stmt = $mysqli->prepare("UPDATE Fight SET TournamentCompetitor2Id=?, pv1=0, pv2=0, forfeit1=1, forfeit2=0, noWinner=0 WHERE step_id=?");
		    $stmt->bind_param("ii", $user_id_2, $step_id);         
		    $stmt->execute();
		    $stmt->close();
		    $need_recheck=true;
        } else  if ($user_id_1>0 && $user_id_2<0) {
		    $stmt = $mysqli->prepare("UPDATE Fight SET TournamentCompetitor1Id=?, pv1=0, pv2=0,  forfeit1=0, forfeit2=1, noWinner=0 WHERE step_id=?");
		    $stmt->bind_param("ii", $user_id_1, $step_id);         
		    $stmt->execute();
		    $stmt->close();
		    $need_recheck=true;
        } else if ($user_id_1<0 && $user_id_2<0) {
		    $stmt = $mysqli->prepare("UPDATE Fight SET pv1=0, pv2=0,  forfeit1=1, forfeit2=1, noWinner=1 WHERE step_id=?");
		    $stmt->bind_param("i", $step_id);         
		    $stmt->execute();
		    $stmt->close();
		    $need_recheck=true;
        } else {
		    $stmt = $mysqli->prepare("UPDATE Fight SET TournamentCompetitor1Id=?, TournamentCompetitor2Id=? WHERE step_id=?");
		    $stmt->bind_param("iii", $user_id_1, $user_id_2, $step_id);         
		    $stmt->execute();
		    $stmt->close();
        }
    }
    return $need_recheck;
}


function update_sorter_step($ActualCategory_id){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("SELECT Id, Data FROM CategoryStep WHERE ActualCategoryId=? AND CategoryStepsTypeId=30"); 
    $stmt->bind_param("i", $ActualCategory_id);   
    $stmt->bind_result($s_step_Id,$data);
    $stmt->execute();
    $stmt->fetch(); 
    $stmt->close(); 
    
    
    if (!empty($s_step_Id) && (empty($data) || $data=='')){
        // get the 3 pool steps
         $stmt = $mysqli->prepare("SELECT in_step_1_id FROM StepLinking WHERE ActualCategoryId=? AND out_step_id=?");  
         $stmt->bind_param("ii", $ActualCategory_id, $s_step_Id);   
         $stmt->bind_result($in_step_1_Id);
         $stmt->execute();
         $pool_step_ids=array();
         while ($stmt->fetch()){
              $pool_step_ids[]=$in_step_1_Id;
         } 
         $stmt->close(); 

         
        // get the result for the pool steps
         $all_result=True;
         $selected = rand(0, 2);
         $first = array();
         $second= array();
         $index=0;
         foreach ($pool_step_ids as $step_id) {
             $res_1 = get_step_results($step_id)["ordered"];
             if (empty($res_1)) {
                 $all_result=False;
             } else {
                  if ($index == $selected) {
                     $second[] = $res_1[1];
                     $second[] = $res_1[2];
                  } else {
                     $first[] = $res_1[1];
                  }
             }
             $index+=1;
         }
         
         if ($all_result){
         
          // create the status and store it into data
          $data_obj=$first[0].",".$first[1].",".$second[0].",".$second[1];
          $data = $data_obj;
          
          $stmt = $mysqli->prepare("UPDATE CategoryStep SET DATA=? WHERE Id=?"); 
          $stmt->bind_param("si", $data, $s_step_Id);   
          $stmt->execute();
          $stmt->close(); 
         }
         
    }
}




function create_step_pool_2($ActualCategory_id, $user_id_1, $user_id_2, $name){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,10,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_2);         
    $stmt->execute();
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_2);         
    $stmt->execute();
    $stmt->close();

    return $step_id;
}


function create_step_pool_3($ActualCategory_id, $user_id_1, $user_id_2, $user_id_3, $name) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,3,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);           
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

function create_resolver_3($ActualCategory_id, $name) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,30,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);           
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    return $step_id;
}

function create_step_pool_4($ActualCategory_id, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $name) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,4,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);           
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

function create_step_pool_5($ActualCategory_id, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $name) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,5,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);         
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
    $pool_1=create_step_pool_3($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, 'Groupe A');
    $pool_2=create_step_pool_3($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, 'Groupe B');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_7($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7, 'Groupe A');
    $pool_2=create_step_pool_3($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, 'Groupe B');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_8($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7, $user_id_8) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7, 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, $user_id_8, 'Groupe B');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_9($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7, $user_id_8, $user_id_9) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7, $user_id_9, 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, $user_id_8,  'Groupe B');
  
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_10($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7, $user_id_8, $user_id_9, $user_id_10) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7, $user_id_9, 'Groupe A');
    $pool_2=create_step_pool_5($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, $user_id_8, $user_id_10,  'Groupe B');
  
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_11($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_ids[0], $user_ids[3], $user_ids[6], $user_ids[9], 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_ids[1], $user_ids[4], $user_ids[7], $user_ids[10], 'Groupe B');
    $pool_3=create_step_pool_3($ActualCategoryId, $user_ids[2], $user_ids[5], $user_ids[8], 'Groupe C');
    
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_1, $pool_3, 2, $pool_2, 2);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_2, $pool_3, 1, $pool_1, 2);
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $quarter_1, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_2, 1, $quarter_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_12($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_3($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], 'Groupe A');
    $pool_2=create_step_pool_3($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], 'Groupe B');
    $pool_3=create_step_pool_3($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], 'Groupe C');
    $pool_4=create_step_pool_3($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], 'Groupe D');

    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_1, $pool_1, 1, $pool_2, 2);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_2, $pool_3, 1, $pool_4, 2); 
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_3, $pool_1, 2, $pool_2, 1);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_4, $pool_3, 2, $pool_4, 1);

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $quarter_1, 1, $quarter_2, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $quarter_3, 1, $quarter_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_13($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], 'Groupe A');
    $pool_2=create_step_pool_3($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], 'Groupe B');
    $pool_3=create_step_pool_3($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], 'Groupe C');
    $pool_4=create_step_pool_3($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], 'Groupe D');
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_1, $pool_1, 1, $pool_2, 2);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_2, $pool_3, 1, $pool_4, 2); 
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_3, $pool_1, 2, $pool_2, 1);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_4, $pool_3, 2, $pool_4, 1);

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $quarter_1, 1, $quarter_2, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $quarter_3, 1, $quarter_4, 1);
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_14($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], 'Groupe B');
    $pool_3=create_step_pool_3($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], 'Groupe C');
    $pool_4=create_step_pool_3($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], 'Groupe D');
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_1, $pool_1, 1, $pool_2, 2);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_2, $pool_3, 1, $pool_4, 2); 
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_3, $pool_1, 2, $pool_2, 1);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_4, $pool_3, 2, $pool_4, 1);

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $quarter_1, 1, $quarter_2, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $quarter_3, 1, $quarter_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_15($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], 'Groupe B');
    $pool_3=create_step_pool_4($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], 'Groupe C');
    $pool_4=create_step_pool_3($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], 'Groupe C');
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_1, $pool_1, 1, $pool_2, 2);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_2, $pool_3, 1, $pool_4, 2); 
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_3, $pool_1, 2, $pool_2, 1);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_4, $pool_3, 2, $pool_4, 1);

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $quarter_1, 1, $quarter_2, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $quarter_3, 1, $quarter_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_16($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], 'Groupe B');
    $pool_3=create_step_pool_4($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], 'Groupe C');
    $pool_4=create_step_pool_4($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], $user_ids[15], 'Groupe D');

    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_1, $pool_1, 1, $pool_2, 2);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_2, $pool_3, 1, $pool_4, 2); 
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_3, $pool_1, 2, $pool_2, 1);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_4, $pool_3, 2, $pool_4, 1);

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $quarter_1, 1, $quarter_2, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $quarter_3, 1, $quarter_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_17($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], $user_ids[16], 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], 'Groupe B');
    $pool_3=create_step_pool_4($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], 'Groupe C');
    $pool_4=create_step_pool_4($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], $user_ids[15], 'Groupe D');

    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_1, $pool_1, 1, $pool_2, 2);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_2, $pool_3, 1, $pool_4, 2); 
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_3, $pool_1, 2, $pool_2, 1);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_4, $pool_3, 2, $pool_4, 1);

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $quarter_1, 1, $quarter_2, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $quarter_3, 1, $quarter_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_18($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], $user_ids[16], 'Groupe A');
    $pool_2=create_step_pool_5($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], $user_ids[17], 'Groupe B');
    $pool_3=create_step_pool_4($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], 'Groupe C');
    $pool_4=create_step_pool_4($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], $user_ids[15], 'Groupe D');

    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_1, $pool_1, 1, $pool_2, 2);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_2, $pool_3, 1, $pool_4, 2); 
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_3, $pool_1, 2, $pool_2, 1);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_4, $pool_3, 2, $pool_4, 1);

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $quarter_1, 1, $quarter_2, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $quarter_3, 1, $quarter_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_19($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], $user_ids[16], 'Groupe A');
    $pool_2=create_step_pool_5($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], $user_ids[17], 'Groupe B');
    $pool_3=create_step_pool_5($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], $user_ids[18], 'Groupe C');
    $pool_4=create_step_pool_4($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], $user_ids[15], 'Groupe D');

    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_1, $pool_1, 1, $pool_2, 2);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_2, $pool_3, 1, $pool_4, 2); 
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_3, $pool_1, 2, $pool_2, 1);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_4, $pool_3, 2, $pool_4, 1);

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $quarter_1, 1, $quarter_2, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $quarter_3, 1, $quarter_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_20($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], $user_ids[16], 'Groupe A');
    $pool_2=create_step_pool_5($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], $user_ids[17], 'Groupe B');
    $pool_3=create_step_pool_5($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], $user_ids[18], 'Groupe C');
    $pool_4=create_step_pool_5($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], $user_ids[15], $user_ids[19], 'Groupe D');

    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_1, $pool_1, 1, $pool_2, 2);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_2, $pool_3, 1, $pool_4, 2); 
    
    $quarter_1=create_step_direct($ActualCategoryId,'Quart 1');
    create_link($ActualCategoryId, $quarter_3, $pool_1, 2, $pool_2, 1);
    
    $quarter_2=create_step_direct($ActualCategoryId,'Quart 2');
    create_link($ActualCategoryId, $quarter_4, $pool_3, 2, $pool_4, 1);

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $quarter_1, 1, $quarter_2, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $quarter_3, 1, $quarter_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
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
    
    $stmt = $mysqli->prepare("SELECT count(Id) FROM Fight WHERE step_id=? and pv1 IS NULL");
    $stmt->bind_param("i", $step_id);         
    $stmt->bind_result($missing_fight);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close();
    if ($missing_fight>0){
        return array("ordered"=>Null,"full"=>Null);
    }
    
    $stmt = $mysqli->prepare("SELECT CategoryStepsTypeId FROM CategoryStep WHERE Id=?");
    $stmt->bind_param("i", $step_id);         
    $stmt->bind_result($step_type);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close();
    
    if ($step_type==1) {
        //direct step 
        $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id, pv1, forfeit1, TournamentCompetitor2Id, pv2, forfeit2, noWinner FROM Fight WHERE step_id=?");
        $stmt->bind_param("i", $step_id);         
        $stmt->bind_result($TournamentCompetitor1Id, $pv1, $ff1, $TournamentCompetitor2Id, $pv2, $ff2, $noWinner);     
        $stmt->execute();  
        $stmt->fetch();
        $stmt->close();
        if ($noWinner==1){
                return array("ordered"=>array(1=>-1, 2=>-1),"full"=>array(1=>-1, 2=>-1));
        } else if ($pv1>0 || $forfeit2==1){
                return array("ordered"=>array(1=>$TournamentCompetitor1Id, 2=>$TournamentCompetitor2Id),"full"=>array(1=>$TournamentCompetitor1Id, 2=>$TournamentCompetitor2Id));
        } else if ($pv2>0 || $forfeit1==1){
                return array("ordered"=>array(1=>$TournamentCompetitor2Id, 2=>$TournamentCompetitor1Id),"full"=>array(1=>$TournamentCompetitor2Id, 2=>$TournamentCompetitor1Id));
        } else {
                return array("ordered"=>NULL,"full"=>NULL);
        }
        
    } else if ($step_type<10) {
        // pool step  
  
        $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id,pv1, forfeit1,TournamentCompetitor2Id,pv2, forfeit2, TieBreakFight, noWinner FROM Fight WHERE step_id=?");
        $stmt->bind_param("i", $step_id);         
        $stmt->bind_result($TournamentCompetitor1Id, $pv1, $ff1, $TournamentCompetitor2Id, $pv2, $ff2, $tb, $nowin);     
        $stmt->execute();  
        $results = array();
        // victory and PV
        while ($stmt->fetch()) {
            if ($nowin==0){
                if (!array_key_exists($TournamentCompetitor1Id, $results)){
                    $results[$TournamentCompetitor1Id]=0;
                }
                
                if (!array_key_exists($TournamentCompetitor2Id, $results)){
                    $results[$TournamentCompetitor2Id]=0;
                }
                
                if ($tb==0) {
                    $results[$TournamentCompetitor1Id] += 1000000000*(int)($pv1>0 || $ff2==1)+1000000*$pv1;
                    $results[$TournamentCompetitor2Id] += 1000000000*(int)($pv2>0 || $ff1==1)+1000000*$pv2;
                } else {
                    $results[$TournamentCompetitor1Id] += 10000*(int)($pv1>0 || $ff2==1)+10*$pv1;
                    $results[$TournamentCompetitor2Id] += 10000*(int)($pv2>0 || $ff1==1)+10*$pv2;
                }
            }
        }
        $stmt->close();
        
        // Direct fight in case of equality
        $counts = array_count_values($results);
        foreach ($counts as $value => $mult) {   
            $tied_keys=get_list_from_val($value,$results);
            $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id, TournamentCompetitor2Id, pv1, pv2, forfeit1, forfeit2, noWinner FROM Fight WHERE step_id=? AND TournamentCompetitor1Id IN (".implode(',',$tied_keys).") AND TournamentCompetitor2Id IN (".implode(',',$tied_keys).")");
            $stmt->bind_param("i", $step_id);         
            $stmt->bind_result($TCompetitor1Id, $TCompetitor2Id, $pv1, $pv2, $ff1, $ff2, $nowin);     
            $stmt->execute();  
            while ($stmt->fetch()) {
                $results[$TCompetitor1Id] += (int)($pv1>0 || $ff2==1);
                $results[$TCompetitor2Id] += (int)($pv2>0 || $ff1==1);
            }
            $stmt->close(); 
        }
        $tie =0;
        if (count($results)>0) {
            $tie= max(array_count_values($results));
        }
        
        arsort($results, SORT_NUMERIC);

        $res= array();
        $idx=1;
        foreach ($results as $cmp => $point) {
            $res[$idx]=$cmp;
            $idx+=1;
        }
        
        return array("ordered"=>$res,"full"=>$results,"tie"=>$tie);
    } else if ($step_type==10) {
        // 2 fighter category
        $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id,pv1,forfeit1,TournamentCompetitor2Id,pv2,forfeit2, noWinner FROM Fight WHERE step_id=?");
        $stmt->bind_param("i", $step_id);         
        $stmt->bind_result($TournamentCompetitor1Id, $pv1, $ff1, $TournamentCompetitor2Id, $pv2, $ff2, $nowin);     
        $stmt->execute();  
        $results = array();
        // victory and PV
        while ($stmt->fetch()) {
            if ($nowin!=1){
                if (!array_key_exists($TournamentCompetitor1Id, $results)){
                    $results[$TournamentCompetitor1Id]=0;
                }
                
                if (!array_key_exists($TournamentCompetitor2Id, $results)){
                    $results[$TournamentCompetitor2Id]=0;
                }
                $results[$TournamentCompetitor1Id] += 10000*(int)($pv1>0  || $ff2==1)+10*$pv1;
                $results[$TournamentCompetitor2Id] += 10000*(int)($pv2>0  || $ff1==1)+10*$pv2;
            }
        }
        $stmt->close();
        arsort($results, SORT_NUMERIC);
        $res= array();
        $idx=1;
        foreach ($results as $cmp => $point) {
            $res[$idx]=$cmp;
            $idx+=1;
        }
        
        return array("ordered"=>$res,"full"=>$results);
    } else if ($step_type==30) {
     
        // sorter step
        $mysqli= ConnectionFactory::GetConnection(); 
        $results = array();
        $res= array();
    
        $stmt = $mysqli->prepare("SELECT Id, Data FROM CategoryStep WHERE Id=? AND CategoryStepsTypeId=30"); 
        $stmt->bind_param("i", $step_id);   
        $stmt->bind_result($s_step_Id,$data);
        $stmt->execute();
        $stmt->fetch(); 
        $stmt->close(); 
        if (!empty($data) && strlen($data)>4) {
            $res_json = explode(',',$data);
            var_dump($res_json);
            $res[1]=intval($res_json[0]);
            $res[2]=intval($res_json[1]);
            $res[3]=intval($res_json[2]);
            $res[4]=intval($res_json[3]);
        }
     
        return array("ordered"=>$res,"full"=>$results);
    }
}

function addTieBreakFight($ActualCategoryId, $stepId, $compId1, $compId2, $HMD1, $HMD2, $tie){
       $mysqli= ConnectionFactory::GetConnection(); 
	   if ($HMD1 + $HMD2==0) {
                   // normal step: in case of equality add a 3rd fight
		   $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id, TieBreakFight) VALUES (?,?,?,?,?)");
		   $stmt->bind_param("iiiii", $ActualCategoryId, $stepId, $compId1, $compId2, $tie);         
		   $stmt->execute();
		   $stmt->close();
           } else if ($HMD1 + $HMD2==1) {
                   // one Hansokumake Direct insert a fight win by forfeit
                   $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id, pv1,pv2,forfeit1,forfeit2, TieBreakFight) VALUES (?,?,?,?,0,0,?,?,?)");
		   $stmt->bind_param("iiiiiii", $ActualCategoryId, $stepId, $compId1, $compId2, $HMD1, $HMD2, $tie);         
		   $stmt->execute();
		   $stmt->close();
           } else if ($HMD1 + $HMD2==2) {
                  // double hansokumake: do not insert fight
           } 
}


function check_for_tie($ActualCategoryId) {

    $mysqli= ConnectionFactory::GetConnection(); 
    // check for 2-fighter cat
    $stmt = $mysqli->prepare("SELECT Id FROM CategoryStep WHERE ActualCategoryId=? AND CategoryStepsTypeId=10");
    $stmt->bind_param("i", $ActualCategoryId);         
    $stmt->bind_result($step_2f_id);     
    $stmt->execute();
    $stmt->fetch();
    $stmt->close(); 
    
    if (isset($step_2f_id) && $step_2f_id>0) {
        // check if a 3rd fight is needed
        $stmt = $mysqli->prepare("select count(Id), sum((pv1>pv2 or forfeit2=1)), sum(pv2>pv1 or forfeit1=1), TournamentCompetitor1Id, TournamentCompetitor2Id from Fight WHERE step_id=? and noWinner=0");
        $stmt->bind_param("i", $step_2f_id);         
        $stmt->bind_result($tot_f,$win_1,$win_2, $user_id_1, $user_id_2);     
        $stmt->execute();  
        $stmt->fetch();
        $stmt->close(); 
        if ($tot_f>=2 && $win_1 == $win_2) {
           $stmt = $mysqli->prepare("SELECT c1.Hansokumake, c2.Hansokumake FROM Fight INNER JOIN TournamentCompetitor AS c1 ON c1.Id = TournamentCompetitor1Id INNER JOIN TournamentCompetitor AS c2 ON c2.Id = TournamentCompetitor2Id WHERE step_id=?");
           $stmt->bind_param("i", $step_2f_id);         
           $stmt->bind_result($HMD1,$HMD2);     
           $stmt->execute();  
           $stmt->fetch();
           $stmt->close(); 
           addTieBreakFight($ActualCategoryId, $step_2f_id, $user_id_1, $user_id_2, $HMD1, $HMD2, 0);
        }
    } else {
        // check tie in pool
        $stmt = $mysqli->prepare("SELECT out_step_id, in_step_1_id, rank_in_step_1, in_step_2_id, rank_in_step_2 FROM StepLinking WHERE ActualCategoryId=?");
        $stmt->bind_param("i", $ActualCategoryId);         
        $stmt->bind_result($out_step,$in_step_1,$rank_1,$in_step_2,$rank_2);     
        $stmt->execute();
        $linked=array();  
        while ($stmt->fetch()){
             if (!array_key_exists($in_step_1, $linked)) {
                 $linked[$in_step_1]=array();
             } 
             $linked[$in_step_1][count($linked[$in_step_1])]=$rank_1;
             if (!array_key_exists($in_step_2, $linked)) {
                 $linked[$in_step_2]=array();
             } 
             $linked[$in_step_2][count($linked[$in_step_2])]=$rank_2;
        }
        
        foreach ( $linked as $step_id=>$rank_list){
            $step_res = get_step_results($step_id);
            
            $ranks = array_values($rank_list);
            if (array_key_exists("tie", $step_res) && $step_res["tie"]>0) {  
             /// WE assume that 
             ///                due to pool structure ties are between 3 fighters 
             ///                if 2nd or 3rd is requested so is lower ranks (1rst (2nd))
             ///                no tiebreak needed in isolated pool
                $comp_list=array();
                // 1rst
                if (in_array(1,$ranks) &&  $step_res["full"][$step_res["ordered"][1]] == $step_res["full"][$step_res["ordered"][2]]){
                       array_push($comp_list, $step_res["ordered"][1]);
                       array_push($comp_list, $step_res["ordered"][2]);
                       array_push($comp_list, $step_res["ordered"][3]);
                }
                // not 1rst but second
                else if (in_array(2,$ranks) &&  $step_res["full"][$step_res["ordered"][2]] == $step_res["full"][$step_res["ordered"][3]]){
                       array_push($comp_list, $step_res["ordered"][2]);
                       array_push($comp_list, $step_res["ordered"][3]);
                       array_push($comp_list, $step_res["ordered"][4]);
                }
                
                 // 3rd and bellow
                else if (in_array(3,$ranks) &&  $step_res["full"][$step_res["ordered"][3]] == $step_res["full"][$step_res["ordered"][4]]){
                
                       array_push($comp_list, $step_res["ordered"][3]);
                       array_push($comp_list, $step_res["ordered"][4]);
                       array_push($comp_list, $step_res["ordered"][5]);
                }

                
                if (count($comp_list)>0){
		        // Check for HMD
		        $stmt = $mysqli->prepare("SELECT Hansokumake FROM TournamentCompetitor WHERE Id IN (?,?,?)");
		  	$stmt->bind_param("iii", $comp_list[0], $comp_list[1], $comp_list[2]);         
		        $stmt->bind_result($HMD); 
		        $stmt->execute();  
		        $hmds=array();  
		        $index=0;
		   	while ($stmt->fetch()) {
		   	   $hmds[$index]=$HMD;
		   	   $index+=1;
		   	}
		   	$stmt->close(); 
		        
                       // Add tiebreak fights
                       addTieBreakFight($ActualCategoryId, $step_id, $comp_list[0], $comp_list[1], $hmds[0], $hmds[1], 1);
                       addTieBreakFight($ActualCategoryId, $step_id, $comp_list[0], $comp_list[2], $hmds[0], $hmds[2], 1);
                       addTieBreakFight($ActualCategoryId, $step_id, $comp_list[1], $comp_list[2], $hmds[1], $hmds[2], 1);
                }
            }
        }
    }
}


function check_link($ActualCategoryId) {
    check_for_tie($ActualCategoryId);
    update_sorter_step($ActualCategoryId);
    
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("SELECT out_step_id, in_step_1_id, rank_in_step_1, in_step_2_id, rank_in_step_2 FROM StepLinking WHERE ActualCategoryId=?");
    $stmt->bind_param("i", $ActualCategoryId);         
    $stmt->bind_result($out_step,$in_step_1,$rank_1,$in_step_2,$rank_2);     
    $stmt->execute();  
    $links=array();
    while ($stmt->fetch()){
        $res_1 = get_step_results($in_step_1)["ordered"];
        $res_2 = get_step_results($in_step_2)["ordered"];
        

        if (!empty($res_1) and !empty($res_2)) {        
            $to_update = array("step_Id"=>$out_step, 
                          "f1"=>$res_1[$rank_1], 
                          "f2"=>$res_2[$rank_2]);
            $links[]=$to_update;
        }
    }
    $stmt->close();
    
    $need_recheck=false;
    foreach ($links as $link){
        $need_recheck |= update_step_direct($link["step_Id"],$link["f1"],$link["f2"]);
    }
    
    if ($need_recheck){
        check_link($ActualCategoryId);
    }  
}


function getCompetitorFromFight($fight_id, $is_comp_1){
   $mysqli= ConnectionFactory::GetConnection(); 
   $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id, TournamentCompetitor2Id FROM Fight WHERE Id=?");
   $stmt->bind_param("i", $fight_id);         
   $stmt->bind_result($comp_1,$comp_2);     
   $stmt->execute();  
   $stmt->fetch();
   $stmt->close();
   return $is_comp_1?$comp_1:$comp_2;
}

function add_HMD($ActualCategoryId, $fight_id, $hmd_1, $hmd_2){
   $mysqli= ConnectionFactory::GetConnection(); 
   // find all fights to be fighted in the category and put them as lost by forfeit, if the other is already forfeit => nowin
   // Set the TournamentCompetitor as HandokuMake
   if ($hmd_1==1){
      $cid = getCompetitorFromFight($fight_id, true);
      
      $stmt = $mysqli->prepare("UPDATE Fight SET forfeit1=1, noWinner=forfeit2 WHERE ActualCategoryId=? AND TournamentCompetitor1Id=? AND pv1 IS NOT NULL");
      $stmt->bind_param("ii",$ActualCategoryId, $cid);       
      $stmt->execute();  
      $stmt->close();
      $stmt = $mysqli->prepare("UPDATE Fight SET forfeit2=1, noWinner=forfeit1 WHERE ActualCategoryId=? AND TournamentCompetitor2Id=? AND pv1 IS NOT NULL");
      $stmt->bind_param("ii",$ActualCategoryId, $cid);       
      $stmt->execute();  
      $stmt->close();
      
      $stmt = $mysqli->prepare("UPDATE TournamentCompetitor SET Hansokumake=1 WHERE Id=?");
      $stmt->bind_param("i", $cid);       
      $stmt->execute();  
      $stmt->close();
   }
   
   if ($hmd_2==1){
      $cid = getCompetitorFromFight($fight_id, false);
      
      $stmt = $mysqli->prepare("UPDATE Fight SET forfeit1=1, noWinner=forfeit2 WHERE ActualCategoryId=? AND TournamentCompetitor1Id=? AND pv1 IS NOT NULL");
      $stmt->bind_param("ii",$ActualCategoryId, $cid);       
      $stmt->execute();  
      $stmt->close();
      $stmt = $mysqli->prepare("UPDATE Fight SET forfeit2=1, noWinner=forfeit1 WHERE ActualCategoryId=? AND TournamentCompetitor2Id=? AND pv1 IS NOT NULL");
      $stmt->bind_param("ii",$ActualCategoryId, $cid);       
      $stmt->execute();  
      $stmt->close();
      
      $stmt = $mysqli->prepare("UPDATE TournamentCompetitor SET Hansokumake=1 WHERE Id=?");
      $stmt->bind_param("i", $cid);       
      $stmt->execute();  
      $stmt->close();
   }
   
   check_link($ActualCategoryId);
   if (isCatCompleted($ActualCategoryId)) {
	close_category($ActualCategoryId);
   }
}


function add_fight_result($ActualCategoryId, $fight_id, $pv_1, $pv_2, $ff_1, $ff_2, $noWin){
    $mysqli= ConnectionFactory::GetConnection(); 
    if ($noWin) {
        $stmt = $mysqli->prepare("UPDATE Fight SET noWinner=1, pv1=0, pv2=0, forfeit1=?, forfeit2=?, ResultSavedBy=?  WHERE Id=?");
        $stmt->bind_param("iiii", $ff_1, $ff_2, $_SESSION['_UserId'], $fight_id);       
        $stmt->execute();
        $stmt->close();
    } else if ($ff_1>0 || $ff_2>0) {
        $stmt = $mysqli->prepare("UPDATE Fight SET noWinner=0, pv1=0, pv2=0, forfeit1=?, forfeit2=?, ResultSavedBy=? WHERE Id=?");
        $stmt->bind_param("iiii", $ff_1, $ff_2, $_SESSION['_UserId'], $fight_id);       
        $stmt->execute();
        $stmt->close();
    } else if ($pv_1>=0 && $pv_2>=0 && $pv_1*$pv_2==0 && $pv_1+$pv_2>0) {
        $stmt = $mysqli->prepare("UPDATE Fight SET noWinner=0, pv1=?, pv2=?, forfeit1=0, forfeit2=0, ResultSavedBy=? WHERE Id=?");
        $stmt->bind_param("iiii", $pv_1, $pv_2, $_SESSION['_UserId'], $fight_id);       
        $stmt->execute();
        $stmt->close();
    } else {
    	echo 'Error in data'; exit;
    }
    
    check_link($ActualCategoryId);
    if (isCatCompleted($ActualCategoryId)) {
        close_category($ActualCategoryId);
    }
    
}

function isCatCompleted($ActualCategoryId){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    // look for linking and more specifically for the step which is only in the output never in the inputs
    $stmt = $mysqli->prepare("SELECT count(Id) 
                              FROM Fight 
                              WHERE ActualCategoryId =? AND pv1 IS NULL");
    $stmt->bind_param("i", $ActualCategoryId);         
    $stmt->bind_result($missing_fight);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close();
    return $missing_fight==0;
}

function get_full_result($ActualCategoryId){
    if (!isCatCompleted($ActualCategoryId)){
        return NULL;
    }

    $mysqli= ConnectionFactory::GetConnection(); 
    
    // look for linking and more specifically for the step which is only in the output never in the inputs
    $stmt = $mysqli->prepare("SELECT 
                              ST1.out_step_id
                              FROM StepLinking AS ST1
                              LEFT OUTER JOIN StepLinking AS ST2 ON ST1.ActualCategoryId = ST2.ActualCategoryId AND ST1.out_step_id=ST2.in_step_1_id
                              LEFT OUTER JOIN StepLinking AS ST3 ON ST1.ActualCategoryId = ST3.ActualCategoryId AND ST1.out_step_id=ST3.in_step_2_id
                              WHERE ST2.ActualCategoryId IS NULL AND ST3.ActualCategoryId IS NULL AND ST1.ActualCategoryId =?");
    $stmt->bind_param("i", $ActualCategoryId);         
    $stmt->bind_result($final_step_id);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close();
    
    if (empty($final_step_id)){
        $stmt = $mysqli->prepare("SELECT Id FROM CategoryStep WHERE ActualCategoryId=?");
        $stmt->bind_param("i", $ActualCategoryId);         
        $stmt->bind_result($step_id);     
        $stmt->execute();  
        $stmt->fetch();
        $stmt->close();
        return get_step_results($step_id)["ordered"];
    } else {
        $result=array();
        $counter=0;
        $current_steps=array($final_step_id);
        $alredy_in=array();
              
        while (count($current_steps)){
            $new_current_steps=array();
            foreach($current_steps as $index=>$stp_id) {
                $step_result = get_step_results($stp_id)["ordered"];
               
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
            
            $counter+=1;
            $current_steps=$new_current_steps;
        }
        
        return $result; 
    }  
}

function promote_Unique($tc_id_1){
    $mysqli= ConnectionFactory::GetConnection(); 
    $stmt = $mysqli->prepare("SELECT
                                  TournamentAgeCategory.Name,
                                  TournamentAgeCategory.ShortName,
                                  TournamentGender.Name,     
                                  IFNULL(-TC1.MaxWeight, IFNULL(concat('+',TC1.MinWeight),'OPEN')),
                                  TournamentRegistration. CompetitorId
                              FROM TournamentCategory TC1
                              INNER JOIN TournamentAgeCategory ON TournamentAgeCategory.Id=TC1.AgeCategoryId
                              INNER JOIN TournamentGender on TournamentGender.Id=TournamentAgeCategory.GenderId
                              INNER JOIN TournamentWeighting on TournamentAgeCategory.Id = TournamentWeighting.AgeCategoryId
                              LEFT OUTER JOIN TournamentRegistration ON TournamentRegistration.CategoryId=TC1.Id AND WeightChecked=1 

                              WHERE  TC1.Id=?
                         ");               
    $stmt->bind_param("i", $tc_id_1);         
    $stmt->bind_result($cat_n,$cat_sn,$cat_gen,$weight,$compId);   
    $stmt->execute(); 
    $stmt->fetch(); 
    $stmt->close(); 
    
    $name = $cat_sn.' '.$cat_n.' '.$cat_gen.' '.$weight;

    $stmt = $mysqli->prepare("INSERT INTO ActualCategory (CategoryId,Name,IsCompleted,Dummy) VALUES (?,?,1,1)");
    $stmt->bind_param("is", $tc_id_1, $name);         
    $stmt->execute();
    $actual_category_id = $mysqli->insert_id;
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO ActualCategoryResult (ActualCategoryId,Competitor1Id,RankId,Medal) VALUES (?,?,1,1)");
    $stmt->bind_param("ii", $actual_category_id, $compId );   
    $stmt->execute();
    $actual_category_id = $mysqli->insert_id;
    $stmt->close();      
}

function open_Category($tc_id_1,$tc_id_2,$name){
    $mysqli= ConnectionFactory::GetConnection(); 
    $actual_category_id = -1;
    if ($tc_id_2>0){
        $stmt = $mysqli->prepare("INSERT INTO ActualCategory (CategoryId,Category2Id,Name) VALUES (?,?,?)");
        $stmt->bind_param("iis", $tc_id_1, $tc_id_2, $name);         
        $stmt->execute();
        $actual_category_id = $mysqli->insert_id;
        $stmt->close();
    } else {
        $stmt = $mysqli->prepare("INSERT INTO ActualCategory (CategoryId,Name) VALUES (?,?)");
        $stmt->bind_param("is", $tc_id_1, $name);         
        $stmt->execute();
        $actual_category_id = $mysqli->insert_id;
        $stmt->close();
    }
    
    
    // get competitor list remove not checked and Direct Hansokumake
    $stmt = $mysqli->prepare("SELECT 
            TournamentCompetitor.Id,
            TournamentCompetitor.ClubId
        FROM TournamentRegistration
        INNER JOIN TournamentCompetitor ON CompetitorId=TournamentCompetitor.Id
        WHERE WeightChecked=1 and TournamentCompetitor.Hansokumake<>1 and TournamentRegistration.CategoryId In (?,?)");
    $stmt->bind_param("ii", $tc_id_1, $tc_id_2);         
    $stmt->bind_result($cmpid,$clubId);     
    $stmt->execute();  
    $user=array();
    while($stmt->fetch()) {
      $user[$cmpid]=$clubId;
    }
    $stmt->close();
    
    // scramble it (scramble the club then user into the club so we avoid people in the same club to be in the same pool)
    $club_list = array_unique(array_values($user));
    $club_val=array();
    
   foreach($club_list as $club_id){
       $club_val[$club_id]=rand(1, count($club_list));
   }
   
   $new_usr=array();
   foreach($user as $uid=>$club_id){
        $new_usr[$uid]= $club_val[$club_id]*100 +rand(1,99);
   }
   
   asort($new_usr, SORT_NUMERIC);
   
   $usr_ids = array_keys($new_usr);
   switch (count($usr_ids)) {
    case 2:
        create_step_pool_2($actual_category_id, $usr_ids[0], $usr_ids[1], 'Finale');
        break;
    case 3:
        create_step_pool_3($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2],'Groupe A');
        break;
    case 4:
        create_step_pool_4($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],'Groupe A');
        break;
    case 5:
        create_step_pool_5($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],'Groupe A');
        break;
    case 6:
        create_steps_6($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],  $usr_ids[5]);
        break;
    case 7:
        create_steps_7($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],  $usr_ids[5],  $usr_ids[6]);
        break;
    case 8:
        create_steps_8($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],  $usr_ids[5],  $usr_ids[6],  $usr_ids[7]);
        break;
    case 9:
        create_steps_9($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],  $usr_ids[5],  $usr_ids[6],  $usr_ids[7],  $usr_ids[8]);
        break;
    case 10:
        create_steps_10($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],  $usr_ids[5],  $usr_ids[6],  $usr_ids[7],  $usr_ids[8],  $usr_ids[9]);
        break;
     //////   
    case 11:
        create_steps_11($actual_category_id, $usr_ids);
        break;
    case 12:
        create_steps_12($actual_category_id, $usr_ids);
        break;
    case 13:
        create_steps_13($actual_category_id, $usr_ids);
        break;
    case 14:
        create_steps_14($actual_category_id, $usr_ids);
        break;
    case 15:
        create_steps_15($actual_category_id, $usr_ids);
        break;
    case 16:
        create_steps_16($actual_category_id, $usr_ids);
        break;
    case 17:
        create_steps_17($actual_category_id, $usr_ids);
        break;
    case 18:
        create_steps_18($actual_category_id, $usr_ids);
        break;
    case 19:
        create_steps_19($actual_category_id, $usr_ids);
        break;
    case 20:
        create_steps_20($actual_category_id, $usr_ids);
        break;

    }
}

function cancel_fight($ActualCategoryId, $fight_Id){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    // check if linked step is sorter, if so the fighted has to be checked on subsequent
    // if so clear also the DATA in the step
    
     $stmt = $mysqli->prepare("select CategoryStep.Id from CategoryStep INNER JOIN StepLinking ON CategoryStep.Id=StepLinking.out_step_id INNER JOIN Fight FI ON in_step_1_id = FI.step_id OR in_step_2_id = FI.step_id where FI.Id=?  and CategoryStep.CategoryStepsTypeId=30 ");
    $stmt->bind_param("i", $fight_Id);  
    $stmt->bind_result($sorter_step_id);         
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    
    if (intval($sorter_step_id)>0){
        $stmt = $mysqli->prepare("update CategoryStep set Data=Null where Id=? ");
        $stmt->bind_param("i", $sorter_step_id);       
        $stmt->execute();
        $stmt->close();
    }
    
    
    ///////////////////
    
    
    $stmt = $mysqli->prepare("SELECT
                             
			       count(FI.Id)
			     FROM Fight FI
			     INNER JOIN StepLinking ON in_step_1_id = FI.step_id OR in_step_2_id = FI.step_id
			     INNER JOIN  Fight FD ON FD.step_id = out_step_id AND FD.pv1 IS NOT NULL
			     WHERE FI.Id=? ");
    $stmt->bind_param("i", $fight_Id);  
    $stmt->bind_result($already_fighted);         
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    if ($already_fighted==0) {
        // echo 'ACATID='.$ActualCategoryId;
         $stmt = $mysqli->prepare(" DELETE FROM ActualCategoryResult WHERE ActualCategoryId=?");
         $stmt->bind_param("i", $ActualCategoryId);  
	 $stmt->execute();
	 $stmt->close();
	 
	 $stmt = $mysqli->prepare("UPDATE ActualCategory SET IsCompleted=0 WHERE Id=?");
         $stmt->bind_param("i", $ActualCategoryId);  
	 $stmt->execute();
	 $stmt->close();
	 
	 $stmt = $mysqli->prepare("UPDATE Fight FD
	 INNER JOIN StepLinking ON FD.step_id = out_step_id 
	 INNER JOIN Fight FI ON (FI.step_id = in_step_1_id OR  FI.step_id = in_step_2_id) AND FI.Id=?
	 SET FD.TournamentCompetitor1Id=NULL, FD.pv1=NULL, FD.TournamentCompetitor2Id=NULL, FD.pv2=NULL, FD.forfeit1=NULL, FD.forfeit2=NULL,FD.noWinner=NULL");
         $stmt->bind_param("i", $fight_Id);  
	 $stmt->execute();
	 $stmt->close();
	 
	 
	 $stmt = $mysqli->prepare("UPDATE Fight SET pv1=NULL, pv2=NULL, forfeit1=NULL, forfeit2=NULL, noWinner=NULL WHERE Id=?");
         $stmt->bind_param("i", $fight_Id);  
	 $stmt->execute();
	 $stmt->close();
    }
    
}

function cancel_Category($ActualCategoryId, $force){
    $mysqli= ConnectionFactory::GetConnection(); 
    $stmt = $mysqli->prepare("SELECT COUNT(Fight.Id) FROM Fight WHERE pv1 IS NOT NULL AND ActualCategoryId=?");
    $stmt->bind_param("i", $ActualCategoryId);  
    $stmt->bind_result($already_fighted);         
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    if ($already_fighted==0 || $force) {
         $stmt = $mysqli->prepare(" DELETE FROM ActualCategoryResult WHERE ActualCategoryId=?");
         $stmt->bind_param("i", $ActualCategoryId);  
	 $stmt->execute();
	 $stmt->close();
	 
	 $stmt = $mysqli->prepare(" DELETE FROM Fight WHERE ActualCategoryId=?");
         $stmt->bind_param("i", $ActualCategoryId);  
	 $stmt->execute();
	 $stmt->close();
	 
	 $stmt = $mysqli->prepare(" DELETE FROM StepLinking WHERE ActualCategoryId=?");
         $stmt->bind_param("i", $ActualCategoryId);  
	 $stmt->execute();
	 $stmt->close();
	 
	 $stmt = $mysqli->prepare(" DELETE FROM CategoryStep WHERE ActualCategoryId=?");
         $stmt->bind_param("i", $ActualCategoryId);  
	 $stmt->execute();
	 $stmt->close();
	 
	 $stmt = $mysqli->prepare(" DELETE FROM ActualCategory WHERE Id=?");
         $stmt->bind_param("i", $ActualCategoryId);  
	 $stmt->execute();
	 $stmt->close();
    }
}


function close_category($ActualCategoryId){
    $mysqli= ConnectionFactory::GetConnection(); 
    $result = get_full_result($ActualCategoryId);
    
    if (! empty($result)){
    
        $skip_nominal=False;
    
        // Check for tie =1 in cĥecktie
        $stmt = $mysqli->prepare("SELECT Id FROM CategoryStep WHERE ActualCategoryId=?");
        $stmt->bind_param("i", $ActualCategoryId);  
        $stmt->bind_result($id_step);         
        $stmt->execute();
        $multiple=0;
        while ($stmt->fetch()){
            $multiple+=1;
        }
        $stmt->close();
        
        if ($multiple==1 && count($result)>2) {
            $s_res = get_step_results($id_step);
            
	    $skip_nominal=True;

	    $curr_score=-1;
	    $cur_rank=1;
	    $counter=0;
	    $palmares=array();
	    foreach($s_res['full'] as $comp_id => $score){
	       $counter+=1;
	       if ($curr_score!=$score){
	           $cur_rank=$counter;
	           $palmares[$cur_rank]=array();
	           $curr_score=$score;
	       }
	       if ($comp_id>0)	{   
	       	   array_push($palmares[$cur_rank],$comp_id);
	       }
		   
	    }
            
            $counter=0;
            foreach($palmares as $rank=>$clist){
                foreach($clist as $compid){
                    $medal=$rank;
                    if ($medal>3) { $medal=0;}
                    if ($medal==0 && $counter==3){$medal=3; } // in a pool the forth get also a bronz medal 
                    $stmt = $mysqli->prepare("INSERT INTO ActualCategoryResult (ActualCategoryId,Competitor1Id,RankId,Medal) VALUES (?,?,?,?)");
                    $stmt->bind_param("iiii", $ActualCategoryId, $compid, $rank, $medal);         
                    $stmt->execute();
                    $stmt->close();
                    $counter +=1;
                }
            }   
        } 
    
   
       if (!$skip_nominal) {
            $number=0;
            $Medal=1;
            foreach($result as $rank=>$cid){
                if (!is_array($cid)) {
                    $cid=array($cid);    
                }
                
                foreach($cid as $compid){
                    if ($compid>0){
                        $stmt = $mysqli->prepare("INSERT INTO ActualCategoryResult (ActualCategoryId,Competitor1Id,RankId,Medal) VALUES (?,?,?,?)");
                        $stmt->bind_param("iiii", $ActualCategoryId, $compid, $rank, $Medal);         
                        $stmt->execute();
                        $stmt->close();
                    $number+=1;
                    }
                }
                
                if($Medal>0) {$Medal+=1;} 
                if ($Medal==4){
                   $Medal=0;
                }
            }
       }
        
         $stmt = $mysqli->prepare("UPDATE ActualCategory SET IsCompleted=1 WHERE Id=?");
         $stmt->bind_param("i", $ActualCategoryId);         
         $stmt->execute();
         $stmt->close();
         
    } else {
       echo 'Cannot close a category which is not completed';
       exit;
    }
}

function order_fight($f_keys){
    $steps=array();
    foreach($f_keys as $k) {
        $sk = explode('-',$k)[0];
        if (!array_key_exists($sk, $steps)){
           $steps[$sk]=array(); 
        }
        $steps[$sk][count($steps[$sk])]=$k;
    }
    
    if (count($steps)==1) {
        return $f_keys;
    } else {
        $max =0;
        foreach ($steps as $s_id=>$s_fgt){
            if ($s_id>0) {
               $max=max($max, count($s_fgt));
            }
        }
        
        $new_list=array();
        
        for($idx=0;$idx<$max;$idx++){
            foreach ($steps as $s_id=>$s_fgt){
                if ($s_id>0) {
                    if ($idx<count($s_fgt)){
                         array_push($new_list,$s_fgt[$idx]);
                    }
                }
            }
        }
        
        foreach ($steps[0] as $fgt){
             array_push($new_list,$fgt);
        } 
        
       return  $new_list;  
        
   } 
}
        
  

?>
